<?php
require_once 'config.php';

/**
 * Mengecek status backend apakah sedang berjalan
 */
function checkBackendStatus() {
    // Cek apakah port terbuka
    if (isPortOpen(BACKEND_PORT)) {
        return true;
    }
    
    // Cek apakah ada proses Node.js yang berjalan
    $processes = getBackendProcesses();
    return !empty($processes);
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
function getBackendProcesses() {
    $processes = [];
    
    if (getOS() === 'WIN') {
        // Windows: menggunakan tasklist
        $command = 'tasklist /FI "IMAGENAME eq node.exe" /FO CSV /NH';
        $output = shell_exec($command);
        
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (strpos($line, 'node.exe') !== false) {
                    $parts = str_getcsv($line);
                    if (count($parts) >= 2) {
                        $processes[] = [
                            'pid' => trim($parts[1], '"'),
                            'command' => 'node.exe',
                            'cpu' => '0',
                            'memory' => '0'
                        ];
                    }
                }
            }
        }
    } else {
        // Linux/Unix: menggunakan ps
        $command = "ps aux | grep 'node.*" . BACKEND_SCRIPT . "' | grep -v grep";
        $output = shell_exec($command);
        
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $parts = preg_split('/\s+/', trim($line));
                    if (count($parts) >= 11) {
                        $processes[] = [
                            'pid' => $parts[1],
                            'command' => implode(' ', array_slice($parts, 10)),
                            'cpu' => $parts[2],
                            'memory' => $parts[3]
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
function startBackend() {
    $logMessage = date('Y-m-d H:i:s') . " - Attempting to start backend\n";
    file_put_contents(BACKEND_LOG_FILE, $logMessage, FILE_APPEND);
    
    // Cek apakah backend sudah berjalan
    if (checkBackendStatus()) {
        return ['success' => false, 'message' => 'Backend sudah berjalan'];
    }
    
    // Cek apakah file backend ada
    $backendFile = BACKEND_PATH . '/' . BACKEND_SCRIPT;
    if (!file_exists($backendFile)) {
        $errorMessage = date('Y-m-d H:i:s') . " - Backend file not found: $backendFile\n";
        file_put_contents(ERROR_LOG_FILE, $errorMessage, FILE_APPEND);
        return ['success' => false, 'message' => 'File backend tidak ditemukan'];
    }
    
    // Jalankan backend
    $command = getCommandPrefix() . 'cd "' . BACKEND_PATH . '" && ' . getBackgroundCommand() . NODE_COMMAND . ' ' . BACKEND_SCRIPT . ' > "' . BACKEND_LOG_FILE . '" 2>&1';
    
    if (getOS() === 'WIN') {
        $command = 'start /B cmd /c "cd /d "' . BACKEND_PATH . '" && ' . NODE_COMMAND . ' ' . BACKEND_SCRIPT . ' > "' . BACKEND_LOG_FILE . '" 2>&1"';
    }
    
    $result = shell_exec($command);
    
    // Tunggu sebentar untuk memastikan backend sudah start
    sleep(2);
    
    if (checkBackendStatus()) {
        $successMessage = date('Y-m-d H:i:s') . " - Backend started successfully\n";
        file_put_contents(BACKEND_LOG_FILE, $successMessage, FILE_APPEND);
        return ['success' => true, 'message' => 'Backend berhasil dijalankan'];
    } else {
        $errorMessage = date('Y-m-d H:i:s') . " - Failed to start backend\n";
        file_put_contents(ERROR_LOG_FILE, $errorMessage, FILE_APPEND);
        return ['success' => false, 'message' => 'Gagal menjalankan backend'];
    }
}

/**
 * Menghentikan backend Node.js
 */
function stopBackend() {
    $logMessage = date('Y-m-d H:i:s') . " - Attempting to stop backend\n";
    file_put_contents(BACKEND_LOG_FILE, $logMessage, FILE_APPEND);
    
    $processes = getBackendProcesses();
    
    if (empty($processes)) {
        return ['success' => false, 'message' => 'Tidak ada proses backend yang berjalan'];
    }
    
    $killedCount = 0;
    
    foreach ($processes as $process) {
        $pid = $process['pid'];
        
        if (getOS() === 'WIN') {
            $command = "taskkill /PID $pid /F";
        } else {
            $command = "kill -9 $pid";
        }
        
        $result = shell_exec($command);
        $killedCount++;
    }
    
    // Tunggu sebentar untuk memastikan proses sudah berhenti
    sleep(2);
    
    if (!checkBackendStatus()) {
        $successMessage = date('Y-m-d H:i:s') . " - Backend stopped successfully\n";
        file_put_contents(BACKEND_LOG_FILE, $successMessage, FILE_APPEND);
        return ['success' => true, 'message' => "Backend berhasil dihentikan ($killedCount proses)"];
    } else {
        $errorMessage = date('Y-m-d H:i:s') . " - Failed to stop backend completely\n";
        file_put_contents(ERROR_LOG_FILE, $errorMessage, FILE_APPEND);
        return ['success' => false, 'message' => 'Gagal menghentikan backend sepenuhnya'];
    }
}

/**
 * Restart backend Node.js
 */
function restartBackend() {
    $logMessage = date('Y-m-d H:i:s') . " - Attempting to restart backend\n";
    file_put_contents(BACKEND_LOG_FILE, $logMessage, FILE_APPEND);
    
    // Stop backend terlebih dahulu
    $stopResult = stopBackend();
    
    if (!$stopResult['success']) {
        return $stopResult;
    }
    
    // Tunggu sebentar sebelum start
    sleep(3);
    
    // Start backend
    $startResult = startBackend();
    
    if ($startResult['success']) {
        $successMessage = date('Y-m-d H:i:s') . " - Backend restarted successfully\n";
        file_put_contents(BACKEND_LOG_FILE, $successMessage, FILE_APPEND);
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
        $command = "taskkill /PID $pid /F";
    } else {
        $command = "kill -9 $pid";
    }
    
    $result = shell_exec($command);
    
    $logMessage = date('Y-m-d H:i:s') . " - Killed process PID: $pid\n";
    file_put_contents(BACKEND_LOG_FILE, $logMessage, FILE_APPEND);
    
    return ['success' => true, 'message' => "Proses PID $pid berhasil dihentikan"];
}

/**
 * Mendapatkan log backend
 */
function getBackendLogs($lines = 100) {
    if (file_exists(BACKEND_LOG_FILE)) {
        $logs = file(BACKEND_LOG_FILE);
        return array_slice($logs, -$lines);
    }
    return [];
}

/**
 * Mendapatkan informasi sistem
 */
function getSystemInfo() {
    $info = [];
    
    // Memory usage
    if (function_exists('memory_get_usage')) {
        $info['memory_usage'] = memory_get_usage(true);
        $info['memory_peak'] = memory_get_peak_usage(true);
    }
    
    // Disk usage
    $info['disk_free'] = disk_free_space(BACKEND_PATH);
    $info['disk_total'] = disk_total_space(BACKEND_PATH);
    
    // Load average (Linux only)
    if (getOS() !== 'WIN' && file_exists('/proc/loadavg')) {
        $load = file_get_contents('/proc/loadavg');
        $info['load_average'] = explode(' ', $load)[0];
    }
    
    return $info;
}

/**
 * Membersihkan log lama
 */
function cleanOldLogs($days = 7) {
    $logFiles = [BACKEND_LOG_FILE, ERROR_LOG_FILE];
    
    foreach ($logFiles as $logFile) {
        if (file_exists($logFile)) {
            $fileTime = filemtime($logFile);
            $daysOld = (time() - $fileTime) / (24 * 60 * 60);
            
            if ($daysOld > $days) {
                unlink($logFile);
            }
        }
    }
}
?>
