<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Proveedor::create([
            'nombre'=>'Sin Definir',
            'telefono'=>'----',
            'abreviatura'=>'SinDF',
            'rubro'=>'Sin Defenir',
            'direccion'=>'----',
            'localidad'=>'----',
            'mail'=>'correo@correo.com',
            'activo'=>'1'
        ]);
        Proveedor::create([
            'nombre'=>'Biciclateria Balsamo',
            'telefono'=>'----',
            'abreviatura'=>'MdO',
            'rubro'=>'Servicio',
            'direccion'=>'----',
            'localidad'=>'----',
            'mail'=>'correo@correo.com',
            'activo'=>'1'
        ]);
    }
}
