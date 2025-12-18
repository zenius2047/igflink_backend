<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DistrictUser extends Pivot
{
    protected $table = 'district_user';
    protected $fillable = ['user_id', 'district_id'];
}
