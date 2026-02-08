<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    // My code starts here
    use HasFactory, Notifiable;

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

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}