<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractSubsetEmployee extends Model
{
    use HasFactory;
    protected $table = "contract_subset_employees";
    protected $fillable = ["contract_subset_id","user_id","name","national_code","mobile","verify","verify_timestamp"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }

    public function contract_subset(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ContractSubset::class,"contract_subset_id");
    }

    public static function employee($national_code): Model|\Illuminate\Database\Eloquent\Builder|null
    {
        return self::query()->with("contract_subset",function($query){
            $query->where("inactive","=",0);
        })->where("national_code","=",$national_code)->first();
    }
}
