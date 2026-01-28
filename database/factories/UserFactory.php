<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'email_verified_at' => now(),
            'phone_verified_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => fake()->randomElement(['customer', 'provider', 'admin']),
            'status' => true,
            'is_active' => true,
            'date_of_birth' => fake()->optional(0.7)->dateTimeBetween('-60 years', '-18 years'),
            'gender' => fake()->optional(0.6)->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'address' => fake()->optional(0.8)->address(),
            'city' => fake()->optional(0.9)->city(),
            'state' => fake()->optional(0.9)->state(),
            'postal_code' => fake()->optional(0.8)->postcode(),
            'country' => 'India',
            'latitude' => fake()->optional(0.7)->latitude(8.0, 37.0), // India's approximate bounds
            'longitude' => fake()->optional(0.7)->longitude(68.0, 97.0),
            'rating' => fake()->randomFloat(2, 0, 5),
            'total_reviews' => fake()->numberBetween(0, 100),
            'total_bookings' => fake()->numberBetween(0, 50),
            'completed_bookings' => fake()->numberBetween(0, 45),
            'availability_status' => fake()->randomElement(['available', 'busy', 'offline']),
            'background_check_status' => fake()->randomElement(['pending', 'approved', 'rejected', 'not_required']),
            'notification_preferences' => [
                'email_notifications' => fake()->boolean(80),
                'sms_notifications' => fake()->boolean(60),
                'push_notifications' => fake()->boolean(90),
                'booking_updates' => fake()->boolean(95),
                'promotional_offers' => fake()->boolean(40),
            ],
            'preferences' => [
                'language' => fake()->randomElement(['en', 'hi', 'bn', 'te', 'ta']),
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
            ],
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'phone_verified_at' => null,
        ]);
    }

    /**
     * Create a customer user.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'customer',
            'provider_type' => null,
            'experience_years' => null,
            'hourly_rate' => null,
            'business_name' => null,
            'business_license' => null,
            'tax_id' => null,
        ]);
    }

    /**
     * Create a service provider user.
     */
    public function provider(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'provider',
            'provider_type' => fake()->randomElement(['cleaner', 'plumber', 'electrician', 'carpenter', 'painter', 'mechanic']),
            'experience_years' => fake()->numberBetween(1, 20),
            'hourly_rate' => fake()->randomFloat(2, 200, 2000),
            'business_name' => fake()->optional(0.3)->company(),
            'business_license' => fake()->optional(0.2)->regexify('[A-Z0-9]{10}'),
            'tax_id' => fake()->optional(0.4)->regexify('[A-Z]{5}[0-9]{4}[A-Z]{1}'),
            'identity_verified_at' => fake()->optional(0.6)->dateTimeBetween('-6 months', 'now'),
            'background_check_status' => fake()->randomElement(['approved', 'pending']),
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'identity_verified_at' => now(),
            'background_check_status' => 'approved',
            'is_active' => true,
            'provider_type' => null,
            'experience_years' => null,
            'hourly_rate' => null,
        ]);
    }

    /**
     * Create a verified user.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'identity_verified_at' => fake()->optional(0.8)->dateTimeBetween('-3 months', 'now'),
            'background_check_status' => fake()->randomElement(['approved', 'not_required']),
        ]);
    }

    /**
     * Create a user with location.
     */
    public function withLocation(): static
    {
        return $this->state(fn (array $attributes) => [
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'latitude' => fake()->latitude(8.0, 37.0),
            'longitude' => fake()->longitude(68.0, 97.0),
        ]);
    }

    /**
     * Create a highly rated provider.
     */
    public function highlyRated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->randomFloat(2, 4.0, 5.0),
            'total_reviews' => fake()->numberBetween(50, 200),
            'total_bookings' => fake()->numberBetween(100, 300),
            'completed_bookings' => fake()->numberBetween(95, 290),
        ]);
    }
}
