<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceCoverTitleItem extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "invoice_cover_titles_items";
    protected $fillable = ["invoice_cover_id","name","kind","is_operable"];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceCoverTitle::class,"invoice_cover_id");
    }
}
