<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Storage;

class ImageCampaign extends Pivot
{
    protected $fillable = [
        'image_id',
        'campaign_id',
        'cover',
        'path'
    ];

    public function getUrlAttribute()
    {
        return Storage::url('campaigns/'.$this->path);
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
