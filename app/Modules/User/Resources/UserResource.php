<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'tenantId'  => $this->tenant_id,
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'roles'     => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),
            'createdAt' => $this->created_at?->toIso8601String(),
        ];
    }
}
