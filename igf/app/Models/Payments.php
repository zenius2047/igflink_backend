<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'transaction_id',
        'description',
        'payment_type',
        'status',
    ];

    //link back to the property 
    public function user()
    {
        return $this->belongsTo(Property::class);
    }
}
