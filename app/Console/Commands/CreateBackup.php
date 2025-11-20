<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class CreateBackup extends Command
{
    protected $signature = 'backup:create {--cleanup : Clean up old backups}';
    protected $description = 'Create automatic backup of database and files';

    public function handle()
    {
        try {
            $this->info('Starting automatic backup...');
            
            $autoBackupEnabled = Setting::where('key', 'backup.auto_backup_enabled')->first();
            if (!$autoBackupEnabled || !$autoBackupEnabled->value) {
                $this->info('Auto backup is disabled. Skipping...');
                return 0;
            }

            $backupName = 'auto_backup_' . date('Y_m_d_H_i_s');
            $backupPath = storage_path('app/backups');
            
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $dbBackupFile = $backupPath . '/' . $backupName . '_database.sql';
            $this->createDatabaseBackup($dbBackupFile);
            $this->info('Database backup created: ' . basename($dbBackupFile));

            $filesBackupFile = $backupPath . '/' . $backupName . '_files.zip';
            $this->createFilesBackup($filesBackupFile);
            $this->info('Files backup created: ' . basename($filesBackupFile));

            if ($this->option('cleanup')) {
                $this->cleanupOldBackups();
            }

            Log::info('Automatic backup created successfully', [
                'backup_name' => $backupName,
                'database_file' => $dbBackupFile,
                'files_file' => $filesBackupFile
            ]);

            $this->info('Backup completed successfully!');
            return 0;

        } catch (\Exception $e) {
            Log::error('Automatic backup failed', ['error' => $e->getMessage()]);
            $this->error('Backup failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function createDatabaseBackup($filePath)
    {
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $port = config('database.connections.mysql.port', 3306);

        exec('mysqldump --version 2>&1', $versionOutput, $versionCode);
        if ($versionCode === 0) {
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
            if ($returnCode === 0) {
                return;
            }
        }

        $this->createDatabaseBackupFallback($filePath);
    }

    private function createDatabaseBackupFallback($filePath)
    {
        $tables = \DB::select('SHOW TABLES');
        $sql = "-- Automatic backup created on " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            
            $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable->{'Create Table'} . ";\n\n";
            
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
    }

    private function createFilesBackup($filePath)
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($filePath, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Cannot create zip file: ' . $filePath);
        }

        $items = [
            ['path' => storage_path('app/public'), 'type' => 'dir', 'name' => 'storage_public'],
            ['path' => public_path('uploads'), 'type' => 'dir', 'name' => 'uploads'],
            ['path' => base_path('.env'), 'type' => 'file', 'name' => '.env']
        ];

        foreach ($items as $item) {
            try {
                if ($item['type'] === 'file' && is_file($item['path'])) {
                    $zip->addFile($item['path'], $item['name']);
                } elseif ($item['type'] === 'dir' && is_dir($item['path'])) {
                    $this->addDirectoryToZip($zip, $item['path'], $item['name']);
                }
            } catch (\Exception $e) {
                // Continue
            }
        }

        $backupInfo = [
            'created_at' => date('Y-m-d H:i:s'),
            'type' => 'automatic'
        ];
        $zip->addFromString('backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT));
        $zip->close();
    }

    private function addDirectoryToZip($zip, $dir, $zipDir)
    {
        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir() && $file->isReadable() && $file->getSize() <= 50 * 1024 * 1024) {
                    $filePath = $file->getRealPath();
                    $relativePath = $zipDir . '/' . substr($filePath, strlen($dir) + 1);
                    $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
                }
            }
        } catch (\Exception $e) {
            // Continue
        }
    }

    private function cleanupOldBackups()
    {
        $retentionDays = Setting::where('key', 'backup.backup_retention_days')->first();
        $days = $retentionDays ? (int)$retentionDays->value : 30;
        
        $backupPath = storage_path('app/backups');
        $cutoffTime = time() - ($days * 24 * 60 * 60);
        
        $files = glob($backupPath . '/auto_backup_*');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                $deleted++;
            }
        }
        
        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} old backup files");
        }
    }
}