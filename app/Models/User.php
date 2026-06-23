<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;





class User extends Authenticatable
{
        use Impersonate;

    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'governorate',
        'location',
        'maintenance_type',
        'national_id',
        'id_card_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────────
    public function units()
    {
        return $this->hasMany(Unit::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    public function documents()
    {
        return $this->hasOne(OwnerDocument::class, 'owner_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id');
    }

    // ─── Role helpers ──────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isMaintenance(): bool
    {
        return $this->role === 'maintenance';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }


    public function canImpersonate()
    {
        return $this->role === 'admin';
    }


    public function canBeImpersonated()
    {
        return $this->role !== 'admin';
    }
}
