<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create(['categoria'=>'Servicio']);
        Categoria::create(['categoria'=>'General']);
        Categoria::create(['categoria'=>'Accesorio']);
        Categoria::create(['categoria'=>'Repuesto']);
        Categoria::create(['categoria'=>'Camara']);
        Categoria::create(['categoria'=>'Cubierta']);
        Categoria::create(['categoria'=>'Rueda']);
        Categoria::create(['categoria'=>'Trasmicion']);
        Categoria::create(['categoria'=>'Formas']);
        Categoria::create(['categoria'=>'Indumentaria']);
        Categoria::create(['categoria'=>'iluminacion']);
        Categoria::create(['categoria'=>'Asientos']);
        Categoria::create(['categoria'=>'Parche/Soluion']);
        Categoria::create(['categoria'=>'Frenos']);
        Categoria::create(['categoria'=>'Orquilla']);
        Categoria::create(['categoria'=>'Movimientos']);
        Categoria::create(['categoria'=>'Prelota']);
        Categoria::create(['categoria'=>'Oferta']);


    }
}
