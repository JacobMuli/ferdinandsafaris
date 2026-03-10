<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

use App\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'facebook_id',
        'avatar',
        'social_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function tourGuide()
    {
        return $this->hasOne(TourGuide::class);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->hasRole('super-admin') || $this->is_admin === true;
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin') || $this->is_super_admin === true;
    }
}