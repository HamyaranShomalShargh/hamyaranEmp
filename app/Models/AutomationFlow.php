<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationFlow extends Model
{
    use HasFactory;
    protected $table = "automation_flow";
    protected $fillable = ["user_id","name","inactive"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AutomationFlowDetail::class,"automation_flow_id")->orderBy("priority");
    }
    public static function make_flow_list($id): array
    {
        $automation_flow = self::query()->with("details.role")->findOrFail($id);
        $result = [];
        foreach ($automation_flow->details as $detail){
            $tmp["name"] = $detail->role->name;
            $tmp["id"] = $detail->role_id;
            do
                $slug = rand(10,999);
            while(array_search($slug,array_column($result,"slug")));
            $tmp["slug"] = $slug;
            $tmp["main_role"] = $detail->is_main_role;
            $result[] = $tmp;
        }
        return $result;
    }
}
