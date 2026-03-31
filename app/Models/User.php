<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    // My code starts here
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Every user belongs to one role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * A host can have many properties.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'host_id');
    }

    /**
     * A guest can have many bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    /**
     * A user has one profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Reviews given by this user.
     */
    public function givenReviews(): HasMany
    {
        return $this->hasMany(UserReview::class, 'reviewer_id');
    }

    /**
     * Reviews received by this user as either a host or a guest.
     */
    public function receivedReviews(): Collection
    {
        return $this->receivedReviewsQuery()
            ->with(['booking.property', 'reviewer'])
            ->get();
    }

    /**
     * Get the user's average rating from received reviews.
     */
    public function averageRating(): float
    {
        return (float) $this->receivedReviewsQuery()->avg('user_reviews.rating');
    }

    /**
     * Query reviews received by this user as either a host or a guest.
     */
    public function receivedReviewsQuery(): Builder
    {
        return UserReview::query()
            ->join('bookings', 'bookings.id', '=', 'user_reviews.booking_id')
            ->where(function (Builder $query): void {
                $query
                    ->where(function (Builder $query): void {
                        $query
                            ->where('bookings.host_id', $this->id)
                            ->whereColumn('user_reviews.reviewer_id', 'bookings.guest_id');
                    })
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('bookings.guest_id', $this->id)
                            ->whereColumn('user_reviews.reviewer_id', 'bookings.host_id');
                    });
            })
            ->select('user_reviews.*');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
