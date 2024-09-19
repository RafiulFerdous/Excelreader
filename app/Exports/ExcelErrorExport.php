<?php

namespace App\Exports;

use App\Models\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;

class ExcelErrorExport implements FromCollection, WithHeadings, WithEvents
{
    use Exportable;

    protected $data;
    protected $errors;

    public function __construct($data, $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Gender', 'Date of Birth', 'Error'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach ($this->errors as $error) {
                    $sheet->setCellValue(
                        'F' . $error['row'],  // Column F for errors
                        $error['error']
                    );
                }
            },
        ];
    }
}
