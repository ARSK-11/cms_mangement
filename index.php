<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Ambil daftar backend
$backends = getAllBackends();
$currentBackendId = $_GET['backend'] ?? DEFAULT_BACKEND;
$currentBackend = getBackend($currentBackendId);

// Cek status backend yang dipilih
$backendStatus = checkBackendStatus($currentBackendId);
$backendProcesses = getBackendProcesses($currentBackendId);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Management - Node.js Backend Controller</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4><i class="fas fa-server"></i> CMS Management</h4>
                        <p class="text-muted">Node.js Backend Controller</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logs.php">
                                <i class="fas fa-file-alt"></i> Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload.php">
                                <i class="fas fa-upload"></i> File Upload
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="terminal.php">
                                <i class="fas fa-terminal"></i> Terminal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="git.php">
                                <i class="fab fa-git-alt"></i> Git Operations
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
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshStatus()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Backend Selector -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-server"></i> Pilih Backend
                                </h5>
                            </div>
                            <div class="card-body">
                                <select class="form-select" id="backendSelector" onchange="changeBackend(this.value)">
                                    <?php foreach ($backends as $id => $backend): ?>
                                        <option value="<?= $id ?>" <?= $id === $currentBackendId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($backend['name']) ?> (Port: <?= $backend['port'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Status Backend
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="backendStatus">
                                            <?= $backendStatus ? '<span class="text-success">Running</span>' : '<span class="text-danger">Stopped</span>' ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-server fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Port Status
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="portStatus">
                                            <?= isPortOpen($currentBackend['port']) ? '<span class="text-success">Open</span>' : '<span class="text-danger">Closed</span>' ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Active Processes
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="processCount">
                                            <?= count($backendProcesses) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Backend Port
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $currentBackend['port'] ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-plug fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Control Buttons -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-gamepad"></i> Backend Control
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success" onclick="startBackend()">
                                        <i class="fas fa-play"></i> Start Backend
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="stopBackend()">
                                        <i class="fas fa-stop"></i> Stop Backend
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="restartBackend()">
                                        <i class="fas fa-redo"></i> Restart Backend
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="installDependencies()">
                                        <i class="fas fa-download"></i> Install Dependencies
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process List -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list"></i> Active Processes
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>PID</th>
                                                <th>Name</th>
                                                <th>Script</th>
                                                <th>Port</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="processTable">
                                            <?php if (empty($backendProcesses)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Tidak ada proses yang aktif</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($backendProcesses as $process): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($process['pid']) ?></td>
                                                        <td><?= htmlspecialchars($process['name']) ?></td>
                                                        <td><?= htmlspecialchars($process['script']) ?></td>
                                                        <td><?= htmlspecialchars($process['port']) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger" onclick="killProcess(<?= $process['pid'] ?>)">
                                                                <i class="fas fa-times"></i> Kill
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt"></i> Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <a href="logs.php" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-file-alt"></i> View Logs
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="upload.php" class="btn btn-outline-success w-100">
                                            <i class="fas fa-upload"></i> Upload Files
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="terminal.php" class="btn btn-outline-dark w-100">
                                            <i class="fas fa-terminal"></i> Terminal
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="git.php" class="btn btn-outline-info w-100">
                                            <i class="fab fa-git-alt"></i> Git Operations
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2" id="loadingMessage">Processing...</p>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="notification-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        // Global variables
        let currentBackendId = '<?= $currentBackendId ?>';
        let backendController;

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            backendController = new BackendController();
            backendController.init();
            backendController.refreshStatus();
        });

        // Change backend function
        function changeBackend(backendId) {
            currentBackendId = backendId;
            window.location.href = 'index.php?backend=' + backendId;
        }

        // Backend control functions
        function startBackend() {
            backendController.startBackend(currentBackendId);
        }

        function stopBackend() {
            backendController.stopBackend(currentBackendId);
        }

        function restartBackend() {
            backendController.restartBackend(currentBackendId);
        }

        function installDependencies() {
            backendController.installDependencies(currentBackendId);
        }

        function killProcess(pid) {
            backendController.killProcess(pid);
        }

        function refreshStatus() {
            backendController.refreshStatus(currentBackendId);
        }
    </script>
</body>
</html>
