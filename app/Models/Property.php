<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'governorate',
        'location',
        'proximity',
        'bedrooms',
        'bathrooms',
        'floor',
        'price',
        'gender_type',
        'is_furnished',
        'utilities_included',
        'available_from',
        'images',
        'description',
        'status',
        'rented_at',
    ];

    protected $casts = [
        'images'             => 'array',
        'is_furnished'       => 'boolean',
        'utilities_included' => 'boolean',
        'available_from'     => 'date',
        'rented_at'          => 'datetime',
    ];

    public const PUBLIC_STATUSES = ['approved'];

    public const STATUSES = ['pending', 'approved', 'rejected', 'rented'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isRented(): bool
    {
        return $this->status === 'rented';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isAvailableForBooking(): bool
    {
        return $this->status === 'approved';
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where('status', 'approved');
    }
}
