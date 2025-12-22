<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'region',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_district', 'district_id', 'user_id');
    }

    public function registeredBusinesses()
    {
        return $this->hasMany(RegisteredBusiness::class);
    }   
}
