<?php

namespace App\Livewire\Articulo;

use App\Models\Categoria;
use App\Models\Grupos;
use App\Models\Proveedor;
use App\Models\Unidad;
use App\Services\ListaPreciosParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportarLista extends Component
{
    use WithFileUploads;

    /** Archivo subido por el usuario (.xlsx o .pdf). */
    public $archivo;

    /** Proveedor al que pertenece la lista. */
    public $proveedor_id;

    /** Grupo al que se asignan los artículos importados. */
    public $grupo_id;

    /** Formato de la lista: dalsanto | nsm | vega | cairo. */
    public $formato = '';

    /** Cotización del dólar (solo si la lista está en USD; vacío = lista en pesos). */
    public $cotizacion;

    /** Ruta (disco local) del archivo subido. */
    public $rutaArchivo;

    /** Ruta (disco local) del JSON con los items ya parseados. */
    public $rutaItems;

    /** Vista previa (primeras filas) de lo parseado. */
    public $preview = [];

    /** Total de artículos detectados en el archivo. */
    public $total = 0;

    /** Abreviatura del proveedor elegido (solo para mostrar). */
    public $abreviatura = null;

    /** Resultado de la importación. */
    public $resultado = null;

    public const PREVIEW_LIMIT = 50;

    public const FORMATOS = [
        'cairo'    => 'El Cairo (Excel: costo + público)',
        'vega'     => 'Vega (Excel: 1 precio/costo)',
        'dalsanto' => 'Dal Santo (Excel)',
        'nsm'      => 'NSM (PDF)',
    ];

    protected function rules(): array
    {
        return [
            'archivo'      => 'required|file|mimes:xlsx,xls,pdf|max:20480', // 20 MB
            'proveedor_id' => 'required|exists:proveedors,id',
            'grupo_id'     => 'required|exists:grupos,id',
            'formato'      => 'required|in:' . implode(',', array_keys(self::FORMATOS)),
            'cotizacion'   => 'nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'archivo.required'      => 'Debe seleccionar un archivo.',
        'archivo.mimes'         => 'El archivo debe ser Excel (.xlsx/.xls) o PDF.',
        'proveedor_id.required' => 'Debe seleccionar un proveedor.',
        'grupo_id.required'     => 'Debe seleccionar un grupo.',
        'formato.required'      => 'Debe seleccionar el formato de la lista.',
    ];

    /** Al cambiar de proveedor, reseteamos el grupo elegido. */
    public function updatedProveedorId(): void
    {
        $this->grupo_id = null;
    }

    /**
     * Lee el archivo, arma la vista previa y cachea lo parseado (sin tocar la base).
     */
    public function analizar(ListaPreciosParser $parser)
    {
        $this->validate();
        $this->resultado = null;

        @set_time_limit(0);

        $this->abreviatura = Proveedor::whereKey($this->proveedor_id)->value('abreviatura');

        $this->rutaArchivo = $this->archivo->store('listas-importar', 'local');
        $extension = pathinfo($this->rutaArchivo, PATHINFO_EXTENSION);

        $items = $parser->parse(Storage::disk('local')->path($this->rutaArchivo), $extension, $this->formato);

        $this->rutaItems = $this->rutaArchivo . '.items.json';
        Storage::disk('local')->put(
            $this->rutaItems,
            json_encode($items, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)
        );

        $this->total = count($items);
        $this->preview = array_slice($items, 0, self::PREVIEW_LIMIT);

        if ($this->total === 0) {
            $this->addError('archivo', 'No se detectaron artículos. Verifique que el archivo corresponda al formato elegido.');
            $this->limpiarArchivo();
        }
    }

    /**
     * Importa los artículos parseados: inactivos, asignados al grupo elegido.
     * Si hay cotización (>0), los precios se toman como dólares y se pasan a pesos.
     */
    public function confirmar(ListaPreciosParser $parser)
    {
        $this->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'grupo_id'     => 'required|exists:grupos,id',
            'rutaArchivo'  => 'required',
            'cotizacion'   => 'nullable|numeric|min:0',
        ]);

        @set_time_limit(0);

        $items = null;
        if ($this->rutaItems && Storage::disk('local')->exists($this->rutaItems)) {
            $items = json_decode(Storage::disk('local')->get($this->rutaItems), true);
        }
        if (!is_array($items)) {
            if (!Storage::disk('local')->exists($this->rutaArchivo)) {
                $this->addError('archivo', 'El archivo expiró. Vuelva a subirlo.');
                return;
            }
            $extension = pathinfo($this->rutaArchivo, PATHINFO_EXTENSION);
            $items = $parser->parse(Storage::disk('local')->path($this->rutaArchivo), $extension, $this->formato);
        }

        $factor = ($this->cotizacion && (float) $this->cotizacion > 0) ? (float) $this->cotizacion : 1.0;

        $categoriaId = Categoria::firstOrCreate(['categoria' => 'General'])->id;
        $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'Unidad'])->id;
        $abreviatura = Proveedor::whereKey($this->proveedor_id)->value('abreviatura');

        // Códigos ya cargados para este proveedor (dedup en una sola query).
        $existentes = DB::table('stocks')
            ->join('articulos', 'articulos.id', '=', 'stocks.articulo_id')
            ->where('stocks.proveedor_id', $this->proveedor_id)
            ->pluck('articulos.id', 'articulos.codigo')
            ->toArray();

        // Artículos ya vinculados a este grupo (para no duplicar el vínculo).
        $enGrupo = DB::table('grupos_articulos')
            ->where('grupo_id', $this->grupo_id)
            ->pluck('articulo_id')
            ->flip()
            ->toArray();

        $ahora = now();
        $creados = 0;
        $actualizados = 0;
        $stockRows = [];
        $histRows = [];
        $grupoRows = [];

        DB::transaction(function () use (
            $items, $categoriaId, $unidadId, $abreviatura, $ahora, $factor,
            &$existentes, &$enGrupo, &$creados, &$actualizados, &$stockRows, &$histRows, &$grupoRows
        ) {
            foreach ($items as $item) {
                $codigo  = (string) $item['codigo'];
                $precioI = (int) round(($item['precioI'] ?? $item['precio'] ?? 0) * $factor);
                $precioF = (int) round(($item['precioF'] ?? $item['precio'] ?? 0) * $factor);
                if ($precioF <= 0) $precioF = $precioI;

                if (isset($existentes[$codigo])) {
                    $artId = $existentes[$codigo];
                    DB::table('articulos')->where('id', $artId)->update([
                        'precioI' => $precioI, 'precioF' => $precioF, 'updated_at' => $ahora,
                    ]);
                    $histRows[] = [
                        'articulo_id' => $artId, 'precioIcial' => $precioI,
                        'precioFinal' => $precioF, 'created_at' => $ahora, 'updated_at' => $ahora,
                    ];
                    $actualizados++;
                } else {
                    $artId = DB::table('articulos')->insertGetId([
                        'articulo' => $item['articulo'], 'codigo' => $codigo, 'categoria_id' => $categoriaId,
                        'presentacion' => '-', 'unidad_id' => $unidadId, 'descuento' => 0,
                        'unidadVenta' => 'Unidad', 'precioF' => $precioF, 'precioI' => $precioI,
                        'caducidad' => 'No', 'detalles' => '-', 'suelto' => 0, 'activo' => 0,
                        'created_at' => $ahora, 'updated_at' => $ahora,
                    ]);
                    $existentes[$codigo] = $artId;
                    $stockRows[] = [
                        'articulo_id' => $artId, 'proveedor_id' => $this->proveedor_id,
                        'codigo_proveedor' => $abreviatura, 'stockMinimo' => 0, 'stock' => 0,
                        'created_at' => $ahora, 'updated_at' => $ahora,
                    ];
                    $histRows[] = [
                        'articulo_id' => $artId, 'precioIcial' => $precioI, 'precioFinal' => $precioF,
                        'created_at' => $ahora, 'updated_at' => $ahora,
                    ];
                    $creados++;
                }

                // Vincular al grupo si todavía no está.
                if (!isset($enGrupo[$artId])) {
                    $grupoRows[] = ['grupo_id' => $this->grupo_id, 'articulo_id' => $artId];
                    $enGrupo[$artId] = true;
                }
            }

            foreach (array_chunk($stockRows, 500) as $chunk) {
                DB::table('stocks')->insert($chunk);
            }
            foreach (array_chunk($histRows, 500) as $chunk) {
                DB::table('historias_precios')->insert($chunk);
            }
            foreach (array_chunk($grupoRows, 500) as $chunk) {
                DB::table('grupos_articulos')->insert($chunk);
            }

            // Guardamos la cotización usada (para el botón "recalcular" cuando cambie el dólar).
            if ($factor > 1.0) {
                Grupos::whereKey($this->grupo_id)->update(['cotizacion' => $factor]);
            }
        });

        $this->limpiarArchivo();
        $this->reset(['preview', 'total', 'abreviatura', 'cotizacion']);

        $this->resultado = [
            'creados'      => $creados,
            'actualizados' => $actualizados,
        ];

        session()->flash('message', "Importación completa: {$creados} nuevos, {$actualizados} actualizados.");
    }

    public function cancelar()
    {
        $this->limpiarArchivo();
        $this->reset(['preview', 'total', 'resultado', 'abreviatura']);
    }

    private function limpiarArchivo(): void
    {
        foreach ([$this->rutaArchivo, $this->rutaItems] as $ruta) {
            if ($ruta && Storage::disk('local')->exists($ruta)) {
                Storage::disk('local')->delete($ruta);
            }
        }
        $this->reset(['archivo', 'rutaArchivo', 'rutaItems']);
    }

    public function render()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $grupos = $this->proveedor_id
            ? Grupos::where('proveedor_id', $this->proveedor_id)->orderBy('NombreGrupo')->get()
            : collect();

        return view('livewire.articulo.importar-lista', [
            'proveedores' => $proveedores,
            'grupos'      => $grupos,
            'formatos'    => self::FORMATOS,
        ]);
    }
}
