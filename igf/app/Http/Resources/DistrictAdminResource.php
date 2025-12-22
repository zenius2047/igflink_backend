<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'full_name'  => $this->full_name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'staff_id'   => $this->staff_id,
            'department' => $this->department,
            'is_active'  => $this->is_active,
            'districts'  => // name of districts
                $this->districts->map(function ($district) {
                    return [
                        'id'   => $district->id,
                        'name' => $district->name,
                    ];
                }),
            'roles'      => // name of roles
                $this->roles->map(function ($role) {
                    return [
                        'id'   => $role->id,
                        'name' => $role->name,
                    ];
                }),
        ];
    }
}
