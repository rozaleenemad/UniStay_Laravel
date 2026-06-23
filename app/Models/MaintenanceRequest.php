<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = ['booking_id', 'title', 'description', 'photo', 'status'];

    public function booking() {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
