<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Parsea listas de precios de proveedores.
 *
 * Formatos soportados:
 *  - Excel de Dal Santo (hoja "STOCK GENERAL", encabezados en fila 5, datos desde fila 6).
 *  - PDF de NSM (por categorías; líneas "CÓDIGO descripción bulto precio$").
 *
 * Cada item devuelto tiene la forma:
 *   ['codigo' => string, 'articulo' => string, 'precio' => int]
 */
class ListaPreciosParser
{
    /** Nombre de la hoja a leer en el archivo de Dal Santo. */
    public const HOJA_DAL_SANTO = 'STOCK GENERAL';

    /**
     * Dispatcher por extensión: elige el parser según el tipo de archivo.
     *
     * @return array<int, array{codigo:string, articulo:string, precio:int}>
     */
    public function parse(string $rutaArchivo, string $extension): array
    {
        return match (strtolower($extension)) {
            'pdf'        => $this->parseNsm($rutaArchivo),
            'xlsx', 'xls' => $this->parseDalSanto($rutaArchivo),
            default      => throw new \InvalidArgumentException("Formato no soportado: .{$extension}"),
        };
    }

    /**
     * Parsea un .xlsx de Dal Santo y devuelve los artículos listos para importar.
     *
     * @param  string  $rutaArchivo  Ruta absoluta al archivo .xlsx
     * @return array<int, array{codigo:string, articulo:string, precio:int}>
     */
    public function parseDalSanto(string $rutaArchivo): array
    {
        $reader = IOFactory::createReaderForFile($rutaArchivo);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($rutaArchivo);

        // Buscamos la hoja por nombre; si no existe, usamos la activa.
        $sheet = $spreadsheet->getSheetByName(self::HOJA_DAL_SANTO)
            ?? $spreadsheet->getActiveSheet();

        $items = [];

        foreach ($sheet->getRowIterator(6) as $row) {
            $celdas = [];
            $cellIterator = $row->getCellIterator('A', 'D');
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $celdas[$cell->getColumn()] = $cell->getValue();
            }

            // B = codigo, C = detalle, D = precio
            $codigo  = trim((string) ($celdas['B'] ?? ''));
            $detalle = trim((string) ($celdas['C'] ?? ''));
            $precioRaw = $celdas['D'] ?? null;

            // Saltar filas vacías o sin datos esenciales.
            if ($codigo === '' || $detalle === '' || $precioRaw === null || $precioRaw === '') {
                continue;
            }

            $precio = $this->normalizarPrecio($precioRaw);
            if ($precio <= 0) {
                continue;
            }

            $items[] = [
                'codigo'   => $codigo,
                'articulo' => $detalle,
                'precio'   => $precio,
            ];
        }

        return $items;
    }

    /**
     * Convierte un precio (numérico o texto con formato argentino) a entero redondeado.
     */
    private function normalizarPrecio(mixed $valor): int
    {
        if (is_numeric($valor)) {
            return (int) round((float) $valor);
        }

        // Formato argentino "3.800,00" -> 3800.00
        $limpio = str_replace(['.', ' '], '', (string) $valor);
        $limpio = str_replace(',', '.', $limpio);
        $limpio = preg_replace('/[^0-9.\-]/', '', $limpio);

        return is_numeric($limpio) ? (int) round((float) $limpio) : 0;
    }

    /**
     * Parsea el PDF de NSM y devuelve los artículos.
     *
     * Formato por ficha: CÓDIGO (letra+dígitos) + descripción + bulto (entero) + precio "$".
     * Algunas fichas son multi-línea (el código queda solo y la descripción sigue debajo),
     * por eso se acumula en un buffer hasta encontrar el precio.
     *
     * @return array<int, array{codigo:string, articulo:string, precio:int}>
     */
    public function parseNsm(string $rutaArchivo): array
    {
        $pdf = (new PdfParser())->parseFile($rutaArchivo);

        $texto = '';
        foreach ($pdf->getPages() as $page) {
            $texto .= $page->getText() . "\n";
        }

        $items = [];
        $buffer = '';

        foreach (preg_split('/\r\n|\r|\n/', $texto) as $linea) {
            if (trim($linea) === '') {
                continue;
            }

            // Una ficha empieza con código al inicio de la línea. Dos formas:
            //  - prefijo letra: "W 55005", "S1461", "Z 5452"
            //  - prefijo dígito: "1  1000", "4  440" (dígito + ESPACIO + dígitos).
            // El espacio obligatorio en el caso numérico evita confundir con dimensiones
            // de continuación tipo "270x160mm 30 9.100,00$". Además, una ficha SIEMPRE trae
            // descripción (letras): así no confundimos una continuación "312  20 51.000,00$"
            // (solo números + precio) con un código nuevo.
            $empiezaCodigo = preg_match('/^\s*([A-Za-z]\s*\d|\d+\s+\d)/', $linea)
                && preg_match('/[A-Za-z]/', $linea);

            if ($empiezaCodigo) {
                $buffer = trim($linea);
            } elseif ($buffer !== '') {
                // Continuación de una ficha multi-línea.
                $buffer .= ' ' . trim($linea);
            } else {
                continue; // encabezado de categoría / columna / datos de la empresa
            }

            // Si el buffer ya tiene precio, la ficha está completa.
            if (preg_match('/[\d.]+,\d{2}\s*\$/', $buffer)) {
                if ($item = $this->parsearFichaNsm($buffer)) {
                    $items[] = $item;
                }
                $buffer = '';
            }
        }

        return $items;
    }

    /**
     * Convierte una ficha de texto de NSM en un item, o null si no es válida.
     *
     * @return array{codigo:string, articulo:string, precio:int}|null
     */
    private function parsearFichaNsm(string $ficha): ?array
    {
        // Código al inicio: prefijo opcional de letra + número (con posibles espacios internos
        // en el caso numérico). La descripción arranca con la primera letra después del número.
        if (!preg_match('/^\s*([A-Za-z]?)\s*([\d\s]*\d)(.*)$/s', $ficha, $m)) {
            return null;
        }
        $codigo = strtoupper($m[1]) . preg_replace('/\s+/', '', $m[2]);
        $resto = $m[3];

        // Precio: número con formato argentino (,NN) seguido de "$".
        if (!preg_match('/([\d.]+,\d{2})\s*\$/', $resto, $mp)) {
            return null;
        }
        $precio = $this->normalizarPrecio($mp[1]);

        // Lo que está entre el código y el precio = descripción + bulto.
        $medio = substr($resto, 0, strpos($resto, $mp[0]));

        // El bulto es el último entero; lo quitamos para quedarnos con la descripción.
        $descripcion = preg_replace('/\s*\d+\s*$/', '', $medio);
        $descripcion = trim(preg_replace('/\s+/', ' ', $descripcion));

        if ($descripcion === '' || $precio <= 0) {
            return null;
        }

        return [
            'codigo'   => $codigo,
            'articulo' => $descripcion,
            'precio'   => $precio,
        ];
    }
}
