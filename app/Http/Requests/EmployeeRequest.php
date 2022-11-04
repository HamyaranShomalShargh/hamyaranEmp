<?php

namespace App\Http\Requests;

use App\Rules\NationalCodeChecker;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "contract_subset_id" => "required",
            "first_name" => "required",
            "last_name" => "required",
            "gender" => "sometimes|nullable",
            "national_code" => ["required", new NationalCodeChecker(),'unique:employees,national_code,'.$this->route('Employee').",id"],
            "id_number" => "sometimes|nullable",
            "birth_date" => "sometimes|nullable|jdate:Y/m/d",
            "birth_city" => "sometimes|nullable",
            "education" => "sometimes|nullable",
            "marital_status" => "sometimes|nullable",
            "children_number" => "required",
            "insurance_number" => "sometimes|nullable",
            "insurance_days" => "sometimes|nullable",
            "military_status" => "sometimes|nullable",
            "basic_salary" => "required",
            "daily_wage" => "required",
            "worker_credit" => "required",
            "housing_credit" => "required",
            "child_credit" => "required",
            "job_group" => "required",
            "bank_name" => "sometimes|nullable",
            "bank_account" => "sometimes|nullable",
            "credit_card" => "sometimes|nullable",
            "sheba_number" => "sometimes|nullable",
            "phone" => "sometimes|nullable",
            "mobile" => "sometimes|nullable",
            "address" => "sometimes|nullable",
        ];
    }

    public function messages(): array
    {
        return [
            "contract_subset_id.required" => "انتخاب قرارداد الزامی می باشد",
            "first_name.required" => "درج نام الزامی می باشد",
            "last_name.required" => "درج نام خانوادگی الزامی می باشد",
            "national_code.required" => "درج کد ملی الزامی می باشد",
            "national_code.unique" => "کد ملی وارد شده تکراری می باشد",
            "birth_date.jdate" => "فرمت تاریخ تولد صحیح نمی باشد",
            "children_number.required" => "درج صفر و یا تعداد فرزندان مشمول الزامی می باشد",
            "basic_salary.required" => "درج صفر و یا حقوق پایه الزامی می باشد",
            "daily_wage.required" => "درج صفر و یا دستمزد الزامی می باشد",
            "worker_credit.required" => "درج صفر و یا بن ماهیانه الزامی می باشد",
            "housing_credit.required" => "درج صفر و یا کمک هزینه مسکن الزامی می باشد",
            "job_group.required" => "درج صفر و یا گروه شغلی الزامی می باشد",
        ];
    }
}
