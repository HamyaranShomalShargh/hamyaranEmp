<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "contracts";
    protected $fillable = ["user_id","name","number","start_date","end_date","files","inactive"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function subsets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContractSubset::class,"contract_id");
    }
    public function subset_employees(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(ContractSubsetEmployee::class,ContractSubset::class,"contract_id","contract_subset_id");
    }
    public static function Output(): array
    {
        $contracts = self::query()->with("subsets.children")->get();
        $result = [];
        if ($contracts->isNotEmpty()) {
            foreach ($contracts as $contract) {
                $arr = [];
                foreach ($contract->subsets as $subset) {
                    if ($subset->children->isNotEmpty()) {
                        foreach ($subset->children as $child)
                            $arr[] = ["contract_subset" => $subset->name, "child_name" => $child->name ,"workplace" => $child->workplace, "id" => $child->id];
                    } elseif ($subset->parent_id == null)
                        $arr[] = ["contract_subset" => $subset->name, "child_name" => "" ,"workplace" => $subset->workplace, "id" => $subset->id];
                }
                $result[$contract->name]["name"] = $contract->name;
                $result[$contract->name]["data"] = $arr;
            }
        }
        return $result;
    }
}
