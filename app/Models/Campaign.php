<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'type',
        'category_id',
        'goal',
        'status',
        'user_id',
        'slug',
        'is_featured',
        'featured_order',
        'end_date',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'end_date' => 'date',
        'featured_order' => 'integer',
    ];

    protected $appends = [
        'cover',
        'missing_amount',
        'total_donations',
        'number_of_donations',
        'progress_percent',
        'supporters_count',
        'amount_raised_raw',
        'is_active',
        'days_remaining',
    ];

    public function images()
    {
        return $this->hasMany(ImageCampaign::class, 'campaign_id')->orderBy('cover', 'ASC');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function updates()
    {
        return $this->hasMany(CampaignUpdate::class)->orderByDesc('is_pinned')->orderByDesc('created_at');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 3);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('featured_order');
    }

    public function cover()
    {
        $cover = $this->images()->where('cover', 1)->first();

        if (empty($cover)) {
            if (empty($this->images()->first())) {
                return Storage::url('public/campaigns/imagem-indisponivel.jpg');
            }

            $path = Image::find($this->images()->first()->image_id)->path;

            return Storage::url('public/'.$path);
        }

        $path = Image::find($cover['image_id']);

        return Storage::url('public/'.$path->path);
    }

    public function getCoverAttribute()
    {
        return $this->cover();
    }

    public function setSlug()
    {
        if (! empty($this->title)) {
            $this->attributes['slug'] = Str::slug($this->title, '-').'-'.$this->id;
            $this->save();
        }
    }

    public function getGoalAttribute($value)
    {
        if (! empty($value)) {
            return number_format($value, '2', ',', '.');
        }
    }

    public function getGoalRawAttribute()
    {
        return $this->attributes['goal'] ?? 0;
    }

    public function getTotalDonationsAttribute()
    {
        $value = $this->donations()->where('status', 3)->sum('amount');

        return number_format($value, '2', ',', '.');
    }

    public function getAmountRaisedRawAttribute()
    {
        return (float) $this->donations()->where('status', 3)->sum('amount');
    }

    public function getNumberOfDonationsAttribute()
    {
        return $this->donations()->where('status', 3)->count();
    }

    public function getSupportersCountAttribute()
    {
        return $this->donations()->where('status', 3)->distinct('user_id')->count('user_id');
    }

    public function getProgressPercentAttribute()
    {
        $goal = (float) ($this->attributes['goal'] ?? 0);
        if ($goal <= 0) {
            return 0;
        }

        return min(100, round(($this->amount_raised_raw / $goal) * 100, 1));
    }

    public function getIsActiveAttribute()
    {
        return (int) $this->status === 1;
    }

    public function getDaysRemainingAttribute()
    {
        if (! $this->end_date) {
            return null;
        }

        return max(0, now()->startOfDay()->diffInDays($this->end_date, false));
    }

    public function getMissingAmountAttribute()
    {
        if (! empty($this->goal)) {
            $goal = floatval($this->convertStringToDouble($this->goal));
            $total = floatval($this->convertStringToDouble($this->getTotalDonationsAttribute()));

            return number_format(floatval($goal - $total), '2', ',', '.');
        }

        return null;
    }

    private function convertStringToDate(?string $param)
    {
        if (empty($param)) {
            return null;
        }
        [$day, $month, $year] = explode('/', $param);

        return (new \DateTime($year.'-'.$month.'-'.$day))->format('Y-m-d');
    }

    private function convertStringToDouble(?string $param)
    {
        if (empty($param)) {
            return null;
        }

        return str_replace(',', '.', str_replace('.', '', $param));
    }
}
