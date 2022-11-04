<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationFlowDetail extends Model
{
    use HasFactory;
    protected $table = "automation_flow_details";
    protected $fillable = ["automation_flow_id","role_id","priority","is_main_role"];

    public function automation_flow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AutomationFlow::class,"automation_flow_id");
    }
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class,"role_id");
    }
}
