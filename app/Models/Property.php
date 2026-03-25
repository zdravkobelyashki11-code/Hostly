<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

// My code starts here
class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'host_id',
        'title',
        'description',
        'price_per_night',
        'location',
        'city',
        'country',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'is_active',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id')->withTrashed();
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Reviews for this property.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(PropertyReview::class);
    }

    /**
     * Get the property's average rating.
     */
    public function averageRating(): float
    {
        return (float) $this->reviews()->avg('rating');
    }
}
