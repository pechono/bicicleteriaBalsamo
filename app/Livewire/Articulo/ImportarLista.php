<?php

namespace App\Livewire\Articulo;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\HistoriasPrecio;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Unidad;
use App\Services\ListaPreciosParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportarLista extends Component
{
    use WithFileUploads;

    /** Archivo .xlsx subido por el usuario. */
    public $archivo;

    /** Proveedor al que pertenece la lista. */
    public $proveedor_id;

    /** Ruta (disco local) del archivo persistido para re-parsear al confirmar. */
    public $rutaArchivo;

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
     * Lee el archivo y arma la vista previa, sin tocar la base de datos.
     */
    public function analizar(ListaPreciosParser $parser)
    {
        $this->validate();
        $this->resultado = null;

        $this->abreviatura = Proveedor::whereKey($this->proveedor_id)->value('abreviatura');

        // Persistimos el archivo para poder re-parsearlo al confirmar.
        $this->rutaArchivo = $this->archivo->store('listas-importar', 'local');
        $extension = pathinfo($this->rutaArchivo, PATHINFO_EXTENSION);

        $items = $parser->parse(Storage::disk('local')->path($this->rutaArchivo), $extension);

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

        if (!Storage::disk('local')->exists($this->rutaArchivo)) {
            $this->addError('archivo', 'El archivo expiró. Vuelva a subirlo.');
            return;
        }

        @set_time_limit(0);

        $extension = pathinfo($this->rutaArchivo, PATHINFO_EXTENSION);
        $items = $parser->parse(Storage::disk('local')->path($this->rutaArchivo), $extension);

        $categoriaId = Categoria::firstOrCreate(['categoria' => 'General'])->id;
        $unidadId = Unidad::query()->value('id') ?? Unidad::create(['unidad' => 'Unidad'])->id;
        // En este sistema stocks.codigo_proveedor guarda la abreviatura del proveedor;
        // el código del producto va en articulos.codigo. El QR NO se genera al importar.
        $abreviatura = Proveedor::whereKey($this->proveedor_id)->value('abreviatura');

        $creados = 0;
        $actualizados = 0;

        DB::transaction(function () use ($items, $categoriaId, $unidadId, $abreviatura, &$creados, &$actualizados) {
            foreach ($items as $item) {
                $precio = $item['precio'];

                // ¿Ya existe un artículo con ese código de producto para este proveedor?
                $stock = Stock::query()
                    ->join('articulos', 'articulos.id', '=', 'stocks.articulo_id')
                    ->where('stocks.proveedor_id', $this->proveedor_id)
                    ->where('articulos.codigo', $item['codigo'])
                    ->select('stocks.*')
                    ->first();

                if ($stock) {
                    // Solo actualizamos precios; no tocamos stock, nombre ni estado activo.
                    $articulo = Articulo::find($stock->articulo_id);
                    if ($articulo) {
                        $articulo->update([
                            'precioI' => $precio,
                            'precioF' => $precio,
                        ]);
                        HistoriasPrecio::create([
                            'articulo_id'  => $articulo->id,
                            'precioIcial'  => $precio,
                            'precioFinal'  => $precio,
                        ]);
                        $actualizados++;
                    }
                    continue;
                }

                // Artículo nuevo: inactivo, para que el usuario lo active manualmente.
                $articulo = Articulo::create([
                    'articulo'    => $item['articulo'],
                    'codigo'      => $item['codigo'],
                    'categoria_id' => $categoriaId,
                    'presentacion' => '-',
                    'unidad_id'   => $unidadId,
                    'descuento'   => 0,
                    'unidadVenta' => 'Unidad',
                    'precioF'     => $precio,
                    'precioI'     => $precio,
                    'caducidad'   => 'No',
                    'detalles'    => '-',
                    'suelto'      => 0,
                    'activo'      => 0,
                ]);

                Stock::create([
                    'articulo_id'      => $articulo->id,
                    'proveedor_id'     => $this->proveedor_id,
                    'codigo_proveedor' => $abreviatura,
                    'stockMinimo'      => 0,
                    'stock'            => 0,
                ]);

                HistoriasPrecio::create([
                    'articulo_id' => $articulo->id,
                    'precioIcial' => $precio,
                    'precioFinal' => $precio,
                ]);

                $creados++;
            }
        });

        // Limpieza.
        Storage::disk('local')->delete($this->rutaArchivo);
        $this->reset(['archivo', 'rutaArchivo', 'preview', 'total', 'abreviatura']);

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
        if ($this->rutaArchivo && Storage::disk('local')->exists($this->rutaArchivo)) {
            Storage::disk('local')->delete($this->rutaArchivo);
        }
        $this->reset(['archivo', 'rutaArchivo']);
    }

    public function render()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('livewire.articulo.importar-lista', compact('proveedores'));
    }
}
