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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'is_active' => $this->is_active,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'provider_type' => $this->provider_type,
            'experience_years' => $this->experience_years,
            'hourly_rate' => $this->hourly_rate,
            'business_name' => $this->business_name,
            'business_license' => $this->business_license,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,
            'identity_verified_at' => $this->identity_verified_at,
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                        'hierarchy_level' => $role->hierarchy_level
                    ];
                });
            }),
            'primary_role' => $this->whenLoaded('roles', function () {
                $primaryRole = $this->roles->sortByDesc('hierarchy_level')->first();
                return $primaryRole ? $primaryRole->name : null;
            }),
            'role_names' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('display_name')->implode(', ');
            })
        ];
    }
}