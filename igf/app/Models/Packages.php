<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'features',
        'price',
        'duration_months',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

     public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
