<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove duplicate phone column if it exists
            if (Schema::hasColumn('users', 'phone')) {
                // Check if there are duplicate phone columns
                $columns = Schema::getColumnListing('users');
                $phoneCount = array_count_values($columns)['phone'] ?? 0;
                
                if ($phoneCount > 1) {
                    // Drop the duplicate phone column added by this migration
                    $table->dropColumn('phone');
                }
            }
            
            // Add new authentication and security fields only if they don't exist
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('otp_expires_at');
            }
            
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            }
            
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('locked_until');
            }
            
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('is_active');
            }
            
            // Add profile fields for Urban Company style platform
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('preferences');
            }
            
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('avatar');
            }
            
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }
            
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('state');
            }
            
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->default('India')->after('postal_code');
            }
            
            if (!Schema::hasColumn('users', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('country');
            }
            
            if (!Schema::hasColumn('users', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            
            // Provider specific fields
            if (!Schema::hasColumn('users', 'provider_type')) {
                $table->string('provider_type')->nullable()->after('longitude')->comment('Type of service provider');
            }
            
            if (!Schema::hasColumn('users', 'experience_years')) {
                $table->integer('experience_years')->nullable()->after('provider_type');
            }
            
            if (!Schema::hasColumn('users', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->nullable()->after('experience_years');
            }
            
            if (!Schema::hasColumn('users', 'availability_status')) {
                $table->enum('availability_status', ['available', 'busy', 'offline'])->default('available')->after('hourly_rate');
            }
            
            if (!Schema::hasColumn('users', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0.00)->after('availability_status');
            }
            
            if (!Schema::hasColumn('users', 'total_reviews')) {
                $table->integer('total_reviews')->default(0)->after('rating');
            }
            
            if (!Schema::hasColumn('users', 'total_bookings')) {
                $table->integer('total_bookings')->default(0)->after('total_reviews');
            }
            
            if (!Schema::hasColumn('users', 'completed_bookings')) {
                $table->integer('completed_bookings')->default(0)->after('total_bookings');
            }
            
            // Verification fields
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('completed_bookings');
            }
            
            if (!Schema::hasColumn('users', 'identity_verified_at')) {
                $table->timestamp('identity_verified_at')->nullable()->after('phone_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'background_check_status')) {
                $table->enum('background_check_status', ['pending', 'approved', 'rejected', 'not_required'])->default('not_required')->after('identity_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'documents')) {
                $table->json('documents')->nullable()->after('background_check_status')->comment('Stored document URLs and verification status');
            }
            
            // Notification preferences
            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('documents');
            }
            
            // Social login fields
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('notification_preferences');
            }
            
            if (!Schema::hasColumn('users', 'facebook_id')) {
                $table->string('facebook_id')->nullable()->after('google_id');
            }
            
            // Business fields for providers
            if (!Schema::hasColumn('users', 'business_name')) {
                $table->string('business_name')->nullable()->after('facebook_id');
            }
            
            if (!Schema::hasColumn('users', 'business_license')) {
                $table->string('business_license')->nullable()->after('business_name');
            }
            
            if (!Schema::hasColumn('users', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('business_license');
            }
            
            // Add indexes for better performance
            $table->index(['email', 'is_active']);
            $table->index(['phone', 'phone_verified_at']);
            $table->index('locked_until');
            $table->index(['city', 'state']);
            $table->index(['latitude', 'longitude']);
            $table->index(['provider_type', 'availability_status']);
            $table->index('rating');
            $table->index(['role', 'is_active']);
            $table->index('background_check_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'last_login_ip',
                'failed_login_attempts',
                'locked_until',
                'is_active',
                'preferences',
                'avatar',
                'date_of_birth',
                'gender',
                'address',
                'city',
                'state',
                'postal_code',
                'country',
                'latitude',
                'longitude',
                'provider_type',
                'experience_years',
                'hourly_rate',
                'availability_status',
                'rating',
                'total_reviews',
                'total_bookings',
                'completed_bookings',
                'phone_verified_at',
                'identity_verified_at',
                'background_check_status',
                'documents',
                'notification_preferences',
                'google_id',
                'facebook_id',
                'business_name',
                'business_license',
                'tax_id'
            ]);
        });
    }
};