<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BicicletaCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // =========================
        // COLORES
        // =========================
        DB::table('colors')->insert([
            ['color' => 'Rojo', 'created_at' => $now, 'updated_at' => $now],
            ['color' => 'Azul', 'created_at' => $now, 'updated_at' => $now],
            ['color' => 'Negro', 'created_at' => $now, 'updated_at' => $now],
            ['color' => 'Blanco', 'created_at' => $now, 'updated_at' => $now],
            ['color' => 'Verde', 'created_at' => $now, 'updated_at' => $now],
            ['color' => 'Amarillo', 'created_at' => $now, 'updated_at' => $now],
            ['color' => 'Gris', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // =========================
        // TIPOS DE BICICLETAS
        // =========================
        DB::table('tipo_bikes')->insert([
            ['tipo' => 'MTB', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'Ruta', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'Urbana', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'Tipo Ingles', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'BMX', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'Dama', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'Eléctrica', 'created_at' => $now, 'updated_at' => $now],
            ['tipo' => 'Plegable', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // =========================
        // MARCAS
        // =========================
        DB::table('marcas')->insert([
            ['marca' => 'Vanzo', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Cool', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Siambretta', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Volpro', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Fire Bird', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'SLP', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Olmo', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Vairo', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Trinx', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Raleigh', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Gribom', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'Enrique', 'created_at' => $now, 'updated_at' => $now],
            ['marca' => 'El Cairo', 'created_at' => $now, 'updated_at' => $now],



            ]);

        // =========================
        // PROCESOS / PROCEDIMIENTOS
        // =========================
        DB::table('procesos')->insert([
            [
                'nombre' => 'Ajuste de frenos',
                'descripcion' => 'Regulación y centrado del sistema de frenos',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Servicio Generañ',
                'descripcion' => 'Reemplazo de cámara dañada',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Lubricación completa',
                'descripcion' => 'Lubricación de cadena, piñones y cambios',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Alineación de cambios',
                'descripcion' => 'Ajuste del sistema de cambios delantero y trasero',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Limpieza general',
                'descripcion' => 'Limpieza completa de la bicicleta',
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
