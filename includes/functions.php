<?php
require_once 'config.php';

/**
 * Mengecek status backend apakah sedang berjalan
 */
function checkBackendStatus($backendId = null) {
    if ($backendId === null) {
        $backendId = DEFAULT_BACKEND;
    }
    
    $backend = getBackend($backendId);
    if (!$backend) {
        return false;
    }
    
    return isPortOpen($backend['port']);
}

/**
 * Mengecek apakah port tertentu terbuka
 */
function isPortOpen($port) {
    $connection = @fsockopen('localhost', $port, $errno, $errstr, 1);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    }
    return false;
}

/**
 * Mendapatkan daftar proses backend yang sedang berjalan
 */
function getBackendProcesses($backendId = null) {
    if ($backendId === null) {
        $backendId = DEFAULT_BACKEND;
    }
    
    $backend = getBackend($backendId);
    if (!$backend) {
        return [];
    }
    
    $processes = [];
    
    if (getOS() === 'WIN') {
        // Windows: menggunakan tasklist
        $command = getCommandPrefix() . 'tasklist /FI "IMAGENAME eq node.exe" /FO CSV /NH';
        $output = shell_exec($command);
        
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (strpos($line, 'node.exe') !== false) {
                    $parts = str_getcsv($line);
                    if (count($parts) >= 2) {
                        $processName = trim($parts[0], '"');
                        $pid = trim($parts[1], '"');
                        
                        // Cek apakah proses ini menjalankan backend kita
                        $command2 = getCommandPrefix() . 'tasklist /FI "PID eq ' . $pid . '" /FO CSV /NH';
                        $output2 = shell_exec($command2);
                        if (strpos($output2, $backend['script']) !== false) {
                            $processes[] = [
                                'pid' => $pid,
                                'name' => $processName,
                                'script' => $backend['script'],
                                'port' => $backend['port']
                            ];
                        }
                    }
                }
            }
        }
    } else {
        // Linux/Unix: menggunakan ps
        $command = "ps aux | grep node | grep -v grep";
        $output = shell_exec($command);
        
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (strpos($line, $backend['script']) !== false) {
                    $parts = preg_split('/\s+/', trim($line));
                    if (count($parts) >= 2) {
                        $processes[] = [
                            'pid' => $parts[1],
                            'name' => 'node',
                            'script' => $backend['script'],
                            'port' => $backend['port']
                        ];
                    }
                }
            }
        }
    }
    
    return $processes;
}

/**
 * Menjalankan backend Node.js
 */
function startBackend($backendId = null) {
    if ($backendId === null) {
        $backendId = DEFAULT_BACKEND;
    }
    
    $backend = getBackend($backendId);
    if (!$backend) {
        return ['success' => false, 'message' => 'Backend tidak ditemukan'];
    }
    
    // Cek apakah backend sudah berjalan
    if (checkBackendStatus($backendId)) {
        return ['success' => false, 'message' => 'Backend sudah berjalan di port ' . $backend['port']];
    }
    
    // Cek apakah direktori backend ada
    if (!is_dir($backend['path'])) {
        return ['success' => false, 'message' => 'Direktori backend tidak ditemukan: ' . $backend['path']];
    }
    
    // Cek apakah file script ada
    $scriptPath = $backend['path'] . '/' . $backend['script'];
    if (!file_exists($scriptPath)) {
        return ['success' => false, 'message' => 'File script tidak ditemukan: ' . $scriptPath];
    }
    
    // Jalankan backend
    $command = getCommandPrefix() . getBackgroundCommand() . 'cd "' . $backend['path'] . '" && ' . NODE_COMMAND . ' ' . $backend['script'];
    
    if (getOS() === 'WIN') {
        $command .= ' > NUL 2>&1';
    } else {
        $command .= ' > /dev/null 2>&1 &';
    }
    
    $result = shell_exec($command);
    
    // Tunggu sebentar untuk memastikan backend sudah start
    sleep(2);
    
    if (checkBackendStatus($backendId)) {
        logBackendActivity($backendId, 'START', 'Backend berhasil dijalankan');
        return ['success' => true, 'message' => 'Backend berhasil dijalankan di port ' . $backend['port']];
    } else {
        logBackendActivity($backendId, 'START_ERROR', 'Gagal menjalankan backend');
        return ['success' => false, 'message' => 'Gagal menjalankan backend'];
    }
}

/**
 * Menghentikan backend Node.js
 */
function stopBackend($backendId = null) {
    if ($backendId === null) {
        $backendId = DEFAULT_BACKEND;
    }
    
    $backend = getBackend($backendId);
    if (!$backend) {
        return ['success' => false, 'message' => 'Backend tidak ditemukan'];
    }
    
    $processes = getBackendProcesses($backendId);
    $stoppedCount = 0;
    
    foreach ($processes as $process) {
        if (killProcess($process['pid'])) {
            $stoppedCount++;
        }
    }
    
    if ($stoppedCount > 0) {
        logBackendActivity($backendId, 'STOP', 'Backend dihentikan, ' . $stoppedCount . ' proses dibunuh');
        return ['success' => true, 'message' => 'Backend dihentikan, ' . $stoppedCount . ' proses dibunuh'];
    } else {
        return ['success' => false, 'message' => 'Tidak ada proses backend yang ditemukan'];
    }
}

/**
 * Restart backend Node.js
 */
function restartBackend($backendId = null) {
    if ($backendId === null) {
        $backendId = DEFAULT_BACKEND;
    }
    
    $stopResult = stopBackend($backendId);
    if (!$stopResult['success']) {
        return $stopResult;
    }
    
    sleep(2); // Tunggu sebentar sebelum start
    
    $startResult = startBackend($backendId);
    if ($startResult['success']) {
        logBackendActivity($backendId, 'RESTART', 'Backend berhasil di-restart');
        return ['success' => true, 'message' => 'Backend berhasil di-restart'];
    } else {
        return $startResult;
    }
}

/**
 * Membunuh proses berdasarkan PID
 */
function killProcess($pid) {
    if (getOS() === 'WIN') {
        $command = getCommandPrefix() . 'taskkill /PID ' . $pid . ' /F';
    } else {
        $command = 'kill -9 ' . $pid;
    }
    
    $result = shell_exec($command);
    return $result !== null;
}

/**
 * Mendapatkan log backend
 */
function getBackendLogs($lines = 100, $backendId = null) {
    if ($backendId === null) {
        $backendId = DEFAULT_BACKEND;
    }
    
    $backend = getBackend($backendId);
    if (!$backend) {
        return [];
    }
    
    $logFile = $backend['path'] . '/logs/app.log';
    if (!file_exists($logFile)) {
        return [];
    }
    
    $logs = file($logFile);
    return array_slice($logs, -$lines);
}

/**
 * Mendapatkan informasi sistem
 */
function getSystemInfo() {
    $info = [];
    
    // PHP Info
    $info['php_version'] = PHP_VERSION;
    $info['php_os'] = PHP_OS;
    $info['php_sapi'] = php_sapi_name();
    
    // Memory Info
    $info['memory_limit'] = ini_get('memory_limit');
    $info['memory_usage'] = memory_get_usage(true);
    $info['memory_peak'] = memory_get_peak_usage(true);
    
    // Disk Info
    $info['disk_free_space'] = disk_free_space('.');
    $info['disk_total_space'] = disk_total_space('.');
    
    // Node.js Info
    $nodeVersion = shell_exec(getCommandPrefix() . NODE_COMMAND . ' --version 2>&1');
    $info['node_version'] = trim($nodeVersion) ?: 'Tidak terinstall';
    
    // Git Info
    $gitVersion = shell_exec(getCommandPrefix() . GIT_COMMAND . ' --version 2>&1');
    $info['git_version'] = trim($gitVersion) ?: 'Tidak terinstall';
    
    return $info;
}

/**
 * Membersihkan log lama
 */
function cleanOldLogs($days = 7) {
    $logDir = LOG_DIR;
    $files = glob($logDir . '/*.log');
    $deletedCount = 0;
    
    foreach ($files as $file) {
        if (filemtime($file) < time() - ($days * 24 * 60 * 60)) {
            if (unlink($file)) {
                $deletedCount++;
            }
        }
    }
    
    return $deletedCount;
}

/**
 * Log aktivitas backend
 */
function logBackendActivity($backendId, $action, $message) {
    $backend = getBackend($backendId);
    $logEntry = date('Y-m-d H:i:s') . ' [' . ($backend ? $backend['name'] : $backendId) . '] ' . $action . ': ' . $message . PHP_EOL;
    file_put_contents(BACKEND_LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Upload file
 */
function uploadFile($file, $targetDir = null) {
    if ($targetDir === null) {
        $targetDir = UPLOAD_DIR;
    }
    
    // Validasi file
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'File tidak valid'];
    }
    
    // Cek ukuran file
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (maksimal ' . formatBytes(MAX_UPLOAD_SIZE) . ')'];
    }
    
    // Cek ekstensi file
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Ekstensi file tidak diizinkan: ' . $extension];
    }
    
    // Buat nama file unik
    $filename = time() . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $targetPath = $targetDir . '/' . $filename;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Log upload
        $logEntry = date('Y-m-d H:i:s') . ' UPLOAD: ' . $file['name'] . ' -> ' . $targetPath . ' (' . formatBytes($file['size']) . ')' . PHP_EOL;
        file_put_contents(UPLOAD_LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
        
        return [
            'success' => true, 
            'message' => 'File berhasil diupload',
            'filename' => $filename,
            'original_name' => $file['name'],
            'size' => $file['size'],
            'path' => $targetPath
        ];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }
}

/**
 * Mendapatkan daftar file yang diupload
 */
function getUploadedFiles() {
    $files = [];
    $uploadDir = UPLOAD_DIR;
    
    if (is_dir($uploadDir)) {
        $fileList = scandir($uploadDir);
        foreach ($fileList as $file) {
            if ($file !== '.' && $file !== '..' && is_file($uploadDir . '/' . $file)) {
                $filePath = $uploadDir . '/' . $file;
                $files[] = [
                    'name' => $file,
                    'size' => filesize($filePath),
                    'modified' => filemtime($filePath),
                    'path' => $filePath
                ];
            }
        }
    }
    
    // Urutkan berdasarkan waktu modifikasi terbaru
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    return $files;
}

/**
 * Hapus file yang diupload
 */
function deleteUploadedFile($filename) {
    $filePath = UPLOAD_DIR . '/' . $filename;
    
    if (file_exists($filePath) && unlink($filePath)) {
        $logEntry = date('Y-m-d H:i:s') . ' DELETE: ' . $filename . PHP_EOL;
        file_put_contents(UPLOAD_LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
        return ['success' => true, 'message' => 'File berhasil dihapus'];
    } else {
        return ['success' => false, 'message' => 'Gagal menghapus file'];
    }
}

/**
 * Clone repository Git
 */
function cloneRepository($repositoryUrl, $targetPath, $branch = 'main') {
    // Cek apakah Git terinstall
    $gitVersion = shell_exec(getCommandPrefix() . GIT_COMMAND . ' --version 2>&1');
    if (!trim($gitVersion)) {
        return ['success' => false, 'message' => 'Git tidak terinstall'];
    }
    
    // Cek apakah direktori target sudah ada
    if (is_dir($targetPath)) {
        return ['success' => false, 'message' => 'Direktori target sudah ada: ' . $targetPath];
    }
    
    // Clone repository
    $command = getCommandPrefix() . GIT_COMMAND . ' clone -b ' . $branch . ' ' . $repositoryUrl . ' "' . $targetPath . '" 2>&1';
    $output = shell_exec($command);
    
    if (is_dir($targetPath)) {
        $logEntry = date('Y-m-d H:i:s') . ' CLONE: ' . $repositoryUrl . ' -> ' . $targetPath . PHP_EOL;
        file_put_contents(UPLOAD_LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
        
        return ['success' => true, 'message' => 'Repository berhasil di-clone', 'output' => $output];
    } else {
        return ['success' => false, 'message' => 'Gagal clone repository', 'output' => $output];
    }
}

/**
 * Pull repository Git
 */
function pullRepository($repositoryPath) {
    if (!is_dir($repositoryPath)) {
        return ['success' => false, 'message' => 'Direktori repository tidak ditemukan'];
    }
    
    $command = getCommandPrefix() . 'cd "' . $repositoryPath . '" && ' . GIT_COMMAND . ' pull 2>&1';
    $output = shell_exec($command);
    
    $logEntry = date('Y-m-d H:i:s') . ' PULL: ' . $repositoryPath . PHP_EOL;
    file_put_contents(UPLOAD_LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
    
    return ['success' => true, 'message' => 'Repository berhasil di-pull', 'output' => $output];
}

/**
 * Install dependencies Node.js
 */
function installDependencies($backendId = null) {
    if ($backendId === null) {
        $backendId = DEFAULT_BACKEND;
    }
    
    $backend = getBackend($backendId);
    if (!$backend) {
        return ['success' => false, 'message' => 'Backend tidak ditemukan'];
    }
    
    if (!is_dir($backend['path'])) {
        return ['success' => false, 'message' => 'Direktori backend tidak ditemukan'];
    }
    
    $command = getCommandPrefix() . 'cd "' . $backend['path'] . '" && npm install 2>&1';
    $output = shell_exec($command);
    
    $logEntry = date('Y-m-d H:i:s') . ' NPM_INSTALL: ' . $backend['path'] . PHP_EOL;
    file_put_contents(UPLOAD_LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
    
    return ['success' => true, 'message' => 'Dependencies berhasil diinstall', 'output' => $output];
}

/**
 * Format bytes ke human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
