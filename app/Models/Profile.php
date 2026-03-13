<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'location',
        'bio',
        'languages',
        'phone_number',
        'address',
        'preferences',
    ];

    protected $casts = [
        'languages' => 'array',
        'preferences' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
