<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvantageAttachment extends Model
{
    use HasFactory;
    protected $table = "advantage_attachments";
    protected $fillable = ["advantage_id","texts","files","period"];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Advantage::class,"advantage_id");
    }
}
