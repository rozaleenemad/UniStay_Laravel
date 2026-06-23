<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerDocument extends Model
{
    protected $fillable = ['owner_id', 'national_id_photo', 'property_contract_photo'];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
