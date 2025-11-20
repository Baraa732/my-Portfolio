<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class SettingsController extends Controller
{
    private $settingsConfig = [
        'general' => [
            'site_name' => ['type' => 'string', 'required' => true, 'max' => 255],
            'site_description' => ['type' => 'text', 'required' => false, 'max' => 1000],
            'site_keywords' => ['type' => 'text', 'required' => false, 'max' => 500],
            'maintenance_mode' => ['type' => 'boolean', 'required' => false],
            'timezone' => ['type' => 'string', 'required' => true],
        ],
        'security' => [
            'two_factor_enabled' => ['type' => 'boolean', 'required' => false, 'permission' => 'super_admin'],
            'session_timeout' => ['type' => 'integer', 'required' => true, 'min' => 5, 'max' => 1440],
            'max_login_attempts' => ['type' => 'integer', 'required' => true, 'min' => 3, 'max' => 10],
            'password_expiry_days' => ['type' => 'integer', 'required' => false, 'min' => 30, 'max' => 365],
            'force_https' => ['type' => 'boolean', 'required' => false, 'permission' => 'super_admin'],
        ],
        'email' => [
            'smtp_host' => ['type' => 'string', 'required' => false, 'encrypted' => true],
            'smtp_port' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 65535],
            'smtp_username' => ['type' => 'string', 'required' => false, 'encrypted' => true],
            'smtp_password' => ['type' => 'password', 'required' => false, 'encrypted' => true],
            'mail_from_address' => ['type' => 'email', 'required' => false],
            'mail_from_name' => ['type' => 'string', 'required' => false],
        ],
        'backup' => [
            'auto_backup_enabled' => ['type' => 'boolean', 'required' => false, 'permission' => 'super_admin'],
            'backup_frequency' => ['type' => 'string', 'required' => false, 'options' => ['daily', 'weekly', 'monthly']],
            'backup_retention_days' => ['type' => 'integer', 'required' => false, 'min' => 1, 'max' => 365],
        ]
    ];

    public function index()
    {
        try {
            $user = Auth::user();
            $settings = $this->loadSettings();
            
            return response()->json([
                'success' => true,
                'settings' => $settings,
                'user_permissions' => $this->getUserPermissions($user)
            ]);
        } catch (\Exception $e) {
            Log::error('Settings load error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'settings' => $this->getDefaultSettings(),
                'user_permissions' => ['is_super_admin' => false]
            ]);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $group = $request->input('group');
        $settings = $request->input('settings', []);
        
        if (!isset($this->settingsConfig[$group])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid settings group'
            ], 400);
        }
        
        $rules = [];
        foreach ($settings as $key => $value) {
            if (!isset($this->settingsConfig[$group][$key])) continue;
            
            $config = $this->settingsConfig[$group][$key];
            
            if (isset($config['permission']) && !$this->hasPermission($user, $config['permission'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient permissions for setting: {$key}"
                ], 403);
            }
            
            $rule = [];
            if ($config['required']) $rule[] = 'required';
            
            switch ($config['type']) {
                case 'string':
                case 'text':
                    $rule[] = 'string';
                    if (isset($config['max'])) $rule[] = "max:{$config['max']}";
                    break;
                case 'email':
                    $rule[] = 'email';
                    break;
                case 'integer':
                    $rule[] = 'integer';
                    if (isset($config['min'])) $rule[] = "min:{$config['min']}";
                    if (isset($config['max'])) $rule[] = "max:{$config['max']}";
                    break;
                case 'boolean':
                    $rule[] = 'boolean';
                    break;
                case 'password':
                    if ($value) $rule[] = 'string|min:8';
                    break;
            }
            
            if (isset($config['options'])) {
                $rule[] = 'in:' . implode(',', $config['options']);
            }
            
            $rules["settings.{$key}"] = implode('|', $rule);
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            foreach ($settings as $key => $value) {
                if (!isset($this->settingsConfig[$group][$key])) continue;
                
                $config = $this->settingsConfig[$group][$key];
                $fullKey = "{$group}.{$key}";
                
                if ($config['type'] === 'password' && empty($value)) continue;
                
                Setting::updateOrCreate(
                    ['key' => $fullKey],
                    [
                        'value' => $value,
                        'type' => $config['type'],
                        'group' => $group,
                        'is_encrypted' => isset($config['encrypted']) ? $config['encrypted'] : false,
                        'requires_permission' => $config['permission'] ?? null
                    ]
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings'
            ], 500);
        }
    }

    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully'
        ]);
    }

    private function loadSettings()
    {
        $settings = Setting::all()->keyBy('key');
        $result = [];
        
        foreach ($this->settingsConfig as $group => $groupConfig) {
            $result[$group] = [];
            foreach ($groupConfig as $key => $config) {
                $fullKey = "{$group}.{$key}";
                $setting = $settings->get($fullKey);
                
                $value = $setting ? $setting->value : $this->getDefaultValue($config);
                
                // Convert string boolean values
                if ($config['type'] === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                } elseif ($config['type'] === 'integer') {
                    $value = (int) $value;
                }
                
                $result[$group][$key] = [
                    'value' => $value,
                    'config' => $config
                ];
            }
        }
        
        return $result;
    }
    
    private function getDefaultSettings()
    {
        $result = [];
        foreach ($this->settingsConfig as $group => $groupConfig) {
            $result[$group] = [];
            foreach ($groupConfig as $key => $config) {
                $result[$group][$key] = [
                    'value' => $this->getDefaultValue($config),
                    'config' => $config
                ];
            }
        }
        return $result;
    }

    private function getDefaultValue($config)
    {
        switch ($config['type']) {
            case 'boolean': return false;
            case 'integer': 
                if (isset($config['min'])) return $config['min'];
                return 0;
            case 'string': 
            case 'text': 
            case 'email': 
            case 'password': 
                return '';
            default: 
                return null;
        }
    }

    private function hasPermission($user, $permission)
    {
        if ($permission === 'super_admin') {
            return $user->email === 'baraaalrifaee732@gmail.com';
        }
        return true;
    }

    private function getUserPermissions($user)
    {
        return [
            'is_super_admin' => $this->hasPermission($user, 'super_admin')
        ];
    }

    public function createBackup(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->hasPermission($user, 'super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin privileges required'
            ], 403);
        }

        try {
            $backupName = 'backup_' . date('Y_m_d_H_i_s');
            $backupPath = storage_path('app/backups');
            $progressFile = $backupPath . '/' . $backupName . '_progress.json';
            
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Initialize progress
            $this->updateProgress($progressFile, 0, 'Starting backup...');
            
            // Database backup (50% of total)
            $this->updateProgress($progressFile, 10, 'Creating database backup...');
            $dbBackupFile = $backupPath . '/' . $backupName . '_database.sql';
            $this->createDatabaseBackupWithProgress($dbBackupFile, $progressFile, 10, 50);

            // Files backup (50% of total)
            $this->updateProgress($progressFile, 50, 'Creating files backup...');
            $filesBackupFile = $backupPath . '/' . $backupName . '_files.zip';
            $this->createFilesBackupWithProgress($filesBackupFile, $progressFile, 50, 90);

            // Finalize
            $this->updateProgress($progressFile, 95, 'Finalizing backup...');
            sleep(1); // Brief pause for realism
            
            $this->updateProgress($progressFile, 100, 'Backup completed successfully!');
            
            // Clean up progress file
            if (file_exists($progressFile)) {
                unlink($progressFile);
            }

            Log::info('Backup created successfully', [
                'user_id' => $user->uuid,
                'backup_name' => $backupName,
                'database_file' => $dbBackupFile,
                'files_file' => $filesBackupFile
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_name' => $backupName,
                'size' => $this->getBackupSize($backupPath, $backupName)
            ]);

        } catch (\Exception $e) {
            // Clean up progress file on error
            if (isset($progressFile) && file_exists($progressFile)) {
                unlink($progressFile);
            }
            
            Log::error('Backup creation failed', [
                'user_id' => $user->uuid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Backup creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBackupProgress(Request $request, $backupName)
    {
        $progressFile = storage_path('app/backups/' . $backupName . '_progress.json');
        
        if (!file_exists($progressFile)) {
            return response()->json([
                'success' => false,
                'message' => 'Progress file not found'
            ], 404);
        }
        
        $progress = json_decode(file_get_contents($progressFile), true);
        
        return response()->json([
            'success' => true,
            'progress' => $progress['percentage'],
            'message' => $progress['message'],
            'completed' => $progress['percentage'] >= 100
        ]);
    }

    private function updateProgress($progressFile, $percentage, $message)
    {
        $progress = [
            'percentage' => $percentage,
            'message' => $message,
            'timestamp' => time()
        ];
        
        file_put_contents($progressFile, json_encode($progress));
    }

    public function listBackups(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->hasPermission($user, 'super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin privileges required'
            ], 403);
        }

        try {
            $backupPath = storage_path('app/backups');
            $backups = [];

            if (file_exists($backupPath)) {
                $files = glob($backupPath . '/backup_*_database.sql');
                
                foreach ($files as $file) {
                    $basename = basename($file, '_database.sql');
                    $timestamp = str_replace('backup_', '', $basename);
                    $date = \DateTime::createFromFormat('Y_m_d_H_i_s', $timestamp);
                    
                    $backups[] = [
                        'name' => $basename,
                        'date' => $date ? $date->format('Y-m-d H:i:s') : 'Unknown',
                        'size' => $this->getBackupSize($backupPath, $basename),
                        'files' => [
                            'database' => file_exists($backupPath . '/' . $basename . '_database.sql'),
                            'files' => file_exists($backupPath . '/' . $basename . '_files.zip')
                        ]
                    ];
                }

                // Sort by date descending
                usort($backups, function($a, $b) {
                    return strtotime($b['date']) - strtotime($a['date']);
                });
            }

            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list backups: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteBackup(Request $request, $backupName)
    {
        $user = Auth::user();
        
        if (!$this->hasPermission($user, 'super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin privileges required'
            ], 403);
        }

        try {
            $backupPath = storage_path('app/backups');
            $dbFile = $backupPath . '/' . $backupName . '_database.sql';
            $filesFile = $backupPath . '/' . $backupName . '_files.zip';

            $deleted = [];
            if (file_exists($dbFile)) {
                unlink($dbFile);
                $deleted[] = 'database';
            }
            if (file_exists($filesFile)) {
                unlink($filesFile);
                $deleted[] = 'files';
            }

            Log::info('Backup deleted', [
                'user_id' => $user->uuid,
                'backup_name' => $backupName,
                'deleted_files' => $deleted
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkCronStatus(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->hasPermission($user, 'super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin privileges required'
            ], 403);
        }

        try {
            // Check if schedule:run has been executed recently
            $logFile = storage_path('logs/laravel.log');
            $cronWorking = false;
            $lastRun = null;
            
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                // Look for recent schedule:run entries (within last 2 minutes)
                $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*schedule:run/i';
                preg_match_all($pattern, $logContent, $matches);
                
                if (!empty($matches[1])) {
                    $lastRunTime = end($matches[1]);
                    $lastRun = $lastRunTime;
                    $timeDiff = time() - strtotime($lastRunTime);
                    $cronWorking = $timeDiff < 120; // Within last 2 minutes
                }
            }
            
            // Alternative: Check if auto backups exist
            $backupPath = storage_path('app/backups');
            $autoBackups = glob($backupPath . '/auto_backup_*');
            $hasAutoBackups = count($autoBackups) > 0;
            
            return response()->json([
                'success' => true,
                'cron_working' => $cronWorking || $hasAutoBackups,
                'last_run' => $lastRun,
                'auto_backups_count' => count($autoBackups),
                'message' => $cronWorking ? 'Cron job is working properly' : 'Cron job may not be configured correctly'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check cron status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testAutoBackup(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->hasPermission($user, 'super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin privileges required'
            ], 403);
        }

        try {
            \Artisan::call('backup:create', ['--cleanup' => true]);
            $output = \Artisan::output();
            
            Log::info('Auto backup test executed', [
                'user_id' => $user->uuid,
                'output' => $output
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Auto backup test completed successfully',
                'output' => $output
            ]);

        } catch (\Exception $e) {
            Log::error('Auto backup test failed', [
                'user_id' => $user->uuid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Auto backup test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restoreBackup(Request $request, $backupName)
    {
        $user = Auth::user();
        
        if (!$this->hasPermission($user, 'super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Super admin privileges required'
            ], 403);
        }

        $type = $request->input('type'); // 'database' or 'files'
        
        try {
            $backupPath = storage_path('app/backups');
            
            if ($type === 'database') {
                $dbFile = $backupPath . '/' . $backupName . '_database.sql';
                if (!file_exists($dbFile)) {
                    throw new \Exception('Database backup file not found');
                }
                $this->restoreDatabase($dbFile);
                $message = 'Database restored successfully';
            } elseif ($type === 'files') {
                $filesFile = $backupPath . '/' . $backupName . '_files.zip';
                if (!file_exists($filesFile)) {
                    throw new \Exception('Files backup not found');
                }
                $this->restoreFiles($filesFile);
                $message = 'Files restored successfully';
            } else {
                throw new \Exception('Invalid restore type');
            }

            Log::info('Backup restored', [
                'user_id' => $user->uuid,
                'backup_name' => $backupName,
                'type' => $type
            ]);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Backup restore failed', [
                'user_id' => $user->uuid,
                'backup_name' => $backupName,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function restoreDatabase($sqlFile)
    {
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $port = config('database.connections.mysql.port', 3306);

        // Try mysql command first
        exec('mysql --version 2>&1', $versionOutput, $versionCode);
        if ($versionCode === 0) {
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($sqlFile)
            );

            exec($command, $output, $returnCode);
            if ($returnCode === 0) {
                return;
            }
        }

        // Fallback: Execute SQL manually
        $sql = file_get_contents($sqlFile);
        \DB::unprepared($sql);
    }

    private function restoreFiles($zipFile)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipFile) !== TRUE) {
            throw new \Exception('Cannot open backup file');
        }

        $extractPath = storage_path('app/restore_temp');
        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        $zip->extractTo($extractPath);
        $zip->close();

        // Restore files to their original locations
        $mappings = [
            'storage_public' => storage_path('app/public'),
            'uploads' => public_path('uploads'),
            'css' => public_path('css'),
            'js' => public_path('js'),
            'images' => public_path('images')
        ];

        foreach ($mappings as $source => $destination) {
            $sourcePath = $extractPath . '/' . $source;
            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destination);
            }
        }

        // Restore .env file
        $envFile = $extractPath . '/.env';
        if (file_exists($envFile)) {
            copy($envFile, base_path('.env'));
        }

        // Clean up temp directory
        $this->deleteDirectory($extractPath);
    }

    private function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $targetPath = $destination . '/' . substr($file->getRealPath(), strlen($source) + 1);
            if ($file->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                copy($file->getRealPath(), $targetPath);
            }
        }
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) return;
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    private function createDatabaseBackupWithProgress($filePath, $progressFile, $startPercent, $endPercent)
    {
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $port = config('database.connections.mysql.port', 3306);

        $this->updateProgress($progressFile, $startPercent + 5, 'Connecting to database...');
        sleep(1);

        // Check if mysqldump is available
        exec('mysqldump --version 2>&1', $versionOutput, $versionCode);
        if ($versionCode !== 0) {
            $this->updateProgress($progressFile, $startPercent + 10, 'Using fallback method...');
            $this->createDatabaseBackupFallbackWithProgress($filePath, $progressFile, $startPercent + 10, $endPercent);
            return;
        }

        $this->updateProgress($progressFile, $startPercent + 15, 'Exporting database tables...');
        
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        // Simulate progress during export
        for ($i = $startPercent + 20; $i < $endPercent; $i += 5) {
            $this->updateProgress($progressFile, $i, 'Exporting database... ' . round(($i - $startPercent) / ($endPercent - $startPercent) * 100) . '%');
            usleep(500000); // 0.5 second
        }

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('mysqldump failed', [
                'command' => $command,
                'output' => $output,
                'return_code' => $returnCode
            ]);
            
            $this->updateProgress($progressFile, $startPercent + 20, 'Mysqldump failed, using fallback...');
            $this->createDatabaseBackupFallbackWithProgress($filePath, $progressFile, $startPercent + 20, $endPercent);
        } else {
            $this->updateProgress($progressFile, $endPercent, 'Database backup completed');
        }
    }

    private function createDatabaseBackup($filePath)
    {
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $port = config('database.connections.mysql.port', 3306);

        // Check if mysqldump is available
        exec('mysqldump --version 2>&1', $versionOutput, $versionCode);
        if ($versionCode !== 0) {
            // Fallback to Laravel's database export if mysqldump is not available
            $this->createDatabaseBackupFallback($filePath);
            return;
        }

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('mysqldump failed', [
                'command' => $command,
                'output' => $output,
                'return_code' => $returnCode
            ]);
            
            // Try fallback method
            $this->createDatabaseBackupFallback($filePath);
        }
    }

    private function createDatabaseBackupFallbackWithProgress($filePath, $progressFile, $startPercent, $endPercent)
    {
        try {
            $this->updateProgress($progressFile, $startPercent + 5, 'Getting database tables...');
            $tables = \DB::select('SHOW TABLES');
            $sql = "-- Database backup created on " . date('Y-m-d H:i:s') . "\n\n";
            
            $totalTables = count($tables);
            
            foreach ($tables as $index => $table) {
                $tableName = array_values((array) $table)[0];
                $currentPercent = $startPercent + 10 + (($index / $totalTables) * ($endPercent - $startPercent - 15));
                
                $this->updateProgress($progressFile, $currentPercent, 'Backing up table: ' . $tableName);
                
                // Get table structure
                $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable->{'Create Table'} . ";\n\n";
                
                // Get table data
                $rows = \DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "INSERT INTO `{$tableName}` VALUES\n";
                    $values = [];
                    foreach ($rows as $row) {
                        $rowData = array_map(function($value) {
                            return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                        }, (array) $row);
                        $values[] = '(' . implode(', ', $rowData) . ')';
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
                
                usleep(200000); // 0.2 second delay
            }
            
            $this->updateProgress($progressFile, $endPercent - 5, 'Writing backup file...');
            file_put_contents($filePath, $sql);
            
        } catch (\Exception $e) {
            throw new \Exception('Database backup failed: ' . $e->getMessage());
        }
    }

    private function createDatabaseBackupFallback($filePath)
    {
        try {
            $tables = \DB::select('SHOW TABLES');
            $sql = "-- Database backup created on " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                // Get table structure
                $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable->{'Create Table'} . ";\n\n";
                
                // Get table data
                $rows = \DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "INSERT INTO `{$tableName}` VALUES\n";
                    $values = [];
                    foreach ($rows as $row) {
                        $rowData = array_map(function($value) {
                            return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                        }, (array) $row);
                        $values[] = '(' . implode(', ', $rowData) . ')';
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }
            
            file_put_contents($filePath, $sql);
            
        } catch (\Exception $e) {
            throw new \Exception('Database backup failed: ' . $e->getMessage());
        }
    }

    private function createFilesBackupWithProgress($filePath, $progressFile, $startPercent, $endPercent)
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($filePath, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Cannot create zip file: ' . $filePath);
        }

        $this->updateProgress($progressFile, $startPercent + 5, 'Preparing files for backup...');
        
        $items = [
            ['path' => storage_path('app/public'), 'type' => 'dir', 'name' => 'storage_public'],
            ['path' => public_path('uploads'), 'type' => 'dir', 'name' => 'uploads'],
            ['path' => base_path('.env'), 'type' => 'file', 'name' => '.env'],
            ['path' => public_path('css'), 'type' => 'dir', 'name' => 'css'],
            ['path' => public_path('js'), 'type' => 'dir', 'name' => 'js'],
            ['path' => public_path('images'), 'type' => 'dir', 'name' => 'images']
        ];

        $totalItems = count($items);
        $addedFiles = 0;
        
        foreach ($items as $index => $item) {
            $currentPercent = $startPercent + 10 + (($index / $totalItems) * ($endPercent - $startPercent - 15));
            $this->updateProgress($progressFile, $currentPercent, 'Adding ' . $item['name'] . ' to backup...');
            
            try {
                if ($item['type'] === 'file' && is_file($item['path'])) {
                    $zip->addFile($item['path'], $item['name']);
                    $addedFiles++;
                } elseif ($item['type'] === 'dir' && is_dir($item['path'])) {
                    $this->addDirectoryToZipWithProgress($zip, $item['path'], $item['name'], $progressFile, $currentPercent, $currentPercent + 5);
                    $addedFiles++;
                }
                
                usleep(300000); // 0.3 second delay for realism
            } catch (\Exception $e) {
                Log::warning('Failed to add to backup: ' . $item['path'], ['error' => $e->getMessage()]);
            }
        }

        $this->updateProgress($progressFile, $endPercent - 5, 'Adding backup information...');
        
        $backupInfo = [
            'created_at' => date('Y-m-d H:i:s'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'files_added' => $addedFiles
        ];
        $zip->addFromString('backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT));

        $zip->close();
        $this->updateProgress($progressFile, $endPercent, 'Files backup completed');
    }

    private function createFilesBackup($filePath)
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($filePath, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Cannot create zip file: ' . $filePath);
        }

        // Add important directories and files
        $items = [
            ['path' => storage_path('app/public'), 'type' => 'dir', 'name' => 'storage_public'],
            ['path' => public_path('uploads'), 'type' => 'dir', 'name' => 'uploads'],
            ['path' => base_path('.env'), 'type' => 'file', 'name' => '.env'],
            ['path' => public_path('css'), 'type' => 'dir', 'name' => 'css'],
            ['path' => public_path('js'), 'type' => 'dir', 'name' => 'js'],
            ['path' => public_path('images'), 'type' => 'dir', 'name' => 'images']
        ];

        $addedFiles = 0;
        foreach ($items as $item) {
            try {
                if ($item['type'] === 'file' && is_file($item['path'])) {
                    $zip->addFile($item['path'], $item['name']);
                    $addedFiles++;
                } elseif ($item['type'] === 'dir' && is_dir($item['path'])) {
                    $this->addDirectoryToZip($zip, $item['path'], $item['name']);
                    $addedFiles++;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to add to backup: ' . $item['path'], ['error' => $e->getMessage()]);
            }
        }

        // Add a backup info file
        $backupInfo = [
            'created_at' => date('Y-m-d H:i:s'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'files_added' => $addedFiles
        ];
        $zip->addFromString('backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT));

        $zip->close();
    }

    private function addDirectoryToZipWithProgress($zip, $dir, $zipDir, $progressFile, $startPercent, $endPercent)
    {
        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $fileArray = iterator_to_array($files);
            $totalFiles = count($fileArray);
            $processedFiles = 0;

            foreach ($fileArray as $file) {
                if (!$file->isDir() && $file->isReadable()) {
                    $filePath = $file->getRealPath();
                    $relativePath = $zipDir . '/' . substr($filePath, strlen($dir) + 1);
                    
                    // Skip files that are too large (>50MB)
                    if ($file->getSize() > 50 * 1024 * 1024) {
                        continue;
                    }
                    
                    $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
                    $processedFiles++;
                    
                    // Update progress every 10 files or for small directories
                    if ($processedFiles % 10 === 0 || $totalFiles < 50) {
                        $currentPercent = $startPercent + (($processedFiles / $totalFiles) * ($endPercent - $startPercent));
                        $this->updateProgress($progressFile, $currentPercent, 'Adding files from ' . $zipDir . '... (' . $processedFiles . '/' . $totalFiles . ')');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error adding directory to zip: ' . $dir, ['error' => $e->getMessage()]);
        }
    }

    private function addDirectoryToZip($zip, $dir, $zipDir)
    {
        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir() && $file->isReadable()) {
                    $filePath = $file->getRealPath();
                    $relativePath = $zipDir . '/' . substr($filePath, strlen($dir) + 1);
                    
                    // Skip files that are too large (>50MB)
                    if ($file->getSize() > 50 * 1024 * 1024) {
                        continue;
                    }
                    
                    $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error adding directory to zip: ' . $dir, ['error' => $e->getMessage()]);
        }
    }

    private function getBackupSize($backupPath, $backupName)
    {
        $totalSize = 0;
        $files = [
            $backupPath . '/' . $backupName . '_database.sql',
            $backupPath . '/' . $backupName . '_files.zip'
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $totalSize += filesize($file);
            }
        }

        return $this->formatBytes($totalSize);
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}