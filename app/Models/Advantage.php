<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advantage extends Model
{
    use HasFactory;use SoftDeletes;
    protected $table = "advantages";
    protected $fillable = ["user_id","name","inactive","automation_flow_id"];

    public function attachments(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdvantageAttachment::class,"advantage_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function automation_flow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AutomationFlow::class,"automation_flow_id");
    }
    public static function make_items_list($id): array
    {
        $advantages = self::query()->with(["attachments"])->findOrFail($id);
        $result = [];
        $slug = rand(11,999);
        foreach (json_decode($advantages->attachments->texts,true) as $text){
            if (count($result) > 0){
                while (in_array($slug,array_column($result,"slug")))
                    $slug = rand(11,999);
            }
            $result[] = ["name" => $text, "slug" => $slug, "kind" => "text"];
        }
        foreach (json_decode($advantages->attachments->files,true) as $file){
            if (count($result) > 0){
                while (in_array($slug,array_column($result,"slug")))
                    $slug = rand(11,999);
            }
            $result[] = ["name" => $file, "slug" => $slug, "kind" => "file"];
        }
        return $result;
    }

}
