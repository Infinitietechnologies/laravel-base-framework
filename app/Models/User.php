<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     * name varchar(255)
     * mobile varchar(20) [unique]
     * email varchar(255) [unique]
     * password varchar(255)
     * referral_code varchar(32) [null]
     * friends_code varchar(32) [null]
     * reward_points decimal(10,2) [default: 0.00]
     * remember_token varchar(100)
     * status enum('active','inactive') [default: 'inactive']
     */
    protected $fillable = [
        'name', 'mobile', 'email', 'password', 'referral_code', 'friends_code', 'reward_points', 'status','profile_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }
}
