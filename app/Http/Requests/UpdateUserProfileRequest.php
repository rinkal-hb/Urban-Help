<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;
        
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users')->ignore($userId)
            ],
            'date_of_birth' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:male,female,other,prefer_not_to_say',
            'address' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            
            // Provider specific fields
            'provider_type' => 'sometimes|string|max:100',
            'experience_years' => 'sometimes|integer|min:0|max:50',
            'hourly_rate' => 'sometimes|numeric|min:0|max:99999.99',
            'availability_status' => 'sometimes|in:available,busy,offline',
            'business_name' => 'sometimes|nullable|string|max:255',
            'business_license' => 'sometimes|nullable|string|max:100',
            'tax_id' => 'sometimes|nullable|string|max:50',
            
            // Preferences
            'preferences' => 'sometimes|array',
            'preferences.language' => 'sometimes|string|in:en,hi,bn,te,ta,mr,gu,kn,ml,or,pa,as',
            'preferences.currency' => 'sometimes|string|in:INR,USD,EUR',
            'preferences.timezone' => 'sometimes|string|max:50',
            
            'notification_preferences' => 'sometimes|array',
            'notification_preferences.email_notifications' => 'sometimes|boolean',
            'notification_preferences.sms_notifications' => 'sometimes|boolean',
            'notification_preferences.push_notifications' => 'sometimes|boolean',
            'notification_preferences.booking_updates' => 'sometimes|boolean',
            'notification_preferences.promotional_offers' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'experience_years.max' => 'Experience years cannot exceed 50.',
            'hourly_rate.max' => 'Hourly rate cannot exceed 99,999.99.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'date_of_birth' => 'date of birth',
            'postal_code' => 'postal code',
            'provider_type' => 'provider type',
            'experience_years' => 'years of experience',
            'hourly_rate' => 'hourly rate',
            'availability_status' => 'availability status',
            'business_name' => 'business name',
            'business_license' => 'business license',
            'tax_id' => 'tax ID',
        ];
    }
}