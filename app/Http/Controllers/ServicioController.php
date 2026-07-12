<?php

namespace App\Http\Controllers;
use App\Livewire\Service\IngresarBike;
use App\Models\IngresoBici;
use App\Models\Bici;

use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function comprobante($nro_ingreso)
    {
        return view('service.reporteIngreso', compact('nro_ingreso'));
    }
}