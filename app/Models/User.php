<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'phone',
        'bio',
        'avatar',
        'public_slug',
        'asaas_id',
        'api_token',
        'password_reset_token',
        'last_login_at',
        'last_login_ip',
        'is_admin',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'donations_count',
        'donations_total',
        'avatar_url',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->public_slug) && ! empty($user->name)) {
                $user->public_slug = static::generateUniqueSlug($user->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (static::where('public_slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function generateToken()
    {
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'user_id', 'id');
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url('public/'.$this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=059669&color=fff';
    }

    public function getTotalRaisedAttribute()
    {
        return $this->campaigns()->withSum(['donations as raised' => function ($q) {
            $q->where('status', 3);
        }], 'amount')->get()->sum('raised');
    }

    public function setCPFAttribute($value)
    {
        $this->attributes['cpf'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
    }

    public function getCpfAttribute($value)
    {
        return substr($value, 0, 3).'.'.substr($value, 3, 3).'.'.substr($value, 6, 3).'-'.substr($value, 9, 2);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
    }

    public function getPhoneAttribute($value)
    {
        return substr($value, 0, 2).' '.substr($value, 2, 5).'-'.substr($value, 7, 4);
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
