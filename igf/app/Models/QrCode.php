<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'district_id',
        'qr_code',
        'asset_type',  // 'business' or 'vehicle'
        'asset_id',    // ID of the linked asset
        'assigned_by',
        'assigned_at',
        'active',
    ];

    /**
     * QR code belongs to a district
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Officer who assigned the QR code
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the linked asset (vehicle or business)
     */
    public function asset()
    {
        if ($this->asset_type === 'business') {
            return $this->belongsTo(RegisteredBusiness::class, 'asset_id');
        }

        if ($this->asset_type === 'vehicle') {
            return $this->belongsTo(Vehicle::class, 'asset_id');
        }

        return null;
    }

    /**
     * Scope for active QR codes
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope for inactive QR codes
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }
}
