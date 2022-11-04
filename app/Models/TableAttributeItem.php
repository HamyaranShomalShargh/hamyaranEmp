<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableAttributeItem extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "table_attribute_items";
    protected $fillable = ["table_attribute_id","name","kind","category","is_operable","condition"];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TableAttribute::class,"table_attribute_id");
    }
}
