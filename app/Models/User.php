<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAddress;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    const SUPERADMIN = 1;
    const ACTIVE = 1;
    const IS_USE_MEDICAL_INSURANCE =1;
    
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'entity_type_id',
        'avatar',
        'is_medical_insurance',
        'medical_insurance_name',
        'medical_insurance_number',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'code',
        'remember_token',
    ];

    // protected $appends = ['avatar_url'];

    // public function getAvatarUrlAttribute($value)
    // {
    //     if(empty($this->avatar)) {
    //         return asset('/images/user-placeholder.png');
    //     }
    //     return asset('/storage/uploads/avatar/'.$this->avatar);
    // }

    public function AauthAcessToken(){
        return $this->hasMany(OauthAccessToken::class);
    }

    public function userAddress(){
       return $this->hasOne(UserAddress::class,'user_id','id')->select(['id','country_id','state_id','city_id','address','user_id'])->where('type',UserAddress::PROFILE_ADDRESS);
    }
    public function userDeliveryAddress(){
        return $this->hasOne(UserAddress::class,'user_id','id')->select(['id','country_id','state_id','city_id','address','user_id'])->where('type',UserAddress::DELIVERY_ADDRESS);
    }
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    //share link example 
    // public function getShareLiveStreamUrlAttribute(){
    //     $shareUrl = route('deeplink', ['stream_id' => $this->id]);
    //     return $shareUrl;
    // }
    protected $casts = [
        'is_medical_insurance' => 'boolean',
        
    ];
}
