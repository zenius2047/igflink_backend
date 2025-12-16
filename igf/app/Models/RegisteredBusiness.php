<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisteredBusiness extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'business_name',
        'owner_name',
        'phone',
        'location',
        'category',
        'qr_code_id',
    ];

    /**
     * Business belongs to a district
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Business has one QR code
     */
    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }
}

