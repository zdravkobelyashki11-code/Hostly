<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReview extends Model
{
    protected $fillable = [
        'booking_id',
        'reviewer_id',
        'rating',
        'sub_ratings',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'sub_ratings' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withTrashed();
    }
}
