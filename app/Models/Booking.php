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
        return $this->belongsTo(Property::class)->withTrashed();
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id')->withTrashed();
    }

    /**
     * Property review left by the guest.
     */
    public function propertyReviewByGuest(): HasOne
    {
        return $this->hasOne(PropertyReview::class)->where('reviewer_id', $this->guest_id);
    }

    /**
     * Host review left by the guest.
     */
    public function hostReviewByGuest(): HasOne
    {
        return $this->hasOne(UserReview::class)
            ->where('reviewer_id', $this->guest_id)
            ->where('reviewee_id', $this->property->host_id);
    }

    /**
     * Review left by the host.
     */
    public function reviewByHost(): HasOne
    {
        return $this->hasOne(UserReview::class)
            ->where('reviewer_id', $this->property->host_id)
            ->where('reviewee_id', $this->guest_id);
    }
}
