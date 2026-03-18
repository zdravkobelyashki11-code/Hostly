<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'property_id',
        'guest_id',
        'check_in',
        'check_out',
        'total_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'total_price' => 'decimal:2',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    /**
     * Property review left by the guest.
     */
    public function propertyReviewByGuest(): HasOne
    {
        // reviewer is guest, property is this booking's property
        return $this->hasOne(Review::class)
            ->where('reviewer_id', $this->guest_id)
            ->where('review_type', 'property');
    }

    /**
     * Host review left by the guest.
     */
    public function hostReviewByGuest(): HasOne
    {
        // reviewer is guest, and the target is the host
        return $this->hasOne(Review::class)
            ->where('reviewer_id', $this->guest_id)
            ->where('review_type', 'user');
    }

    /**
     * Review left by the host.
     */
    public function reviewByHost(): HasOne
    {
        // reviewer is host, property is this booking's property
        return $this->hasOne(Review::class)
            ->where('review_type', 'user')
            ->whereHas('reviewer', function ($query) {
            $query->whereIn('id', function ($subQuery) {
                $subQuery->select('host_id')
                         ->from('properties')
                         ->whereColumn('properties.id', 'reviews.property_id');
            });
        });
    }
}
