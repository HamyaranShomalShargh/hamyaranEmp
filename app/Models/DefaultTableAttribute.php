<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultTableAttribute extends Model
{
    use HasFactory;
    protected $table = "default_table_attributes";
    protected $fillable = ["name","user_id","type","category","kind"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public static function attributes($type): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::query()->where("type","=",$type)->get();
    }
}
