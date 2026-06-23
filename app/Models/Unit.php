<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['owner_id', 'title', 'description', 'price_per_month', 'address', 'latitude', 'longitude', 'status'];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images() {
        return $this->hasMany(UnitImage::class, 'unit_id');
    }
}
