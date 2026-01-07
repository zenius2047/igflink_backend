<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegisteredVehicles extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'district_id',
        'owner_name',
        'registration_number',
        'vehicle_type',
        'phone',
        'email',
        'make',
        'model',
        'monthly_levy',
        'status',
        'compliance_status',
    ];

    // Registered Vehicle belongs to a district
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // Casts
    protected $casts = [
        'monthly_levy' => 'decimal:2',
    ];
    
    
}
