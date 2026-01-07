<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Property extends Model
{

    use HasFactory;
    //the fields that can be mass eyi ... assigned
    protected $fillable = [
        'address',
        'owner_name',
        'rate_amount',
        'owner_phone',
        'owner_email',
        'zone',
        'district',
        'community',
        'registration_number',
        'status',
        'due_date',
        'amount_paid'
    ];
}