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
            'role' => $this->role,
            'is_active' => $this->is_active,
            'status' => $this->status,
            
            // Profile Information
            'profile' => [
                'avatar' => $this->avatar,
                'date_of_birth' => $this->date_of_birth,
                'age' => $this->age,
                'gender' => $this->gender,
            ],
            
            // Location Information
            'location' => [
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
                'full_address' => $this->full_address,
                'coordinates' => $this->when($this->hasCoordinates(), [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                ]),
            ],
            
            // Provider Information (only for providers)
            'provider_info' => $this->when($this->role === 'provider', [
                'provider_type' => $this->provider_type,
                'experience_years' => $this->experience_years,
                'hourly_rate' => $this->hourly_rate,
                'availability_status' => $this->availability_status,
                'rating' => $this->average_rating,
                'total_reviews' => $this->total_reviews,
                'total_bookings' => $this->total_bookings,
                'completed_bookings' => $this->completed_bookings,
                'completion_rate' => $this->completion_rate,
                'business_info' => [
                    'business_name' => $this->business_name,
                    'business_license' => $this->business_license,
                    'tax_id' => $this->tax_id,
                ],
            ]),
            
            // Verification Status
            'verification' => [
                'email_verified' => !is_null($this->email_verified_at),
                'phone_verified' => $this->isPhoneVerified(),
                'identity_verified' => $this->isIdentityVerified(),
                'background_check_status' => $this->background_check_status,
                'is_fully_verified' => $this->isFullyVerified(),
                'verified_at' => [
                    'email' => $this->email_verified_at,
                    'phone' => $this->phone_verified_at,
                    'identity' => $this->identity_verified_at,
                ],
            ],
            
            // Preferences
            'preferences' => $this->preferences,
            'notification_preferences' => $this->notification_preferences,
            
            // Social Login
            'social_accounts' => [
                'google_connected' => !is_null($this->google_id),
                'facebook_connected' => !is_null($this->facebook_id),
            ],
            
            // Roles and Permissions (from role management system)
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                        'assigned_at' => $role->pivot->assigned_at,
                    ];
                });
            }),
            
            'permissions' => $this->when($request->user()?->id === $this->id, function () {
                return $this->getPermissionNames();
            }),
            
            // Timestamps
            'last_login_at' => $this->last_login_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}