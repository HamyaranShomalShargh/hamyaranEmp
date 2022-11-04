<?php

namespace App\Models;

use App\Notifications\NewAdvantageChange;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class AdvantageAutomation extends Model
{
    use HasFactory;
    protected $table = "advantage_automation";
    protected $fillable = ["role_id","user_id","employee_id","advantage_id","contract","texts","files","advantage_form","type","start_month","end_month","role_priority","is_committed","is_referred","is_read","is_finished"];
    protected $appends = ['details_url','disagree_url'];

    public function current_role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class,"role_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public function advantage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Advantage::class,"advantage_id");
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
        Notification::send($users,new $notification($message,$id));
    }
    public function automate($type){
        $flow = $this->advantage->automation_flow->details;
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
                    if(isset($next)) {
                        $data["users"] = User::NotifyUsers($this->role_id, $this->employee->contract_subset_id);
                        $data["data"]["message"] = "درخواست تغییرات مزایای ".$this->advantage->name." ".$this->employee->first_name." ".$this->employee->last_name." به صندوق اتوماسیون شما ارسال شد";
                        $data["data"]["id"] = $this->id;
                        $data["data"]["type"] = "advantage";
                        $data["data"]["action"] = route("AdvantageAutomation.details",$this->id);
                        return $data;
                    }
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
                    $data["users"] = User::NotifyUsers($this->role_id, $this->employee->contract_subset_id);
                    $data["data"]["message"] = "درخواست تغییرات مزایای ".$this->advantage->name." ".$this->employee->first_name." ".$this->employee->last_name." به صندوق اتوماسیون شما ارجاع شد";
                    $data["data"]["id"] = $this->id;
                    $data["data"]["type"] = "advantage";
                    $data["data"]["action"] = route("AdvantageAutomation.details",$this->id);
                    return $data;
                }
                break;
            }
        }
    }
    public function final_automation_role(){
        $flow = $this->advantage->automation_flow->details();
        return $flow->select(['role_id'])->where("priority","=",$flow->max("priority"))->first()->role_id;
    }
    public function getDetailsUrlAttribute(): string
    {
        return route("AdvantageAutomation.details",$this->id);
    }
    public function getDisagreeUrlAttribute(): string
    {
        return route("AdvantageAutomation.disagree",$this->id);
    }
}
