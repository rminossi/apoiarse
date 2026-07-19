<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'whatsapp_group',
        'bank',
        'type',
        'acc',
        'ag',
        'cpf',
        'fullName',
    ];

    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = preg_replace('/[^0-9]/', '', (string) $value);
    }

    public function getCpfAttribute($value)
    {
        if (strlen($value) !== 11) {
            return $value;
        }

        return substr($value, 0, 3).'.'.substr($value, 3, 3).'.'.substr($value, 6, 3).'-'.substr($value, 9, 2);
    }
}
