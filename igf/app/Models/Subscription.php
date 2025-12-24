<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'package_id',
        'payment_method',
        'reference_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['expires_at'];

public function getExpiresAtAttribute()
{
    if (! $this->created_at || ! $this->package) {
        return null;
    }

    $expiresAt = $this->created_at->copy()->addMonths($this->package->duration_months);

    // Difference in days (integer)
    $diffInDays = now()->diffInDays($expiresAt, false); // false: allows negative if expired

    return (int) $diffInDays; // cast to integer
}


    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function package()
    {
        return $this->belongsTo(Packages::class);
    }

    /**
     * FINAL source of truth for subscription validity
     */
    public function isActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! $this->package || ! $this->package->is_active) {
            return false;
        }

        return now()->lessThanOrEqualTo(
            $this->created_at->addMonths($this->package->duration_months)
        );
    }

    public function expiresAt()
    {
        if (! $this->created_at || ! $this->package) {
            return null;
        }

        return $this->created_at->copy()
            ->addMonths($this->package->duration_months);
    }

}
