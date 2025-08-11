<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$systemInfo = getSystemInfo();
$configFile = __DIR__ . '/includes/config.php';
$configContent = file_get_contents($configFile);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = '';
    $messageType = 'success';
    
    if (isset($_POST['update_config'])) {
        // Update configuration
        $newBackendPath = $_POST['backend_path'];
        $newBackendPort = $_POST['backend_port'];
        $newBackendScript = $_POST['backend_script'];
        
        // Validate inputs
        if (!is_dir($newBackendPath)) {
            $message = 'Path backend tidak valid atau tidak ditemukan';
            $messageType = 'danger';
        } elseif (!is_numeric($newBackendPort) || $newBackendPort < 1 || $newBackendPort > 65535) {
            $message = 'Port harus berupa angka antara 1-65535';
            $messageType = 'danger';
        } else {
            // Update config file
            $newConfig = str_replace(
                ["define('BACKEND_PATH', 'C:/Users/Aris/Documents/GitHub/backend_user');", 
                 "define('BACKEND_PORT', 3000);", 
                 "define('BACKEND_SCRIPT', 'index.js');"],
                ["define('BACKEND_PATH', '$newBackendPath');", 
                 "define('BACKEND_PORT', $newBackendPort);", 
                 "define('BACKEND_SCRIPT', '$newBackendScript');"],
                $configContent
            );
            
            if (file_put_contents($configFile, $newConfig)) {
                $message = 'Konfigurasi berhasil diperbarui';
                $messageType = 'success';
                // Reload config
                require_once 'includes/config.php';
            } else {
                $message = 'Gagal memperbarui konfigurasi';
                $messageType = 'danger';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - CMS Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">CMS Management</h4>
                        <p class="text-muted">Node.js Backend Controller</p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logs.php">
                                <i class="fas fa-file-alt"></i> Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Settings</h1>
                </div>

                <?php if (isset($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Configuration Settings -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-cogs"></i> Backend Configuration</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="backend_path" class="form-label">Backend Path</label>
                                        <input type="text" class="form-control" id="backend_path" name="backend_path" 
                                               value="<?php echo BACKEND_PATH; ?>" required>
                                        <div class="form-text">Path lengkap ke folder backend Node.js</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="backend_port" class="form-label">Backend Port</label>
                                        <input type="number" class="form-control" id="backend_port" name="backend_port" 
                                               value="<?php echo BACKEND_PORT; ?>" min="1" max="65535" required>
                                        <div class="form-text">Port yang digunakan oleh backend (1-65535)</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="backend_script" class="form-label">Backend Script</label>
                                        <input type="text" class="form-control" id="backend_script" name="backend_script" 
                                               value="<?php echo BACKEND_SCRIPT; ?>" required>
                                        <div class="form-text">File utama backend (misal: index.js, app.js)</div>
                                    </div>
                                    
                                    <button type="submit" name="update_config" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Configuration
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> System Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>PHP Information</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
                                            <li><strong>OS:</strong> <?php echo PHP_OS; ?></li>
                                            <li><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Memory Usage</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Current:</strong> <?php echo formatBytes($systemInfo['memory_usage'] ?? 0); ?></li>
                                            <li><strong>Peak:</strong> <?php echo formatBytes($systemInfo['memory_peak'] ?? 0); ?></li>
                                            <li><strong>Limit:</strong> <?php echo ini_get('memory_limit'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6>Disk Usage</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Free Space:</strong> <?php echo formatBytes($systemInfo['disk_free'] ?? 0); ?></li>
                                            <li><strong>Total Space:</strong> <?php echo formatBytes($systemInfo['disk_total'] ?? 0); ?></li>
                                            <li><strong>Used:</strong> <?php 
                                                $used = ($systemInfo['disk_total'] ?? 0) - ($systemInfo['disk_free'] ?? 0);
                                                echo formatBytes($used);
                                            ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Backend Status</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Status:</strong> 
                                                <span class="badge <?php echo checkBackendStatus() ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo checkBackendStatus() ? 'Running' : 'Stopped'; ?>
                                                </span>
                                            </li>
                                            <li><strong>Port <?php echo BACKEND_PORT; ?>:</strong> 
                                                <span class="badge <?php echo isPortOpen(BACKEND_PORT) ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo isPortOpen(BACKEND_PORT) ? 'Open' : 'Closed'; ?>
                                                </span>
                                            </li>
                                            <li><strong>Processes:</strong> <?php echo count(getBackendProcesses()); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="startBackend()">
                                        <i class="fas fa-play"></i> Start Backend
                                    </button>
                                    <button class="btn btn-danger" onclick="stopBackend()">
                                        <i class="fas fa-stop"></i> Stop Backend
                                    </button>
                                    <button class="btn btn-warning" onclick="restartBackend()">
                                        <i class="fas fa-redo"></i> Restart Backend
                                    </button>
                                    <hr>
                                    <button class="btn btn-outline-secondary" onclick="testConnection()">
                                        <i class="fas fa-network-wired"></i> Test Connection
                                    </button>
                                    <button class="btn btn-outline-info" onclick="viewLogs()">
                                        <i class="fas fa-file-alt"></i> View Logs
                                    </button>
                                    <button class="btn btn-outline-warning" onclick="cleanLogs()">
                                        <i class="fas fa-broom"></i> Clean Old Logs
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- File Permissions -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-shield-alt"></i> File Permissions</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li>
                                        <strong>Config File:</strong> 
                                        <span class="badge <?php echo is_writable($configFile) ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo is_writable($configFile) ? 'Writable' : 'Read-only'; ?>
                                        </span>
                                    </li>
                                    <li>
                                        <strong>Log Directory:</strong> 
                                        <span class="badge <?php echo is_writable(LOG_DIR) ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo is_writable(LOG_DIR) ? 'Writable' : 'Read-only'; ?>
                                        </span>
                                    </li>
                                    <li>
                                        <strong>Backend Path:</strong> 
                                        <span class="badge <?php echo is_readable(BACKEND_PATH) ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo is_readable(BACKEND_PATH) ? 'Readable' : 'Not Found'; ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Version Info -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-code-branch"></i> Version Information</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><strong>CMS Version:</strong> 1.0.0</li>
                                    <li><strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
                                    <li><strong>Node.js Support:</strong> 
                                        <span class="badge bg-success">Enabled</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        function testConnection() {
            fetch('api/backend-control.php?action=status')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Connection Test Results:\n- Backend Status: ${data.status ? 'Running' : 'Stopped'}\n- Port Open: ${data.port_open ? 'Yes' : 'No'}\n- Active Processes: ${data.processes.length}`);
                    } else {
                        alert('Connection test failed: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Connection test error: ' + error.message);
                });
        }

        function viewLogs() {
            window.location.href = 'logs.php';
        }

        function cleanLogs() {
            if (confirm('Apakah Anda yakin ingin membersihkan log lama (lebih dari 7 hari)?')) {
                fetch('api/backend-control.php', {
                    method: 'POST',
                    body: new FormData(Object.assign(new FormData(), { action: 'clean_logs', days: 7 }))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Log lama berhasil dibersihkan');
                    } else {
                        alert('Gagal membersihkan log: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
    </script>
</body>
</html>

<?php
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
