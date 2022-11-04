<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceCoverTitle extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "invoice_cover_titles";
    protected $fillable = ["user_id","name","is_operable"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceCoverTitleItem::class,"invoice_cover_id");
    }
    public function contracts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContractSubset::class,"invoice_cover_id");
    }
    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceAutomation::class,"invoice_cover_title_id");
    }
    public static function make_items_list($id): array
    {
        $invoice_cover_titles = InvoiceCoverTitle::query()->with(["items" => function($query){
            $query->where("invoice_cover_titles_items.is_operable","=",1);
        }])->findOrFail($id);
        $result = [];
        $slug = rand(11,999);
        foreach ($invoice_cover_titles->items as $title){
            if (count($result) > 0){
                while (in_array($slug,array_column($result,"slug")))
                    $slug = rand(11,999);
            }
            $result[] = ["name" => $title->name, "slug" => $slug, "kind" => $title->kind];
        }
        return $result;
    }
}
