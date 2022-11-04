<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class SmsPasswordReset extends Model
{
    use HasFactory;
    protected $table = "sms_password_resets";
    protected $fillable = ["mobile","token","created_at"];
    protected $primaryKey = "token";

    #[ArrayShape(["result" => "string", "token" => "string"])] public static function send($mobile): array
    {
        do $token = Str::random(60); while (self::query()->where("token","=",$token)->count() > 0);
        $duplicate = self::query()->where("mobile","=",$mobile)->first();
        if ($duplicate){
            $created_at = new Carbon($duplicate->created_at);
            if ($created_at->diffInSeconds(Carbon::now()) < 600)
                return ["result" => "exist","token" => ""];
        }
        self::query()->updateOrInsert(["mobile" => $mobile],[
            "mobile" => $mobile,
            "token" => $token,
            "created_at" => Carbon::now()
        ]);
        return ["result" => "operated","token" => $token];
    }
}
