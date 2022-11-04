<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCoverTitleData extends Model
{
    use HasFactory;
    protected $table = "invoice_cover_titles_data";
    protected $fillable = ["invoice_automation_id","data"];

    public function automation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceAutomation::class,"invoice_automation_id");
    }
}
