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
            'formato'      => 'required|in:' . implode(',', array_keys(self::FORMATOS)),
            'cotizacion'   => 'nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'archivo.required'      => 'Debe seleccionar un archivo.',
        'archivo.mimes'         => 'El archivo debe ser Excel (.xlsx/.xls) o PDF.',
        'proveedor_id.required' => 'Debe seleccionar un proveedor.',
        'formato.required'      => 'Debe seleccionar el formato de la lista.',
    ];

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
     * Importa los artículos parseados al CATÁLOGO (tabla lista_articulos), sin tocar
     * `articulos`. Si hay cotización (>0), la lista se trata como dólares: se guarda
     * el USD original + la cotización y el precio en pesos (USD × cotización).
     */
    public function confirmar(ListaPreciosParser $parser)
    {
        $this->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
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

        $esUsd  = $this->cotizacion && (float) $this->cotizacion > 0;
        $factor = $esUsd ? (float) $this->cotizacion : 1.0;

        // Códigos ya existentes en el catálogo para este proveedor (para contar nuevos vs actualizados).
        $existentes = DB::table('lista_articulos')
            ->where('proveedor_id', $this->proveedor_id)
            ->pluck('codigo')
            ->flip()
            ->toArray();

        $ahora = now();
        $creados = 0;
        $actualizados = 0;
        $rows = [];

        foreach ($items as $item) {
            $codigo = (string) $item['codigo'];
            $rawI   = (float) ($item['precioI'] ?? $item['precio'] ?? 0);
            $rawF   = (float) ($item['precioF'] ?? $item['precio'] ?? 0);
            if ($rawF <= 0) $rawF = $rawI;

            isset($existentes[$codigo]) ? $actualizados++ : $creados++;
            $existentes[$codigo] = true; // evita doble conteo si se repite en el archivo

            $rows[] = [
                'proveedor_id'   => $this->proveedor_id,
                'codigo'         => $codigo,
                'articulo'       => $item['articulo'],
                'precio_costo'   => (int) round($rawI * $factor),
                'precio_publico' => (int) round($rawF * $factor),
                'moneda'         => $esUsd ? 'USD' : 'ARS',
                'costo_usd'      => $esUsd ? $rawI : null,
                'publico_usd'    => $esUsd ? $rawF : null,
                'cotizacion'     => $esUsd ? $factor : null,
                'created_at'     => $ahora,
                'updated_at'     => $ahora,
            ];
        }

        // Upsert por (proveedor_id, codigo): inserta nuevos, actualiza precios/nombre/grupo.
        // NO toca articulo_id (el vínculo con el artículo ya promovido se conserva).
        foreach (array_chunk($rows, 500) as $chunk) {
            \App\Models\ListaArticulo::upsert(
                $chunk,
                ['proveedor_id', 'codigo'],
                ['articulo', 'precio_costo', 'precio_publico', 'moneda', 'costo_usd', 'publico_usd', 'cotizacion', 'updated_at']
            );
        }

        $this->limpiarArchivo();
        $this->reset(['preview', 'total', 'abreviatura', 'cotizacion']);

        $this->resultado = [
            'creados'      => $creados,
            'actualizados' => $actualizados,
        ];

        session()->flash('message', "Catálogo actualizado: {$creados} nuevos, {$actualizados} actualizados.");
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
        return view('livewire.articulo.importar-lista', [
            'proveedores' => Proveedor::orderBy('nombre')->get(),
            'formatos'    => self::FORMATOS,
        ]);
    }
}
