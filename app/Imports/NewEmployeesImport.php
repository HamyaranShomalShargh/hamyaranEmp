<?php

namespace App\Imports;

use App\Models\Employee;
use App\Rules\NationalCodeChecker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;

class NewEmployeesImport implements OnEachRow,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;

    private int $contract_subset_id;

    public function __construct($contract_subset_id)
    {
        $this->contract_subset_id = $contract_subset_id;
    }

    public function onRow(Row $row)
    {
        Employee::query()->create(
            [
                "contract_subset_id" => $this->contract_subset_id,
                "user_id" => Auth::id(),
                "first_name" => str_replace("ي", "ی", $row[0]),
                "last_name" => str_replace("ي", "ی", $row[1]),
                "gender" => $row[2],
                "national_code" => $row[3],
                "id_number" => $row[4],
                "birth_date" => $row[5],
                "birth_city" => $row[6],
                "education" => $row[7],
                "military_status" => $row[8],
                "marital_status" => $row[9],
                "children_number" => $row[10] != null ?: 0,
                "insurance_number" => $row[11],
                "insurance_days" => $row[12],
                "basic_salary" => $row[13] != null ?: 0.00,
                "daily_wage" => $row[14] != null ?: 0.00,
                "worker_credit" => $row[15] != null ?: 0.00,
                "housing_credit" => $row[16] != null ?: 0.00,
                "child_credit" => $row[17] != null ?: 0.00,
                "job_group" => $row[18] != null ?: 1,
                "bank_name" => $row[19],
                "bank_account" => $row[20],
                "credit_card" => $row[21],
                "sheba_number" => $row[22],
                "phone" => $row[23],
                "mobile" => $row[24],
                "address" => $row[25]
            ]
        );
    }

    #[ArrayShape(["3" => "array"])] public function rules(): array
    {
        return [
            "3" => [new NationalCodeChecker(),"unique:employees,national_code"]
        ];
    }

    #[ArrayShape(['3' => "string"])] public function customValidationAttributes(): array
    {
        return ['3' => 'national_code'];
    }

    #[ArrayShape(['3.unique' => "string"])] public function customValidationMessages(): array
    {
        return [
            '3.unique' => 'کد ملی تکراری می باشد',
        ];
    }

    public function prepareForValidation($data)
    {
        if (strlen($data[3]) == 8)
            $data[3] = "00" . $data[3];
        elseif (strlen($data[3]) == 9)
            $data[3] = "0" . $data[3];
        $data[2] = match($data[2]){
            'مرد' => 'm',
            'زن' => 'f',
            default => null
        };
        $data[8] = match($data[8]){
            'کارت پایان خدمت' => 'h',
            'معاف' => 'e',
            'بدون خدمت سربازی' => 'n',
            default => null
        };
        $data[9] = match($data[9]){
            'متاهل' => 'm',
            'مجرد' => 's',
            default => null
        };
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
}
