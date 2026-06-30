<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Parsea listas de precios de proveedores.
 *
 * Formatos soportados:
 *  - 'dalsanto' : Excel de Dal Santo (hoja "STOCK GENERAL", datos desde fila 6). 1 precio.
 *  - 'nsm'      : PDF de NSM (por categorías). 1 precio.
 *  - 'vega'     : Excel de Vega (encabezado fila 1; A=código B=desc C=desc.adic D=precio). 1 precio (costo).
 *  - 'cairo'    : Excel de El Cairo (encabezado con "BICICLETERO"/"PUBLICO"; columnas autodetectadas).
 *                 2 precios: BICICLETERO=costo, PUBLICO=venta. Salta filas de sección.
 *
 * Cada item devuelto:
 *   ['codigo' => string, 'articulo' => string, 'precioI' => float, 'precioF' => float]
 * Los precios se devuelven SIN redondear (el importador redondea y, si corresponde,
 * multiplica por la cotización del dólar).
 */
class ListaPreciosParser
{
    public const HOJA_DAL_SANTO = 'STOCK GENERAL';

    /**
     * @return array<int, array{codigo:string, articulo:string, precioI:float, precioF:float}>
     */
    public function parse(string $rutaArchivo, string $extension, ?string $formato = null): array
    {
        // Si viene formato explícito (elegido en la UI), tiene prioridad.
        $formato = $formato ?: (strtolower($extension) === 'pdf' ? 'nsm' : 'dalsanto');

        return match ($formato) {
            'nsm'      => $this->parseNsm($rutaArchivo),
            'dalsanto' => $this->parseDalSanto($rutaArchivo),
            'vega'     => $this->parseVega($rutaArchivo),
            'cairo'    => $this->parseCairo($rutaArchivo),
            default    => throw new \InvalidArgumentException("Formato no soportado: {$formato}"),
        };
    }

    /** Dal Santo: B=codigo, C=detalle, D=precio. Datos desde fila 6. */
    public function parseDalSanto(string $rutaArchivo): array
    {
        $sheet = $this->cargarHoja($rutaArchivo, self::HOJA_DAL_SANTO);
        $items = [];

        foreach ($sheet->getRowIterator(6) as $row) {
            $c = $this->celdas($row, 'A', 'D');
            $codigo  = trim((string) ($c['B'] ?? ''));
            $detalle = trim((string) ($c['C'] ?? ''));
            $precio  = $this->normalizarPrecio($c['D'] ?? null);

            if ($codigo === '' || $detalle === '' || $precio <= 0) {
                continue;
            }
            $items[] = ['codigo' => $codigo, 'articulo' => $detalle, 'precioI' => $precio, 'precioF' => $precio];
        }

        return $items;
    }

    /** Vega: A=código, B=descripción, C=desc. adicional, D=precio (costo). Datos desde fila 2. */
    public function parseVega(string $rutaArchivo): array
    {
        $sheet = $this->cargarHoja($rutaArchivo);
        $items = [];

        foreach ($sheet->getRowIterator(2) as $row) {
            $c = $this->celdas($row, 'A', 'D');
            $codigo = trim((string) ($c['A'] ?? ''));
            $desc   = trim((string) ($c['B'] ?? ''));
            $adic   = trim((string) ($c['C'] ?? ''));
            $precio = $this->normalizarPrecio($c['D'] ?? null);

            if ($codigo === '' || $desc === '' || $precio <= 0) {
                continue;
            }
            $nombre = trim($desc . ($adic !== '' ? ' ' . $adic : ''));
            // 1 solo precio (costo). El precio de venta se calcula al activar (% del grupo).
            $items[] = ['codigo' => $codigo, 'articulo' => $nombre, 'precioI' => $precio, 'precioF' => $precio];
        }

        return $items;
    }

    /**
     * El Cairo: encabezado con columnas "BICICLETERO" (costo) y "PUBLICO" (venta), que según
     * el archivo están en distintas columnas. Se autodetecta el encabezado y las columnas.
     * Las filas de sección (ej "01 .0040. | ASIENTOS DE CROSS" sin precio) se saltan solo.
     */
    public function parseCairo(string $rutaArchivo): array
    {
        $sheet = $this->cargarHoja($rutaArchivo);
        $maxCol = $sheet->getHighestDataColumn();
        $maxRow = $sheet->getHighestDataRow();

        // 1) Buscar la fila de encabezado y las columnas de código / costo / público.
        $colCodigo = $colDesc = $colCosto = $colPublico = null;
        $filaDatos = null;

        for ($fila = 1; $fila <= min($maxRow, 15); $fila++) {
            $valores = $sheet->rangeToArray("A{$fila}:{$maxCol}{$fila}", null, false, false, true)[$fila] ?? [];
            foreach ($valores as $colLetra => $valor) {
                $txt = mb_strtoupper(trim((string) $valor));
                if ($txt === 'CÓDIGO' || $txt === 'CODIGO')        $colCodigo  = $colLetra;
                if ($txt === 'DESCRIPCIÓN' || $txt === 'DESCRIPCION') $colDesc  = $colLetra;
                if (str_contains($txt, 'BICICLETERO'))             $colCosto   = $colLetra;
                if (str_contains($txt, 'PUBLICO') || str_contains($txt, 'PÚBLICO')) $colPublico = $colLetra;
            }
            if ($colCodigo && $colCosto) {
                $filaDatos = $fila + 1;
                break;
            }
        }

        if (!$filaDatos) {
            return []; // no parece una lista de El Cairo
        }
        $colDesc    = $colDesc    ?? 'B';
        $colPublico = $colPublico ?? $colCosto; // si no hay público, usamos el costo

        $items = [];
        for ($fila = $filaDatos; $fila <= $maxRow; $fila++) {
            $valores = $sheet->rangeToArray("A{$fila}:{$maxCol}{$fila}", null, false, false, true)[$fila] ?? [];
            $codigo = trim((string) ($valores[$colCodigo] ?? ''));
            $desc   = trim((string) ($valores[$colDesc] ?? ''));
            $costo  = $this->normalizarPrecio($valores[$colCosto] ?? null);
            $public = $this->normalizarPrecio($valores[$colPublico] ?? null);

            // Filas de sección o vacías: sin costo numérico -> saltar.
            if ($codigo === '' || $desc === '' || $costo <= 0) {
                continue;
            }
            if ($public <= 0) {
                $public = $costo;
            }
            $items[] = ['codigo' => $codigo, 'articulo' => $desc, 'precioI' => $costo, 'precioF' => $public];
        }

        return $items;
    }

    // ── Helpers Excel ──────────────────────────────────────────────────

    private function cargarHoja(string $rutaArchivo, ?string $nombreHoja = null)
    {
        $reader = IOFactory::createReaderForFile($rutaArchivo);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($rutaArchivo);

        return ($nombreHoja ? $spreadsheet->getSheetByName($nombreHoja) : null)
            ?? $spreadsheet->getActiveSheet();
    }

    private function celdas($row, string $desde, string $hasta): array
    {
        $celdas = [];
        $it = $row->getCellIterator($desde, $hasta);
        $it->setIterateOnlyExistingCells(false);
        foreach ($it as $cell) {
            $celdas[$cell->getColumn()] = $cell->getValue();
        }
        return $celdas;
    }

    /** Convierte un precio (numérico o texto con formato argentino) a float. SIN redondear. */
    private function normalizarPrecio(mixed $valor): float
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        // Formato argentino "3.800,00" -> 3800.00
        $limpio = str_replace(['.', ' '], '', (string) $valor);
        $limpio = str_replace(',', '.', $limpio);
        $limpio = preg_replace('/[^0-9.\-]/', '', $limpio);

        return is_numeric($limpio) ? (float) $limpio : 0.0;
    }

    // ── NSM (PDF) ──────────────────────────────────────────────────────

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

            $empiezaCodigo = preg_match('/^\s*([A-Za-z]\s*\d|\d+\s+\d)/', $linea)
                && preg_match('/[A-Za-z]/', $linea);

            if ($empiezaCodigo) {
                $buffer = trim($linea);
            } elseif ($buffer !== '') {
                $buffer .= ' ' . trim($linea);
            } else {
                continue;
            }

            if (preg_match('/[\d.]+,\d{2}\s*\$/', $buffer)) {
                if ($item = $this->parsearFichaNsm($buffer)) {
                    $items[] = $item;
                }
                $buffer = '';
            }
        }

        return $items;
    }

    private function parsearFichaNsm(string $ficha): ?array
    {
        if (!preg_match('/^\s*([A-Za-z]?)\s*([\d\s]*\d)(.*)$/s', $ficha, $m)) {
            return null;
        }
        $codigo = strtoupper($m[1]) . preg_replace('/\s+/', '', $m[2]);
        $resto = $m[3];

        if (!preg_match('/([\d.]+,\d{2})\s*\$/', $resto, $mp)) {
            return null;
        }
        $precio = $this->normalizarPrecio($mp[1]);

        $medio = substr($resto, 0, strpos($resto, $mp[0]));
        $descripcion = preg_replace('/\s*\d+\s*$/', '', $medio);
        $descripcion = trim(preg_replace('/\s+/', ' ', $descripcion));

        if ($descripcion === '' || $precio <= 0) {
            return null;
        }

        return ['codigo' => $codigo, 'articulo' => $descripcion, 'precioI' => $precio, 'precioF' => $precio];
    }
}
