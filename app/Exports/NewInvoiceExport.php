<?php

namespace App\Exports;

use App\Models\ContractSubset;
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

class NewInvoiceExport extends StringValueBinder implements FromView,WithStyles,WithEvents,WithTitle,WithProperties
{
    private int $contract_subset_id;
    private array $columns;
    private array|null|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $contract;
    private int $highest_column;
    public function __construct($contract_subset_id,$invoice_automation_id = null)
    {
        $this->contract_subset_id = $contract_subset_id;
        if ($invoice_automation_id) {
            $this->contract = ContractSubset::query()->with(["contract",
                "invoice_automation.authorized_date",
                "invoice_automation.invoices.employee",
                "invoice_attribute.items"])->whereHas("invoice_automation", function ($query) use ($invoice_automation_id) {
                $query->where("invoice_automation.id", "=", $invoice_automation_id);
            })->findOrFail($this->contract_subset_id);
            $employee_invoices = $this->contract->invoice_automation->invoices->toArray();
            $this->contract->invoice_automation->invoices->map(function ($item) use ($employee_invoices) {
                $search_data = array_column($employee_invoices,"employee_id");
                $index = array_search($item->employee->id,$search_data);
                if (gettype($index) !== 'boolean') {
                    $item->employee["invoice_data"] = json_decode($employee_invoices[$index]["data"]);
                }
            });
        }
        else
            $this->contract = ContractSubset::query()->with(["performance_automation.authorized_date",
                "performance_automation.performances.employee",
                "invoice_attribute.items"])->findOrFail($this->contract_subset_id);
        $this->columns = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
            "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
            "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
            "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ"
        ];
        $this->highest_column = count($this->contract->performance_attribute->items) + 3;
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:'.$this->columns[$this->highest_column])->getFont()->setName('tahoma');
        $sheet->getStyle('A:'.$this->columns[$this->highest_column])->getFont()->setSize(9);
        $sheet->getStyle('A1:'.$this->columns[$this->highest_column].'1')->getFont()->setSize(9);
        $sheet->getStyle('A:'.$this->columns[$this->highest_column])->getAlignment()->setVertical('center');
        $sheet->getStyle('A:'.$this->columns[$this->highest_column])->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $index = 3;
        foreach ($this->contract->performance_attribute->items as $attribute){
            $sheet->getColumnDimension($this->columns[$index++])->setWidth(15);
        }
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
        return view('excel.new_invoice',["contract" => $this->contract]);
    }

    public function title(): string
    {
        return "صورت وضعیت جدید";
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
