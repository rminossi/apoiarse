<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'phone',
        'asaas_id',
        'is_admin',
        'api_token',
        'password_reset_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'donations_count',
        'donations_total'
    ];

    public function generateToken() {
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }

    public function campaigns() {
        return $this->hasMany(Campaign::class, 'user_id', 'id');
    }

    public function setCPFAttribute($value)
    {
        $this->attributes['cpf'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
    }
    public function getCpfAttribute($value)
    {
        return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9, 2);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
    }

    public function getPhoneAttribute($value)
    {
        return substr($value, 0, 2) . ' ' . substr($value, 2, 5) . '-' . substr($value, 7, 4);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function getDonationsCountAttribute()
    {
        return $this->donations()->where('status', 3)->count();
    }

    public function getDonationsTotalAttribute()
    {
        return $this->donations()->where('status', 3)->sum('amount');
    }
}
