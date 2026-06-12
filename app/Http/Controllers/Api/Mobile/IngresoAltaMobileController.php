<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Livewire\Traits\WithWhatsApp;
use App\Models\Articulo;
use App\Models\Bici;
use App\Models\Cliente;
use App\Models\Color;
use App\Models\IngresoBici;
use App\Models\Marca;
use App\Models\NroIngreso;
use App\Models\TipoBike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Alta de ingreso de bici desde la app — espeja Livewire\Service\IngresarBike e IngresoImp.
 */
class IngresoAltaMobileController extends Controller
{
    use WithWhatsApp;

    /**
     * GET /api/mobile/ingreso-bici/cliente?dni=
     * Busca el cliente por DNI exacto (igual que IngresarBike::buscarCliente).
     */
    public function buscarCliente(Request $request)
    {
        $request->validate(['dni' => 'required|string']);

        $cliente = Cliente::where('dni', $request->dni)->first();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado.'], 404);
        }

        return response()->json($cliente);
    }

    /**
     * POST /api/mobile/ingreso-bici/cliente
     * Alta de cliente (igual que IngresarBike::saveCliente).
     */
    public function crearCliente(Request $request)
    {
        $data = $request->validate([
            'apellido' => 'required|string|max:255',
            'nombre'   => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'dni'      => 'required|string|max:20|unique:clientes,dni',
        ]);
        $data['activo'] = 1;

        $cliente = Cliente::create($data);

        return response()->json(['message' => 'Cliente creado.', 'cliente' => $cliente], 201);
    }

    /**
     * GET /api/mobile/ingreso-bici/datos
     * Marcas, tipos y colores para los selectores.
     */
    public function datos()
    {
        return response()->json([
            'marcas'  => Marca::orderBy('marca')->select('id', 'marca')->get(),
            'tipos'   => TipoBike::orderBy('tipo')->select('id', 'tipo')->get(),
            'colores' => Color::orderBy('color')->select('id', 'color')->get(),
        ]);
    }

    /**
     * POST /api/mobile/ingreso-bici/marca | tipo | color
     * Alta al vuelo, igual que los modales de IngresarBike.
     */
    public function crearMarca(Request $request)
    {
        $request->validate(['marca' => 'required|string|max:100']);
        $marca = Marca::create(['marca' => $request->marca]);
        return response()->json($marca, 201);
    }

    public function crearTipo(Request $request)
    {
        $request->validate(['tipo' => 'required|string|max:100']);
        $tipo = TipoBike::create(['tipo' => $request->tipo]);
        return response()->json($tipo, 201);
    }

    public function crearColor(Request $request)
    {
        $request->validate(['color' => 'required|string|max:100']);
        $color = Color::create(['color' => $request->color]);
        return response()->json($color, 201);
    }

    /**
     * GET /api/mobile/ingreso-bici/procesos?tipo=servicios|articulos&q=
     * Servicios = artículos con categoria_id 1; artículos = el resto.
     * (igual que IngresarBike::cargarProcesos)
     */
    public function procesos(Request $request)
    {
        $tipo = $request->input('tipo', 'servicios');
        $q    = $request->input('q', '');

        $procesos = Articulo::where('activo', true)
            ->when($tipo === 'servicios',
                fn($query) => $query->where('categoria_id', 1),
                fn($query) => $query->where('categoria_id', '<>', 1)
            )
            ->when($q, fn($query) => $query->where('articulo', 'like', "%{$q}%"))
            ->orderBy('articulo')
            ->select('id', 'articulo', 'presentacion', 'precioF', 'categoria_id')
            ->limit(30)
            ->get();

        return response()->json($procesos);
    }

    /**
     * POST /api/mobile/ingreso-bici
     * Registra el ingreso completo (igual que IngresarBike::guardarIngreso).
     *
     * Body: { cliente_id, marca_id, tipo_id, colores: [ids], nota, procesos: [articulo_ids], fecha_retiro? }
     */
    public function guardarIngreso(Request $request)
    {
        $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'marca_id'     => 'required|exists:marcas,id',
            'tipo_id'      => 'required|exists:tipo_bikes,id',
            'colores'      => 'nullable|array',
            'colores.*'    => 'exists:colors,id',
            'nota'         => 'nullable|string|max:1000',
            'procesos'     => 'required|array|min:1',
            'procesos.*'   => 'exists:articulos,id',
            'fecha_retiro' => 'nullable|date',
        ]);

        // Colores concatenados "Rojo - Negro", igual que la web
        $colorStr = Color::whereIn('id', $request->input('colores', []))
            ->orderBy('color')->pluck('color')->implode(' - ');

        $resultado = DB::transaction(function () use ($request, $colorStr) {
            $bici = Bici::create([
                'cliente_id' => $request->cliente_id,
                'tipo_id'    => $request->tipo_id,
                'marca_id'   => $request->marca_id,
                'color'      => $colorStr,
                'detalles'   => '',
            ]);

            $nro = NroIngreso::create([
                'detalles'     => '-' . ($request->input('nota') ?? ''),
                'estado'       => 'Pendiente',
                'fecha_retiro' => $request->input('fecha_retiro'),
            ]);

            foreach ($request->input('procesos') as $articuloId) {
                IngresoBici::create([
                    'bici_id'     => $bici->id,
                    'nro_ingreso' => $nro->id,
                    'articulo_id' => $articuloId,
                ]);
            }

            return $nro;
        });

        return response()->json([
            'message'        => 'Ingreso guardado correctamente.',
            'nro_ingreso'    => $resultado->id,
            'token_mobile'   => $resultado->token_mobile,
            'comprobante_url' => $this->comprobanteUrl($resultado->id),
        ], 201);
    }

    /**
     * POST /api/mobile/ingreso-bici/{id}/notificar
     * Setea/actualiza la fecha de retiro y manda el WhatsApp institucional de ingreso
     * (igual que IngresoImp::imprimirComprobante + enviarWhatsAppIngreso).
     */
    public function notificar(Request $request, $id)
    {
        $nro = NroIngreso::findOrFail($id);

        $request->validate(['fecha_retiro' => 'nullable|date']);

        if ($request->filled('fecha_retiro')) {
            $nro->update(['fecha_retiro' => $request->fecha_retiro]);
        }

        $bicicleta = Bici::join('clientes', 'clientes.id', '=', 'bicis.cliente_id')
            ->join('marcas', 'marcas.id', '=', 'bicis.marca_id')
            ->join('tipo_bikes', 'tipo_bikes.id', '=', 'bicis.tipo_id')
            ->join('ingreso_bicis', 'ingreso_bicis.bici_id', '=', 'bicis.id')
            ->where('ingreso_bicis.nro_ingreso', $id)
            ->select(
                'clientes.nombre', 'clientes.telefono',
                'tipo_bikes.tipo', 'marcas.marca', 'bicis.color',
                'ingreso_bicis.nro_ingreso'
            )
            ->first();

        if (!$bicicleta || !$bicicleta->telefono) {
            return response()->json(['message' => 'El cliente no tiene teléfono registrado.'], 422);
        }

        // Mismo mensaje que IngresoImp::enviarWhatsAppIngreso
        $nombre        = $bicicleta->nombre;
        $nroFormateado = str_pad($bicicleta->nro_ingreso, 4, '0', STR_PAD_LEFT);
        $marca         = $bicicleta->marca ?? '';
        $color         = $bicicleta->color ?? '';
        $tipo          = $bicicleta->tipo ?? '';

        $encabezado = "🔧 *BICICLETERÍA BALSAMO* 🔧\n----------------------------\nHola {$nombre} 👋\nTu bicicleta ingresó al taller.\n\nN° de ingreso: *#{$nroFormateado}*\n🚲 Marca: {$marca}\n🎨 Color: {$color}\n📋 Tipo: {$tipo}";

        if ($nro->fecha_retiro) {
            $fecha = \Carbon\Carbon::parse($nro->fecha_retiro);
            \Carbon\Carbon::setLocale('es');
            $fechaFormateada = ucfirst($fecha->isoFormat('dddd D [de] MMMM [de] YYYY'));
            $mensaje = "{$encabezado}\n\n📅 Fecha estimada de retiro:\n    {$fechaFormateada}\n----------------------------\n¡Gracias por elegirnos! 🙏";
        } else {
            $mensaje = "{$encabezado}\n\nEn cuanto esté lista te avisamos.\n----------------------------\n¡Gracias por elegirnos! 🙏";
        }

        $this->sendWhatsAppMessage($bicicleta->telefono, $mensaje);

        return response()->json([
            'message'         => 'WhatsApp encolado.',
            'comprobante_url' => $this->comprobanteUrl($id),
        ]);
    }

    /**
     * URL pública con hash para ver el comprobante PDF del ingreso desde el celular
     * (mismo esquema que comprobante.mobile de ventas).
     */
    private function comprobanteUrl($nroIngreso): string
    {
        $hash = hash('sha256', 'ingreso' . $nroIngreso . config('app.key'));
        return url("/comprobante-ingreso/mobile/{$nroIngreso}/{$hash}");
    }
}
