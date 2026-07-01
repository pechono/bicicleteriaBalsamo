<?php

namespace Database\Seeders;

use App\Models\Grupos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ArticuloGrupoMDO extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        DB::table('grupos_articulos')->truncate();
        DB::table('stocks')->truncate();
        DB::table('articulos')->truncate();
        DB::table('grupos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // id = 1 SIEMPRE "Mano de Obra" (la genérica, precio a definir en cada trabajo).
        $articulos = [
            ['codigo' => '1','articulo' => 'Mano de Obra',     'precioI' => 5000,  'precioF' => 5000],
            ['codigo' => '2','articulo' => 'Parchar',          'precioI' => 3000,  'precioF' => 3000],
            ['codigo' => '3','articulo' => 'Service General',  'precioI' => 30000, 'precioF' => 30000],
            ['codigo' => '4','articulo' => 'Centrado de Rueda','precioI' => 4000,  'precioF' => 4000],
            ['codigo' => '5','articulo' => 'Mini servis',      'precioI' => 15000, 'precioF' => 15000],
        ];

        foreach ($articulos as $a) {
            DB::table('articulos')->insert([
                'codigo' => $a['codigo'], 'articulo' => $a['articulo'], 'categoria_id' => 1,
                'presentacion' => ' - ', 'unidad_id' => 1, 'descuento' => '0', 'unidadVenta' => 'unidad',
                'precioI' => $a['precioI'], 'precioF' => $a['precioF'], 'caducidad' => 'No',
                'detalles' => ' - ', 'suelto' => false, 'activo' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        Grupos::create([
            'proveedor_id' => 2, 'NombreGrupo' => 'Mano de Obra', 'porsentaje' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Stock + vínculo al grupo "Mano de Obra" (grupo_id = 1 tras el truncate) para cada uno.
        foreach ($articulos as $i => $a) {
            $articuloId = $i + 1;
            DB::table('stocks')->insert([
                'articulo_id' => $articuloId, 'proveedor_id' => 2, 'codigo_proveedor' => 'MdO',
                'stockMinimo' => 1, 'stock' => 10000, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('grupos_articulos')->insert([
                'grupo_id' => 1, 'articulo_id' => $articuloId, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }



    }
}
