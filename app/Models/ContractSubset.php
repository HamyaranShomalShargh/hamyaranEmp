<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ContractSubset extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "contract_subsets";
    protected $fillable = ["name","contract_id","parent_id","user_id","workplace","inactive","files",
        "registration_start_day","registration_final_day","overtime_registration_limit","performance_attributes_id"
        ,"invoice_attributes_id","performance_flow_id","invoice_flow_id","invoice_cover_id"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class,"contract_id");
    }
    public function employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class,"contract_subset_id");
    }
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContractSubset::class,"parent_id");
    }
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ContractSubset::class,"parent_id");
    }
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class,"user_contract_subset","contract_subset_id","user_id");
    }
    public function performance_flow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AutomationFlow::class,"performance_flow_id");
    }
    public function invoice_flow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AutomationFlow::class,"invoice_flow_id");
    }
    public function performance_automation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PerformanceAutomation::class,"contract_subset_id");
    }
    public function performance_attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TableAttribute::class,"performance_attributes_id");
    }
    public function invoice_automation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceAutomation::class,"contract_subset_id");
    }
    public function invoice_attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TableAttribute::class,"invoice_attributes_id");
    }
    public function invoice_cover(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceCoverTitle::class,"invoice_cover_id");
    }
    public static function permitted_contracts(): \Illuminate\Database\Eloquent\Collection|array
    {
        $result = self::query()->with(["contract","employees"])->where("inactive","=",0)->whereHas("performance_flow")->whereHas("employees")
            ->whereHas("performance_attribute")->whereHas("invoice_attribute")->whereHas("users",function ($query){$query->whereIn("staff_id",[Auth::id()]);})->get();
        if($result->isNotEmpty())
            return $result;
        else
            return [];
    }
    public function entry_date_check(): array
    {
        if (intval(verta()->format("j")) >= $this->registration_start_day  && intval(verta()->format("j")) <= $this->registration_final_day) {
            $date = AutomationAuthorizedDate::query()->firstOrCreate(["automation_year" => verta()->format("Y"),"automation_month" => verta()->format("n")],["month_name" => verta()->format("F")]);
            return $date->toArray();
        }
        return [];
    }
    public function check_automation($date): \Illuminate\Database\Eloquent\Builder|Model|\Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->performance_automation()->whereHas("authorized_date",function ($query) use ($date){
            $query->where("automation_authorized_date.id","=",$date["id"]);
        })->first();
    }
    public function check_invoice_automation($year,$month): Model|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->invoice_automation()->whereHas("authorized_date",function ($query) use ($year,$month){
            $query->where("automation_authorized_date.automation_year","=",$year)->where("automation_authorized_date.automation_month","=",$month);
        })->first();
    }
}
