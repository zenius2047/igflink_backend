<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketVendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_name',
        'vendor_name',
        'phone',
        'market_location',
        'community',
        'daily_fees_collected'
    ];

    protected $casts = [
        'daily_fees_collected' => 'decimal:2',
    ];

    //Market belongs to a district
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}