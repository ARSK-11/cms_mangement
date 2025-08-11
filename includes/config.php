<?php
// Konfigurasi Backend Node.js
define('BACKEND_PATH', 'C:/Users/Aris/Documents/GitHub/backend_user'); // Path ke folder backend
define('BACKEND_PORT', 3000); // Port yang digunakan backend
define('BACKEND_SCRIPT', 'index.js'); // File utama backend
define('NODE_COMMAND', 'node'); // Command untuk menjalankan Node.js
define('PM2_COMMAND', 'pm2'); // Command untuk PM2 (jika menggunakan PM2)

// Konfigurasi Log
define('LOG_DIR', __DIR__ . '/../logs');
define('BACKEND_LOG_FILE', LOG_DIR . '/backend.log');
define('ERROR_LOG_FILE', LOG_DIR . '/error.log');

// Konfigurasi Timeout
define('COMMAND_TIMEOUT', 30); // Timeout dalam detik untuk eksekusi command

// Konfigurasi UI
define('REFRESH_INTERVAL', 5000); // Interval refresh dalam milidetik

// Buat direktori log jika belum ada
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

// Fungsi untuk mendapatkan OS
function getOS() {
    return strtoupper(substr(PHP_OS, 0, 3));
}

// Fungsi untuk mendapatkan command yang sesuai dengan OS
function getCommandPrefix() {
    if (getOS() === 'WIN') {
        return 'cmd /c ';
    } else {
        return '';
    }
}

// Fungsi untuk mendapatkan command background
function getBackgroundCommand() {
    if (getOS() === 'WIN') {
        return 'start /B ';
    } else {
        return 'nohup ';
    }
}
?>
