<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'old_price',
        'label',
        'features',
        'is_active',
        'sort_order',
        'access_tier_id'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'integer',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function accessTier()
    {
        return $this->belongsTo(AccessTier::class);
    }
}
