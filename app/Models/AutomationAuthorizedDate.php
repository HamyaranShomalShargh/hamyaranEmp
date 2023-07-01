<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationAuthorizedDate extends Model
{
    use HasFactory;
    protected $table = "automation_authorized_date";
    protected $fillable = ["automation_year","automation_month","month_name","created_at","updated_at"];
    protected $appends = ["date_string"];

    public function getDateStringAttribute(): string
    {
        $date = [$this->automation_year,$this->automation_month,1];
        return implode("-",Verta::jalaliToGregorian($date[0],$date[1],$date[2]));
    }
}
