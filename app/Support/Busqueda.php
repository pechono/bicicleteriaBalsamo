<?php

namespace App\Support;

class Busqueda
{
    /**
     * Aplica una búsqueda por palabras sueltas en cualquier orden (AND entre palabras,
     * OR entre campos). Ej: "piñon index" exige que el registro contenga "piñon" Y "index"
     * en cualquiera de los campos, sin importar el orden ni que estén consecutivas.
     *
     * Separa también por guion, así "DalS-11115bk" se parte en "DalS" + "11115bk" y, con
     * codigo_proveedor y codigo entre los campos, matchea abreviatura + código del artículo.
     *
     * @param  \Illuminate\Contracts\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $texto
     * @param  array<int,string>  $campos  columnas donde buscar (ej: ['articulos.articulo','articulos.codigo'])
     */
    public static function palabras($query, ?string $texto, array $campos)
    {
        foreach (array_filter(preg_split('/[\s\-]+/', trim((string) $texto))) as $palabra) {
            $query->where(function ($q) use ($palabra, $campos) {
                foreach (array_values($campos) as $i => $campo) {
                    $i === 0
                        ? $q->where($campo, 'like', '%'.$palabra.'%')
                        : $q->orWhere($campo, 'like', '%'.$palabra.'%');
                }
            });
        }

        return $query;
    }
}
