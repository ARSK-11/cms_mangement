<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$logs = getBackendLogs(200); // Ambil 200 baris terakhir
$errorLogs = [];
if (file_exists(ERROR_LOG_FILE)) {
    $errorLogs = file(ERROR_LOG_FILE);
    $errorLogs = array_slice($errorLogs, -100); // Ambil 100 baris terakhir
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs - CMS Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .log-container {
            background-color: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            padding: 1rem;
            border-radius: 0.5rem;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .log-line {
            margin: 0;
            padding: 0.125rem 0;
            border-bottom: 1px solid #333;
        }
        
        .log-line:hover {
            background-color: #2d2d2d;
        }
        
        .log-timestamp {
            color: #569cd6;
            font-weight: bold;
        }
        
        .log-error {
            color: #f44336;
        }
        
        .log-warning {
            color: #ff9800;
        }
        
        .log-info {
            color: #2196f3;
        }
        
        .log-success {
            color: #4caf50;
        }
    </style>
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
                            <a class="nav-link active" href="logs.php">
                                <i class="fas fa-file-alt"></i> Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Backend Logs</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshLogs()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearLogs()">
                                <i class="fas fa-trash"></i> Clear Logs
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Log Tabs -->
                <ul class="nav nav-tabs" id="logTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="backend-tab" data-bs-toggle="tab" data-bs-target="#backend-logs" type="button" role="tab">
                            <i class="fas fa-server"></i> Backend Logs
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="error-tab" data-bs-toggle="tab" data-bs-target="#error-logs" type="button" role="tab">
                            <i class="fas fa-exclamation-triangle"></i> Error Logs
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="logTabsContent">
                    <!-- Backend Logs -->
                    <div class="tab-pane fade show active" id="backend-logs" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-list"></i> Backend Application Logs
                                </h5>
                                <span class="badge bg-primary"><?php echo count($logs); ?> lines</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="log-container" id="backendLogContainer">
                                    <?php if (empty($logs)): ?>
                                        <p class="text-muted">No logs available.</p>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <div class="log-line">
                                                <?php 
                                                $logText = htmlspecialchars($log);
                                                // Highlight timestamps
                                                $logText = preg_replace('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', '<span class="log-timestamp">$1</span>', $logText);
                                                // Highlight error keywords
                                                $logText = preg_replace('/(error|Error|ERROR)/', '<span class="log-error">$1</span>', $logText);
                                                // Highlight warning keywords
                                                $logText = preg_replace('/(warning|Warning|WARNING)/', '<span class="log-warning">$1</span>', $logText);
                                                // Highlight info keywords
                                                $logText = preg_replace('/(info|Info|INFO)/', '<span class="log-info">$1</span>', $logText);
                                                // Highlight success keywords
                                                $logText = preg_replace('/(success|Success|SUCCESS)/', '<span class="log-success">$1</span>', $logText);
                                                echo $logText;
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Logs -->
                    <div class="tab-pane fade" id="error-logs" role="tabpanel">
                        <div class="card mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-circle"></i> Error Logs
                                </h5>
                                <span class="badge bg-danger"><?php echo count($errorLogs); ?> lines</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="log-container" id="errorLogContainer">
                                    <?php if (empty($errorLogs)): ?>
                                        <p class="text-muted">No error logs available.</p>
                                    <?php else: ?>
                                        <?php foreach ($errorLogs as $log): ?>
                                            <div class="log-line">
                                                <?php 
                                                $logText = htmlspecialchars($log);
                                                // Highlight timestamps
                                                $logText = preg_replace('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', '<span class="log-timestamp">$1</span>', $logText);
                                                // Highlight error keywords
                                                $logText = preg_replace('/(error|Error|ERROR|failed|Failed|FAILED)/', '<span class="log-error">$1</span>', $logText);
                                                echo $logText;
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Log Controls -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Log Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="logLines" class="form-label">Number of lines to display:</label>
                                    <select class="form-select" id="logLines">
                                        <option value="50">50 lines</option>
                                        <option value="100" selected>100 lines</option>
                                        <option value="200">200 lines</option>
                                        <option value="500">500 lines</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="autoRefresh" class="form-label">Auto refresh interval:</label>
                                    <select class="form-select" id="autoRefresh">
                                        <option value="0">Disabled</option>
                                        <option value="5000">5 seconds</option>
                                        <option value="10000" selected>10 seconds</option>
                                        <option value="30000">30 seconds</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Log Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-primary"><?php echo count($logs); ?></h4>
                                            <small class="text-muted">Backend Log Lines</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-danger"><?php echo count($errorLogs); ?></h4>
                                            <small class="text-muted">Error Log Lines</small>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <small class="text-muted">
                                        Last updated: <?php echo date('Y-m-d H:i:s'); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let autoRefreshInterval = null;
        
        function refreshLogs() {
            location.reload();
        }
        
        function clearLogs() {
            if (confirm('Apakah Anda yakin ingin menghapus semua log? Tindakan ini tidak dapat dibatalkan.')) {
                fetch('api/backend-control.php', {
                    method: 'POST',
                    body: new FormData(Object.assign(new FormData(), { action: 'clean_logs', days: 0 }))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Logs berhasil dibersihkan');
                        location.reload();
                    } else {
                        alert('Gagal membersihkan logs: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
        
        function setupAutoRefresh() {
            const interval = document.getElementById('autoRefresh').value;
            
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
            
            if (interval > 0) {
                autoRefreshInterval = setInterval(refreshLogs, parseInt(interval));
            }
        }
        
        // Event listeners
        document.getElementById('autoRefresh').addEventListener('change', setupAutoRefresh);
        document.getElementById('logLines').addEventListener('change', refreshLogs);
        
        // Auto scroll to bottom of log containers
        document.addEventListener('DOMContentLoaded', function() {
            const containers = document.querySelectorAll('.log-container');
            containers.forEach(container => {
                container.scrollTop = container.scrollHeight;
            });
        });
        
        // Setup initial auto refresh
        setupAutoRefresh();
    </script>
</body>
</html>
