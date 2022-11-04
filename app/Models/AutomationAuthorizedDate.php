<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationAuthorizedDate extends Model
{
    use HasFactory;
    protected $table = "automation_authorized_date";
    protected $fillable = ["automation_year","automation_month","month_name"];
}
