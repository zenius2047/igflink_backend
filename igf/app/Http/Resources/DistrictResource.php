<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            "id"        => $this->id,
            "name"      => $this->name,
            "code"      => $this->code,
            "email"     => $this->email,
            "phone"     => $this->phone,
            "address"   => $this->address,
            "region"    => $this->region,
            "is_active" => $this->is_active,
        ];
    }
}
