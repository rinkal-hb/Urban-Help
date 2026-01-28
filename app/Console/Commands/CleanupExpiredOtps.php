<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OtpService;

class CleanupExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTP verification records';

    protected OtpService $otpService;

    /**
     * Create a new command instance.
     */
    public function __construct(OtpService $otpService)
    {
        parent::__construct();
        $this->otpService = $otpService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cleaning up expired OTPs...');
        
        $this->otpService->cleanupExpiredOtps();
        
        $this->info('Expired OTPs cleaned up successfully.');
        
        return Command::SUCCESS;
    }
}