<?php

namespace App\Imports;

use App\Models\ContractSubset;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeImport;

class InvoiceAttributesImport implements ToArray,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;

    private int $contract_subset_id;

    public function __construct($contract_subset_id)
    {
        $this->contract_subset_id = $contract_subset_id;
    }

    public function array(array $array)
    {
        $contract_subset = ContractSubset::query()->findOrFail($this->contract_subset_id);
        $data = [];
        foreach ($array as $item) {
            $function["title"] = $item[0];
            $function["type"] = $item[1];
            $advantage["title"] = $item[2];
            $advantage["type"] = $item[3];
            $deduction["title"] = $item[4];
            $deduction["type"] = $item[5];
            $data["function"][] = $function;
            $data["advantage"][] = $advantage;
            $data["deduction"][] = $deduction;
        }
        $contract_subset->update(["invoice_attributes" => json_encode($data,JSON_UNESCAPED_UNICODE)]);
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
}
