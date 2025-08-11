<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Cek status backend
$backendStatus = checkBackendStatus();
$backendProcesses = getBackendProcesses();
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
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">CMS Management</h4>
                        <p class="text-muted">Node.js Backend Controller</p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logs.php">
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
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshStatus()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Backend Status</h5>
                                        <p class="card-text">
                                            <span class="badge <?php echo $backendStatus ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $backendStatus ? 'Running' : 'Stopped'; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <i class="fas fa-server fa-2x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Active Processes</h5>
                                        <p class="card-text">
                                            <span class="badge bg-info"><?php echo count($backendProcesses); ?></span>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <i class="fas fa-tasks fa-2x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Port Status</h5>
                                        <p class="card-text">
                                            <span class="badge <?php echo isPortOpen(BACKEND_PORT) ? 'bg-success' : 'bg-warning'; ?>">
                                                Port <?php echo BACKEND_PORT; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <i class="fas fa-network-wired fa-2x text-warning"></i>
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
                                <h5>Backend Control</h5>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Active Backend Processes</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($backendProcesses)): ?>
                                    <p class="text-muted">No active backend processes found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>PID</th>
                                                    <th>Command</th>
                                                    <th>CPU %</th>
                                                    <th>Memory %</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($backendProcesses as $process): ?>
                                                <tr>
                                                    <td><?php echo $process['pid']; ?></td>
                                                    <td><?php echo $process['command']; ?></td>
                                                    <td><?php echo $process['cpu']; ?>%</td>
                                                    <td><?php echo $process['memory']; ?>%</td>
                                                    <td>
                                                        <span class="badge bg-success">Running</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger" onclick="killProcess(<?php echo $process['pid']; ?>)">
                                                            <i class="fas fa-times"></i> Kill
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
