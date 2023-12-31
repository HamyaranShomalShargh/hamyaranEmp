<?php

namespace App\Exports;

use App\Models\ContractSubset;
use App\Models\InvoiceAutomation;
use App\Models\PerformanceAutomation;
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
    private mixed $authorized_date_id;
    private mixed $automation_id;
    private mixed $automation;
    private string $type;
    private array $columns;
    private int $highest_column;
    public function __construct($contract_subset_id,$authorized_date_id = null,$automation_id = null)
    {
        $this->contract_subset_id = $contract_subset_id;
        $this->authorized_date_id = $authorized_date_id;
        $this->automation_id = $automation_id;
        if ($this->automation_id) {
            $this->automation = InvoiceAutomation::query()->with([
                "invoices.employee",
                "attributes.items" => function($query){
                    $query->orderBy("table_attribute_items.category");
                },
                "contract.invoice_cover.items",
                "cover",])->findOrFail($this->automation_id);
            $employee_invoices = $this->automation->invoices->toArray();
            $this->automation->invoices->map(function ($item) use ($employee_invoices) {
                $search_data = array_column($employee_invoices,"employee_id");
                $index = array_search($item->employee->id,$search_data);
                if (gettype($index) !== 'boolean') {
                    $item->employee["invoice_data"] = json_decode($employee_invoices[$index]["data"]);
                }
            });
            $this->type = "created";
        }
        else {
            $this->automation = PerformanceAutomation::query()->with(["contract.invoice_attribute.items" => function($query){
                $query->orderBy("table_attribute_items.category");
            }, "contract.invoice_cover.items", "performances.employee"])
                ->whereHas("authorized_date", function ($query) {
                    $query->where("automation_authorized_date.id", "=", $this->authorized_date_id);
                })->where("contract_subset_id", "=", $this->contract_subset_id)->first();
            $this->type = "new";
        }
        $this->columns = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
            "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
            "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
            "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ"
        ];
        $this->highest_column = count($this->automation->contract->invoice_attribute->items) + 3;
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
        foreach ($this->automation->contract->invoice_attribute->items as $attribute){
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
        return view('excel.new_invoice',["automation" => $this->automation,"type" => $this->type]);
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
