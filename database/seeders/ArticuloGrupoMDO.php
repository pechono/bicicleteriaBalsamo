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

        $articulos = [
            [
                'codigo' => '1','articulo' => 'Parchar','categoria_id' => 1,'presentacion' => ' - ', 'unidad_id' => 1,'descuento' => '0', 'unidadVenta' => 'unidad',
                'precioI' => 3000,'precioF' => 3000,'caducidad' => 'No', 'detalles' => ' - ', 'suelto' => false,'activo' => true,'created_at' => now(), 'updated_at' => now()
            ],
            [
                'codigo' => '2','articulo' => 'Service General','categoria_id' => 1,'presentacion' => ' - ', 'unidad_id' => 1,'descuento' => '0', 'unidadVenta' => 'unidad',
                'precioI' => 28000,'precioF' => 28000,'caducidad' => 'No', 'detalles' => ' - ', 'suelto' => false,'activo' => true,'created_at' => now(), 'updated_at' => now()
            ],
            [
                'codigo' => '3','articulo' => 'Centrado de Rueda','categoria_id' => 1,'presentacion' => ' - ', 'unidad_id' => 1,'descuento' => '0', 'unidadVenta' => 'unidad',
                'precioI' => 4000,'precioF' => 4000,'caducidad' => 'No', 'detalles' => ' - ', 'suelto' => false,'activo' => true,'created_at' => now(), 'updated_at' => now()
            ],[
                'codigo' => '4','articulo' => 'Mini servis','categoria_id' => 1,'presentacion' => ' - ', 'unidad_id' => 1,'descuento' => '0', 'unidadVenta' => 'unidad',
                'precioI' => 20000,'precioF' => 20000,'caducidad' => 'No', 'detalles' => ' - ', 'suelto' => false,'activo' => true,'created_at' => now(), 'updated_at' => now()
            ]
            ];
            foreach ($articulos as $articulo) {
              DB::table('articulos')->insert($articulo);
            }
             $stocks = [
            [
                'articulo_id' => 1, 'proveedor_id' => 2,'codigo_proveedor'=>'MdO','stockMinimo' => 1, 'stock' => 10000, 'created_at' => now(), 'updated_at' => now()
            ],
             [
                'articulo_id' => 2, 'proveedor_id' => 2,'codigo_proveedor'=>'MdO','stockMinimo' => 1, 'stock' => 10000, 'created_at' => now(), 'updated_at' => now()
            ], 
            [
                'articulo_id' => 3, 'proveedor_id' => 2,'codigo_proveedor'=>'MdO','stockMinimo' => 1, 'stock' => 10000, 'created_at' => now(), 'updated_at' => now()
            ], 
            [
                'articulo_id' => 4, 'proveedor_id' => 2,'codigo_proveedor'=>'MdO','stockMinimo' => 1, 'stock' => 10000, 'created_at' => now(), 'updated_at' => now()
            ], 
            [
                'articulo_id' => 5, 'proveedor_id' => 2,'codigo_proveedor'=>'MdO','stockMinimo' => 1, 'stock' => 10000, 'created_at' => now(), 'updated_at' => now()
            ], 
            [
                'articulo_id' => 6, 'proveedor_id' => 2,'codigo_proveedor'=>'MdO','stockMinimo' => 1, 'stock' => 10000, 'created_at' => now(), 'updated_at' => now()
            ]
            ];
             foreach ($stocks as $stock) {
            DB::table('stocks')->insert($stock);
            }

            Grupos::create([
                'proveedor_id' => 2,
                'NombreGrupo' => 'Mano de Obra',
                'porsentaje' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $gruposArticulos = [
            ['grupo_id' => 1, 'articulo_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['grupo_id' => 1, 'articulo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['grupo_id' => 1, 'articulo_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['grupo_id' => 1, 'articulo_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['grupo_id' => 1, 'articulo_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['grupo_id' => 1, 'articulo_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ];
             foreach ($gruposArticulos as $relacion) {
                DB::table('grupos_articulos')->insert($relacion);
             }



    }
}
