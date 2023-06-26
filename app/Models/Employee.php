<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Employee extends Model
{
    protected $table = "employees";
    protected $fillable = ["extra_work_limit","contract_subset_id","first_name","last_name","gender","national_code","id_number","birth_date","birth_city","education","marital_status","children_number","insurance_number","insurance_days","military_status","basic_salary","daily_wage","worker_credit","housing_credit","child_credit","job_group","bank_name","bank_account","credit_card","sheba_number","phone","mobile","address","unemployed","user_id"];
    protected $appends = ['edit_url'];
    use HasFactory;use softDeletes;

    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ContractSubset::class,"contract_subset_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function performances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Performance::class,"employee_id");
    }
    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class,"employee_id");
    }
    public function advantages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AdvantageAutomation::class,"employee_id");
    }
    public static function duplicates($national_code): bool
    {
        $result = self::query()->where("national_code","=",$national_code)->get();
        if($result->isNotEmpty())
            return true;
        return false;
    }
    public function getEditUrlAttribute(): string
    {
        return route("Employees.edit",$this->id);
    }
    public static function permitted_employees(): \Illuminate\Database\Eloquent\Collection|array
    {
        $permitted_contracts = Auth::user()->contracts()->pluck("contract_subsets.id");
        if ($permitted_contracts){
            return self::query()->with("contract")->where("unemployed","=",0)->whereIn("contract_subset_id",$permitted_contracts)->get();
        }
        else
            return [];
    }
}
