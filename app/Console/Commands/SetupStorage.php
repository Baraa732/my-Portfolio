<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SetupStorage extends Command
{
    protected $signature = 'storage:setup';
    protected $description = 'Setup storage directories and symbolic links for production';

    public function handle()
    {
        $this->info('Setting up storage...');

        // Create necessary directories
        $directories = [
            storage_path('app/public/projects'),
            storage_path('app/public/profile'),
            storage_path('app/public/cv'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("Created: {$directory}");
            }
        }

        // Create symbolic link
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (File::exists($link)) {
            if (is_link($link)) {
                $this->info('Storage link already exists.');
            } else {
                $this->error('A file/directory named "storage" already exists in public folder.');
                return 1;
            }
        } else {
            if (windows_os()) {
                $this->call('storage:link');
            } else {
                File::link($target, $link);
            }
            $this->info('Storage link created successfully.');
        }

        // Set permissions (Unix-based systems only)
        if (!windows_os()) {
            chmod(storage_path('app/public'), 0755);
            chmod(storage_path('app/public/projects'), 0755);
            $this->info('Permissions set successfully.');
        }

        $this->info('✓ Storage setup completed!');
        return 0;
    }
}
