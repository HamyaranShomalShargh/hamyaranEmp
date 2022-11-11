<?php

namespace App\Imports;

use App\Models\ContractSubset;
use App\Models\InvoiceAutomation;
use App\Models\PerformanceAutomation;
use App\Rules\NationalCodeChecker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class PreInvoiceImport implements ToArray,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;

    private int $contract_subset_id;
    private mixed $automation_id;
    private string $type;
    private mixed $automation;
    private array $result = [];

    public function __construct($contract_subset_id,$automation_id,$type)
    {
        $this->contract_subset_id = $contract_subset_id;
        $this->automation_id = $automation_id;
        $this->type = $type;
        switch ($this->type){
            case "new":{
                $this->automation = PerformanceAutomation::query()->with(["performances.employee","contract.invoice_attribute.items" => function($query){
                    $query->orderBy("table_attribute_items.category");
                },"authorized_date"])->findOrFail($this->automation_id);
                break;
            }
            case "created":{
                $this->automation = InvoiceAutomation::query()->with(["invoices.employee","attributes.items" => function($query){
                    $query->orderBy("table_attribute_items.category");
                },"authorized_date"])->findOrFail($this->automation_id);
                break;
            }
        }
    }

    #[ArrayShape(["2" => "array"])] public function rules(): array
    {
        return [
            "2" => [new NationalCodeChecker()]
        ];
    }

    #[ArrayShape(['2' => "string"])] public function customValidationAttributes(): array
    {
        return ['2' => 'national_code'];
    }

    public function prepareForValidation($data)
    {
        if (strlen($data[2]) == 8)
            $data[2] = "00" . $data[2];
        elseif (strlen($data[2]) == 9)
            $data[2] = "0" . $data[2];
        return $data;
    }

    #[ArrayShape([BeforeImport::class => "\Closure"])] public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $properties = $event->getDelegate()->getProperties();
                $worksheet = $event->reader->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                if (!Hash::check("hss_creator", $properties->getCreator()) || !Hash::check("hss_manager", $properties->getManager()) || !Hash::check("hss", $properties->getCompany()))
                    throw new \Exception("خطا در شناسایی فایل اکسل: لطفا اطلاعات پرسنل را فقط در فایل نمونه دریافت شده به صورت مقدار (paste as value) جایگذاری نموده و سپس اقدام به ارسال نمایید");
                elseif ($highestRow == 1 || $highestRow == 0)
                    throw new \Exception("فایل اکسل بارگذاری شده خالی می باشد");

            },
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function array(array $array)
    {
        $array = array_values($array);
        $automation_type = $this->type == "new" ? "performances" : "invoices";
        $this->automation->$automation_type->map(function ($item) use ($array) {
            $search_data = array_column($array, 2);
            $index = array_search($item->employee->national_code, $search_data);
            if (gettype($index) !== 'boolean') {
                unset($array[$index][0]);
                unset($array[$index][1]);
                unset($array[$index][2]);
                unset($array[$index][3]);
                $array[$index] = array_values($array[$index]);
                $item->employee["invoice_data"] = $array[$index];
            }
        });
    }
    public function getResult(): array
    {
        return $this->automation->toArray();
    }
}
