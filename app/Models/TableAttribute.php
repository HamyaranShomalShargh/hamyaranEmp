<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableAttribute extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "table_attributes";
    protected $fillable = ["user_id","name","is_operable","type"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TableAttributeItem::class,"table_attribute_id");
    }
    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceAutomation::class,"attribute_id");
    }
    public function performances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PerformanceAutomation::class,"attribute_id");
    }
    public static function make_items_list($id): array
    {
        $table_attribute = TableAttribute::query()->with(["items" => function($query){
            $query->where("table_attribute_items.is_operable","=",1);
        }])->findOrFail($id);
        $result = [];
        $slug = rand(11,999);
        foreach ($table_attribute->items as $attribute){
            if (count($result) > 0){
                while (in_array($slug,array_column($result,"slug")))
                    $slug = rand(11,999);
            }
            $result[] = ["name" => $attribute->name, "slug" => $slug, "kind" => $attribute->kind, "category" => $attribute->category];
        }
        return $result;
    }
}
