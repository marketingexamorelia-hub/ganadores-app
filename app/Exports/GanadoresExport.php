<?php

namespace App\Exports;

use App\Models\Ganador;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class GanadoresExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithCustomValueBinder
{
    protected $ganadores;

    public function __construct()
    {
        $this->ganadores = Ganador::query()->orderBy('id', 'asc')->get();
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value)) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        return $this->ganadores->map(function ($g) {
            $raw = $g->getRawOriginal('caza_premios') ?? $g->caza_premios;
            $esCaza = ($raw == 1 || $raw === true || $raw === '1' || strtolower((string)$raw) === 'true');

            return [
                'ID'             => $g->id,
                'Nombre'         => $g->nombre ?? '',
                'Edad'           => $g->edad ?? '',
                'WhatsApp'       => $g->whatsapp ?? '',
                'Facebook ID'    => $g->facebook_id ?? '',
                'Fecha Dinámica' => $g->fecha_dinamica ? date('d/m/Y', strtotime($g->fecha_dinamica)) : '',
                'Fecha Entrega'  => $g->fecha_entrega ? date('d/m/Y', strtotime($g->fecha_entrega)) : '',
                'Programa'       => $g->programa ?? '',
                'Premio'         => $g->premio ?? '',
                'Patrocinador'   => $g->patrocinador ?? '',
                'Caza Premios'   => $esCaza ? 'SI' : 'NO',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre del Ganador',
            'Edad',
            'WhatsApp',
            'Facebook ID',
            'Fecha Dinámica',
            'Fecha Entrega',
            'Programa',
            'Participando Por',
            'Patrocinador',
            'Caza Premios',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Estilo para los Encabezados (Fondo Café Warm #8D6E63 con letras blancas)
        $sheet->getStyle('A1:K1')->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FF8D6E63');

        $sheet->getStyle('A1:K1')->getFont()
              ->setBold(true)
              ->setColor(new Color(Color::COLOR_WHITE));

        // 2. Recorrer los registros para pintar dinámicamente cada fila
        foreach ($this->ganadores as $index => $g) {
            // Como el encabezado ocupa la fila 1, los datos empiezan en la fila 2 ($index + 2)
            $rowNumber = $index + 2;
            $colorHex = null;

            // Evaluamos las condiciones de los estados
            $rawCaza = $g->getRawOriginal('caza_premios') ?? $g->caza_premios;
            $esCaza = ($rawCaza == 1 || $rawCaza === true || $rawCaza === '1' || strtolower((string)$rawCaza) === 'true');

            if (!empty($g->fecha_entrega)) {
                $colorHex = 'DCF8C6'; // Verde claro (Entregado)
            }
            if ($esCaza) {
                $colorHex = 'F8D7DA'; // Rojo suave / Rosado (Caza Premios)
            }
            if (!empty($g->alerta)) {
                $colorHex = 'FFEA00'; // Amarillo fosforescente (Alerta)
            }

            // Si cumple con alguna condición, aplicamos el color a toda la fila (de la A a la K)
            if ($colorHex) {
                $sheet->getStyle("A{$rowNumber}:K{$rowNumber}")
                      ->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()
                      ->setARGB('FF' . $colorHex);
            }
        }

        return [];
    }
}