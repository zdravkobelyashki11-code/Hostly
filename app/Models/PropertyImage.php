<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PropertyImage extends Model
{
    // My code starts here
    protected $fillable = [
        'property_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    
      //Returns the public URL for the image.
     
    public function getDisplayUrlAttribute(): string
    {
        // Using asset() returns the correct URL even if I change the domain name
        return asset('storage/' . $this->image_path);
    }
}