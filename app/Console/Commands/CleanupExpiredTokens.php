<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired personal access tokens';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cleaning up expired tokens...');
        
        $deletedCount = PersonalAccessToken::where('expires_at', '<', now())->delete();
        
        $this->info("Cleaned up {$deletedCount} expired tokens.");
        
        return Command::SUCCESS;
    }
}