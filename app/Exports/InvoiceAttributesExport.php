<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceAttributesExport extends StringValueBinder implements FromView,WithStyles,WithEvents,WithTitle,WithProperties
{
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:C')->getFont()->setName('tahoma');
        $sheet->getStyle('A:C')->getFont()->setSize(9);
        $sheet->getStyle('A1:C1')->getFont()->setSize(9);
        $sheet->getStyle('A:C')->getAlignment()->setVertical('center');
        $sheet->getStyle('A:C')->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('F')->setWidth(30);
    }
    #[ArrayShape([AfterSheet::class => "\Closure"])] public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
    public function view(): View
    {
        return view('excel.invoice_attributes');
    }

    public function title(): string
    {
        return "لیست عناوین وضعیت";
    }

    #[ArrayShape(['creator' => "string", 'manager' => "string", 'company' => "string"])] public function properties(): array
    {
        return [
            'creator' => Hash::make("hss_creator"),
            'manager' => Hash::make("hss_manager"),
            'company' => Hash::make("hss"),
        ];
    }
}
