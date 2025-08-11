<?php
// Konfigurasi Multiple Backend Node.js
$BACKENDS = [
    'backend1' => [
        'name' => 'Backend User Management',
        'path' => 'C:/Users/Aris/Documents/GitHub/backend_user',
        'port' => 3000,
        'script' => 'index.js',
        'description' => 'Backend untuk manajemen user'
    ],
    'backend2' => [
        'name' => 'Backend API',
        'path' => 'C:/Users/Aris/Documents/GitHub/backend_api',
        'port' => 3001,
        'script' => 'server.js',
        'description' => 'Backend untuk API utama'
    ],
    'backend3' => [
        'name' => 'Backend Admin',
        'path' => 'C:/Users/Aris/Documents/GitHub/backend_admin',
        'port' => 3002,
        'script' => 'app.js',
        'description' => 'Backend untuk admin panel'
    ]
];

// Konfigurasi Default Backend
define('DEFAULT_BACKEND', 'backend1');

// Konfigurasi Command
define('NODE_COMMAND', 'node'); // Command untuk menjalankan Node.js
define('PM2_COMMAND', 'pm2'); // Command untuk PM2 (jika menggunakan PM2)
define('GIT_COMMAND', 'git'); // Command untuk Git

// Konfigurasi Upload
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_EXTENSIONS', ['zip', 'tar', 'gz', 'rar', 'js', 'json', 'txt', 'md', 'yml', 'yaml']);

// Konfigurasi Log
define('LOG_DIR', __DIR__ . '/../logs');
define('BACKEND_LOG_FILE', LOG_DIR . '/backend.log');
define('ERROR_LOG_FILE', LOG_DIR . '/error.log');
define('UPLOAD_LOG_FILE', LOG_DIR . '/upload.log');

// Konfigurasi Timeout
define('COMMAND_TIMEOUT', 60); // Timeout dalam detik untuk eksekusi command

// Konfigurasi UI
define('REFRESH_INTERVAL', 5000); // Interval refresh dalam milidetik

// Buat direktori yang diperlukan jika belum ada
$directories = [LOG_DIR, UPLOAD_DIR];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
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

// Fungsi untuk mendapatkan backend berdasarkan ID
function getBackend($backendId) {
    global $BACKENDS;
    return $BACKENDS[$backendId] ?? null;
}

// Fungsi untuk mendapatkan semua backend
function getAllBackends() {
    global $BACKENDS;
    return $BACKENDS;
}
?>
