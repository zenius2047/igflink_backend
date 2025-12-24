<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'full_name' => $this->full_name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'role'      => $this->role,
            'department'=> $this->department,
            'staff_id'  => $this->staff_id,
            'is_active' => $this->is_active,
           'created_at' => \Carbon\Carbon::parse($this->created_at)->format('F Y'),
            'role'=>$this->roles->pluck('name'),
            'districts'=> $this->districts->pluck('name'),
        ];
    }
}
