<?php

namespace App\Models;

use App\Notifications\PasswordResetEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes, HasPushSubscriptions;
    protected $fillable = [
        'user_id',
        'name',
        'username',
        'password',
        'email',
        'mobile',
        'email_verified_at',
        'mobile_verified_at',
        'is_admin',
        'is_staff',
        'remember_token',
        'role_id',
        'sign',
        'sign_hash',
        'inactive',
        'last_activity',
        'last_ip_address'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetEmail($token));
    }

    public function hasPermission($action,$model): bool
    {
        return in_array("{$model}.{$action}",$this->role->menu_items()->pluck("role_menu.route")->toArray());
    }
    public static function UserType(): string
    {
        $type = User::query()->findOrFail(Auth::id())->only(["is_admin","is_staff"]);
        return match (implode("",$type)){
            "10" => "admin",
            "01" => "staff",
            default => "unknown"
        };
    }
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class,"role_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function contracts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ContractSubset::class,"user_contract_subset","staff_id","contract_subset_id");
    }
    public function log(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserLog::class,"user_id");
    }
    public static function NotifyUsers($role,$contract): \Illuminate\Database\Eloquent\Collection|array
    {
        return User::query()->whereHas("contracts",function ($query) use ($contract){
            $query->where("contract_subsets.id","=",$contract);})->whereHas("role",function ($query) use ($role){
                $query->where("roles.id","=",$role);
        })->get();
    }
}
