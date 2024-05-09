<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'original_name',
        'path'
    ];

    public function campaigns()
    {
        return $this->belongsToMany('App\Campaign')->using('App\ImageCampaign')->withTimestamps();
    }
}
