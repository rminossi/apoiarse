<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'whatsapp_group',
        'bank_1',
        'type_1',
        'acc_1',
        'ag_1',
        'cpf_1',
        'fullName_1',
        'bank_2',
        'type_2',
        'acc_2',
        'ag_2',
        'cpf_2',
        'fullName_2',
        'bank_3',
        'type_3',
        'acc_3',
        'ag_3',
        'cpf_3',
        'fullName_3',
    ];

    public function setCpf_1Attribute($value)
    {
        $this->attributes['cpf_1'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
    }
    public function getCpf_1Attribute($value)
    {
        return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9, 2);
    }
    public function setCpf_2Attribute($value)
    {
        $this->attributes['cpf_2'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
    }
    public function getCpf_2Attribute($value)
    {
        return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9, 2);
    }
    public function setCpf_3Attribute($value)
    {
        $this->attributes['cpf_3'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
    }
    public function getCpf_3Attribute($value)
    {
        return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9, 2);
    }
}
