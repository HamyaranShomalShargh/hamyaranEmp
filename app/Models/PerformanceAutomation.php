<?php

namespace App\Models;

use App\Events\PerformanceAutomationEvent;
use App\Notifications\NewPerformance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Notification;
use Psy\Util\Str;

class PerformanceAutomation extends Model
{
    use HasFactory;
    protected $table = "performance_automation";
    protected $fillable = ["authorized_date_id","role_id","user_id","contract_subset_id","role_priority","is_read","is_finished","is_committed","is_referred","attribute_id"];
    protected $appends = ['details_url','disagree_url'];
    public function authorized_date(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AutomationAuthorizedDate::class,"authorized_date_id");
    }
    public function current_role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class,"role_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ContractSubset::class,"contract_subset_id");
    }
    public function performances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Performance::class,"performance_automation_id");
    }
    public function signs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(AutomationSign::class,"signable");
    }
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(AutomationComment::class,"commentable");
    }
    public function send_push_notification($users,$message,$id,$notification){
        //Notification::send($users,new $notification($message,$id));
        broadcast(new PerformanceAutomationEvent($users));
    }
    public function attributes(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TableAttribute::class,"attribute_id");
    }
    public function automate($type){
        $contract_subset = ContractSubset::query()->with(["performance_flow.details"])->findOrFail($this->contract_subset_id);
        $flow = $contract_subset->performance_flow->details;
        switch ($type){
            case "forward":{
                $max_priority = $flow->max("priority");
                if ($max_priority > $this->role_priority)
                    $next = $flow->where("priority","=",++$this->role_priority)->first();
                $this->update([
                    "role_id" => isset($next) ? $next->role_id : $this->role_id,
                    "role_priority" => isset($next) ? $next->priority : $this->role_priority,
                    "is_committed" => 1,
                    "is_read" => isset($next) ? 0 : 1,
                    "is_referred" => 0,
                    "is_finished" => isset($next) ? 0 : 1
                ]);
                if(isset($next)) {
                    $data["users"] = User::NotifyUsers($this->role_id, $this->contract_subset_id);
                    $data["data"]["message"] = "کارکرد ماهانه ".$this->contract->name."(".$this->contract->workplace . ") در " . $this->authorized_date->month_name. " ماه سال " . $this->authorized_date->automation_year." به صندوق اتوماسیون شما ارسال شد";
                    $data["data"]["id"] = $this->id;
                    $data["data"]["type"] = "performance";
                    $data["data"]["action"] = route("PerformanceAutomation.details",$this->id);
                    return $data;
                }
                break;
            }
            case "backward":{
                $min_priority = $flow->min("priority");
                if ($min_priority < $this->role_priority)
                    $previous = $flow->where("priority","=",--$this->role_priority)->first();
                $this->update([
                    "role_id" => isset($previous) ? $previous->role_id : $this->role_id,
                    "role_priority" => isset($previous) ? $previous->priority : $this->role_priority,
                    "is_committed" => isset($previous) && $previous->priority == 1 ? 0 : 1,
                    "is_read" => isset($previous) ? 0 : 1,
                    "is_finished" => 0,
                    "is_referred" => 1
                ]);
                if(isset($previous)) {
                    $data["users"] = User::NotifyUsers($this->role_id, $this->contract_subset_id);
                    $data["data"]["message"] = "کارکرد ماهانه ".$this->contract->name."(".$this->contract->workplace . ") در " . $this->authorized_date->month_name. " ماه سال " . $this->authorized_date->automation_year." به صندوق اتوماسیون شما ارجاع شد";
                    $data["data"]["id"] = $this->id;
                    $data["data"]["type"] = "performance";
                    $data["data"]["action"] = route("PerformanceAutomation.details",$this->id);
                    return $data;
                }
                break;
            }
        }
    }
    public function final_automation_role(){
        $contract_subset = ContractSubset::query()->with("performance_flow.details")->findOrFail($this->contract_subset_id);
        $flow = $contract_subset->invoice_flow->details();
        return $flow->select(['role_id'])->where("priority","=" ,$flow->max("priority"))->first()->role_id;
    }
    public function getDetailsUrlAttribute(): string
    {
        return route("PerformanceAutomation.details",$this->id);
    }
    public function getDisagreeUrlAttribute(): string
    {
        return route("PerformanceAutomation.disagree",$this->id);
    }
    public static function DateValidation($year,$month,$contract_id): array
    {
        $automation = PerformanceAutomation::query()->with("current_role")->whereHas("authorized_date",function ($query) use ($year,$month,$contract_id){
            $query->where("automation_year","=",$year)->where("automation_month","=",$month);
        })->where("contract_subset_id","=",$contract_id)->first();
        if ($automation != null){
            if ($automation->is_finished)
                return ["result" => "ready"];
            else
                return ["result" => "not_finished","data" => $automation->toArray()];
        }
        else
            return ["result" => "empty"];
    }
}
