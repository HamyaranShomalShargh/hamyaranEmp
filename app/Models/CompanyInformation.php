<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInformation extends Model
{
    use HasFactory;
    protected $table = "company_information";
    protected $fillable = ["ceo_user_id","substitute_user_id","user_id","ceo_title","substitute_title","name","short_name","description","registration_number","national_id","website","address","phone","fax","app_version"];

    public function ceo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"ceo_user_id");
    }
    public function substitute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"substitute_user_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public static function app_version(){
        return self::query()->first()->value("app_version");
    }
}
