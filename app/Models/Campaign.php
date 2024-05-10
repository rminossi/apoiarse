<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'goal',
        'status',
        'user_id',
        'slug',
    ];

    public function images()
    {
        return $this->hasMany(ImageCampaign::class, 'campaign_id')->orderBy('cover', 'ASC');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function cover()
    {
        $cover = $this->images()->where('cover', 1)->first();

        if (empty($cover)) {
            $path = Image::find($this->images()->first()->image_id)->path;

            return Storage::url('public/' . $path);
        }

        $path = Image::find($cover['image_id']);
        return Storage::url('public/' . $path->path);
    }

    public function setSlug()
    {
        if (!empty($this->title)) {
            $this->attributes['slug'] = Str::slug($this->title, '-') . '-' . $this->id;
            $this->save();
        }
    }

    public function getGoalAttribute($value)
    {
        return number_format($value, '2', ',', '.');
    }

    private function convertStringToDate(?string $param)
    {
        if (empty($param)) {
            return null;
        }
        list($day, $month, $year) = explode('/', $param);
        return (new \DateTime($year . '-' . $month . '-' . $day))->format('Y-m-d');
    }

    private function convertStringToDouble(?string $param)
    {
        if (empty($param)) {
            return null;
        }
        return str_replace(',', '.', str_replace('.', '', $param));
    }
};
