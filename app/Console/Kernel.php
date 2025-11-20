<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Setting;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CreateBackup::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Dynamic backup scheduling based on settings
        $schedule->call(function () {
            $autoBackupEnabled = Setting::where('key', 'backup.auto_backup_enabled')->first();
            $backupFrequency = Setting::where('key', 'backup.backup_frequency')->first();
            
            if ($autoBackupEnabled && $autoBackupEnabled->value && $backupFrequency && $backupFrequency->value) {
                $frequency = $backupFrequency->value;
                
                switch ($frequency) {
                    case 'daily':
                        \Artisan::call('backup:create', ['--cleanup' => true]);
                        break;
                    case 'weekly':
                        if (now()->dayOfWeek === 0) { // Sunday
                            \Artisan::call('backup:create', ['--cleanup' => true]);
                        }
                        break;
                    case 'monthly':
                        if (now()->day === 1) { // First day of month
                            \Artisan::call('backup:create', ['--cleanup' => true]);
                        }
                        break;
                }
            }
        })->daily()->at('02:00'); // Check daily at 2 AM
        
        // Alternative: Direct scheduling (uncomment if needed)
        // $schedule->command('backup:create --cleanup')->daily()->at('02:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}