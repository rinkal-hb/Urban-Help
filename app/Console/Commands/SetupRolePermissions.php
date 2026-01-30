<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class SetupRolePermissions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'setup:roles-permissions {--fresh : Fresh setup, will reset all roles and permissions}';

    /**
     * The console command description.
     */
    protected $description = 'Setup roles and permissions for Urban Company clone';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up roles and permissions...');

        if ($this->option('fresh')) {
            $this->warn('Fresh setup requested. This will reset all roles and permissions!');
            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Setup cancelled.');
                return;
            }

            // Truncate tables
            $this->info('Clearing existing data...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('role_permissions')->truncate();
            DB::table('user_roles')->truncate();
            DB::table('permissions')->truncate();
            DB::table('roles')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Run the seeders
        $this->info('Running role and permission seeders...');
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        $this->info('✓ Roles and permissions created');

        Artisan::call('db:seed', ['--class' => 'SuperAdminSeeder']);
        $this->info('✓ Super admin user created');

        // Display summary
        $this->displaySummary();

        $this->info('Setup completed successfully!');
    }

    /**
     * Display setup summary
     */
    protected function displaySummary()
    {
        $this->info("\n" . str_repeat('=', 50));
        $this->info('SETUP SUMMARY');
        $this->info(str_repeat('=', 50));

        // Roles summary
        $roles = Role::with('permissions')->get();
        $this->info("Roles created: " . $roles->count());
        
        foreach ($roles as $role) {
            $this->line("  • {$role->display_name} ({$role->name}) - Level {$role->hierarchy_level} - {$role->permissions->count()} permissions");
        }

        // Users summary
        $adminUsers = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['super_admin', 'admin']);
        })->get();

        $this->info("\nAdmin users:");
        foreach ($adminUsers as $user) {
            $roleNames = $user->roles->pluck('display_name')->join(', ');
            $this->line("  • {$user->name} ({$user->email}) - Roles: {$roleNames}");
        }

        $this->info("\nLogin credentials:");
        $this->line("  Email: superadmin@urbanhelp.com");
        $this->line("  Password: SuperAdmin@123");

        $this->info(str_repeat('=', 50));
    }
}