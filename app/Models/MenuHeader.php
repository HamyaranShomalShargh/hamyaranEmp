<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuHeader extends Model
{
    use HasFactory;
    use softDeletes;
    protected $table = "menu_headers";
    protected $fillable = ["user_id","name","short_name","slug","icon","staff_only","inactive","priority"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuItem::class,"menu_header_id")->orderBy("priority");
    }
}
