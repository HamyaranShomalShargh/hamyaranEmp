<?php

namespace App\Exports;

use App\Models\ContractSubset;
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
use mysql_xdevapi\Collection;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewPerformanceExport extends StringValueBinder implements FromView,WithStyles,WithEvents,WithTitle,WithProperties
{
    private int $contract_subset_id;
    private mixed $authorized_date_id;
    private array $columns;
    private array|null|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $contract;
    private int $highest_column;
    private string $extra;
    private mixed $automation = null;
    public function __construct($contract_subset_id,$authorized_date_id = null,$extra = false)
    {
        $this->authorized_date_id = $authorized_date_id;
        $this->contract_subset_id = $contract_subset_id;
        $this->extra = $extra;
        if ($this->authorized_date_id) {
            $this->contract = ContractSubset::query()->findOrFail($this->contract_subset_id);
            $this->automation = PerformanceAutomation::query()->with(["performances.employee","attributes.items"])->whereHas("authorized_date",function ($query){
                $query->where("authorized_date_id", "=", $this->authorized_date_id);
            })->where("contract_subset_id","=",$this->contract_subset_id)->first();
            $employee_performances = $this->automation->performances->toArray();
            $this->automation->performances->map(function ($item) use ($employee_performances) {
                $search_data = array_column($employee_performances,"employee_id");
                $index = array_search($item->employee->id,$search_data);
                if (gettype($index) !== 'boolean') {
                    $item->employee["performance_data"] = json_decode($employee_performances[$index]["data"]);
                }
            });
        }
        else
            $this->contract = ContractSubset::query()->with(["contract","employees","performance_attribute.items"])->findOrFail($this->contract_subset_id);
        $this->columns = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
            "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
            "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
            "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ"
        ];
        if ($extra)
            $this->highest_column = count($this->contract->performance_attribute->items) + 4;
        else
            $this->highest_column = count($this->contract->performance_attribute->items) + 2;
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
        return view('excel.new_performance',["contract" => $this->contract,"automation" => $this->automation, "extra" => $this->extra]);
    }

    public function title(): string
    {
        return "لیست پرسنل جدید";
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
