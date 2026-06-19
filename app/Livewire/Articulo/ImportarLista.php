<?php

namespace App\Livewire\Articulo;

use App\Models\Categoria;
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

    /** Ruta (disco local) del archivo subido. */
    public $rutaArchivo;

    /** Ruta (disco local) del JSON con los items ya parseados (evita re-parsear al confirmar). */
    public $rutaItems;

    /** Vista previa (primeras filas) de lo parseado. */
    public $preview = [];

    /** Total de artículos detectados en el archivo. */
    public $total = 0;

    /** Abreviatura del proveedor elegido (solo para mostrar, ej. "DalS"). */
    public $abreviatura = null;

    /** Resultado de la importación. */
    public $resultado = null;

    /** Cantidad de filas a mostrar en la vista previa. */
    public const PREVIEW_LIMIT = 50;

    protected function rules(): array
    {
        return [
            'archivo'      => 'required|file|mimes:xlsx,xls,pdf|max:20480', // 20 MB
            'proveedor_id' => 'required|exists:proveedors,id',
        ];
    }

    protected $messages = [
        'archivo.required'      => 'Debe seleccionar un archivo.',
        'archivo.mimes'         => 'El archivo debe ser Excel (.xlsx/.xls) o PDF.',
        'proveedor_id.required' => 'Debe seleccionar un proveedor.',
    ];

    /**
     * Lee el archivo, arma la vista previa y cachea lo parseado (sin tocar la base).
     */
    public function analizar(ListaPreciosParser $parser)
    {
        $this->validate();
        $this->resultado = null;

        // El parseo de PDF puede tardar; evitamos el límite de tiempo.
        @set_time_limit(0);

        $this->abreviatura = Proveedor::whereKey($this->proveedor_id)->value('abreviatura');

        // Persistimos el archivo y cacheamos los items para NO re-parsear al confirmar.
        $this->rutaArchivo = $this->archivo->store('listas-importar', 'local');
        $extension = pathinfo($this->rutaArchivo, PATHINFO_EXTENSION);

        $items = $parser->parse(Storage::disk('local')->path($this->rutaArchivo), $extension);

        $this->rutaItems = $this->rutaArchivo . '.items.json';
        Storage::disk('local')->put(
            $this->rutaItems,
            json_encode($items, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)
        );

        $this->total = count($items);
        $this->preview = array_slice($items, 0, self::PREVIEW_LIMIT);

        if ($this->total === 0) {
            $this->addError('archivo', 'No se detectaron artículos. Verifique que el archivo corresponda al proveedor (Excel de Dal Santo o PDF de NSM).');
            $this->limpiarArchivo();
        }
    }

    /**
     * Importa los artículos parseados a la base de datos.
     */
    public function confirmar(ListaPreciosParser $parser)
    {
        $this->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'rutaArchivo'  => 'required',
        ]);

        @set_time_limit(0);

        // Reutilizamos los items cacheados en analizar(); si faltan, re-parseamos.
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
            $items = $parser->parse(Storage::disk('local')->path($this->rutaArchivo), $extension);
        }

        $categoriaId = Categoria::firstOrCreate(['categoria' => 'General'])->id;
        $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'Unidad'])->id;
        // stocks.codigo_proveedor guarda la abreviatura; el código de producto va en articulos.codigo.
        $abreviatura = Proveedor::whereKey($this->proveedor_id)->value('abreviatura');

        // Pre-cargamos los códigos ya existentes para este proveedor en UNA sola query
        // (evita una consulta de dedup por cada fila).
        $existentes = DB::table('stocks')
            ->join('articulos', 'articulos.id', '=', 'stocks.articulo_id')
            ->where('stocks.proveedor_id', $this->proveedor_id)
            ->pluck('articulos.id', 'articulos.codigo')
            ->toArray(); // [codigo => articulo_id]

        $ahora = now();
        $creados = 0;
        $actualizados = 0;
        $stockRows = [];
        $histRows = [];

        DB::transaction(function () use (
            $items, $categoriaId, $unidadId, $abreviatura, $ahora,
            &$existentes, &$creados, &$actualizados, &$stockRows, &$histRows
        ) {
            foreach ($items as $item) {
                $codigo = (string) $item['codigo'];
                $precio = (int) round($item['precio']);

                if (isset($existentes[$codigo])) {
                    // Ya existe: solo actualizamos precios (no tocamos stock/nombre/activo).
                    DB::table('articulos')->where('id', $existentes[$codigo])->update([
                        'precioI' => $precio, 'precioF' => $precio, 'updated_at' => $ahora,
                    ]);
                    $histRows[] = [
                        'articulo_id' => $existentes[$codigo], 'precioIcial' => $precio,
                        'precioFinal' => $precio, 'created_at' => $ahora, 'updated_at' => $ahora,
                    ];
                    $actualizados++;
                    continue;
                }

                // Artículo nuevo: inactivo, para que el usuario lo active manualmente.
                $id = DB::table('articulos')->insertGetId([
                    'articulo' => $item['articulo'], 'codigo' => $codigo, 'categoria_id' => $categoriaId,
                    'presentacion' => '-', 'unidad_id' => $unidadId, 'descuento' => 0,
                    'unidadVenta' => 'Unidad', 'precioF' => $precio, 'precioI' => $precio,
                    'caducidad' => 'No', 'detalles' => '-', 'suelto' => 0, 'activo' => 0,
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
                $existentes[$codigo] = $id; // evita duplicar si el código se repite en el archivo
                $stockRows[] = [
                    'articulo_id' => $id, 'proveedor_id' => $this->proveedor_id,
                    'codigo_proveedor' => $abreviatura, 'stockMinimo' => 0, 'stock' => 0,
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ];
                $histRows[] = [
                    'articulo_id' => $id, 'precioIcial' => $precio, 'precioFinal' => $precio,
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ];
                $creados++;
            }

            // Inserts en lote (mucho más rápido que fila por fila).
            foreach (array_chunk($stockRows, 500) as $chunk) {
                DB::table('stocks')->insert($chunk);
            }
            foreach (array_chunk($histRows, 500) as $chunk) {
                DB::table('historias_precios')->insert($chunk);
            }
        });

        $this->limpiarArchivo();
        $this->reset(['preview', 'total', 'abreviatura']);

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
        return view('livewire.articulo.importar-lista', compact('proveedores'));
    }
}
