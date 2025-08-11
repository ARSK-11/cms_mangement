<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$backends = getAllBackends();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git Operations - CMS Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .git-status {
            font-family: monospace;
            font-size: 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .repository-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .repository-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .git-branch {
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .git-status-clean {
            color: #4caf50;
        }
        
        .git-status-dirty {
            color: #ff9800;
        }
        
        .clone-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
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
                            <a class="nav-link" href="index.php">
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
                            <a class="nav-link active" href="git.php">
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
                    <h1 class="h2">
                        <i class="fab fa-git-alt"></i> Git Operations
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshGitStatus()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Git Version Check -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle"></i> Git Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Git Version:</strong>
                                        <span id="gitVersion">Checking...</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Status:</strong>
                                        <span id="gitStatus">Checking...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clone Repository -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-download"></i> Clone Repository
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="cloneForm" class="clone-form">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="repositoryUrl" class="form-label">Repository URL:</label>
                                            <input type="url" class="form-control" id="repositoryUrl" 
                                                   placeholder="https://github.com/username/repository.git" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="targetPath" class="form-label">Target Path:</label>
                                            <input type="text" class="form-control" id="targetPath" 
                                                   placeholder="/path/to/target" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="branch" class="form-label">Branch:</label>
                                            <input type="text" class="form-control" id="branch" 
                                                   placeholder="main" value="main">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-download"></i> Clone Repository
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backend Repositories -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-folder"></i> Backend Repositories
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="backendRepositories">
                                    <?php foreach ($backends as $id => $backend): ?>
                                        <div class="repository-card" data-backend-id="<?= $id ?>">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-2">
                                                        <i class="fas fa-server"></i> <?= htmlspecialchars($backend['name']) ?>
                                                    </h6>
                                                    <p class="text-muted mb-2">
                                                        <i class="fas fa-folder"></i> <?= htmlspecialchars($backend['path']) ?>
                                                    </p>
                                                    <div class="git-info">
                                                        <span class="git-branch" id="branch-<?= $id ?>">Checking...</span>
                                                        <span class="git-status" id="status-<?= $id ?>">Checking...</span>
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="checkGitStatus('<?= $id ?>')">
                                                        <i class="fas fa-sync-alt"></i> Check Status
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="pullRepository('<?= $id ?>')">
                                                        <i class="fas fa-download"></i> Pull
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="installDependencies('<?= $id ?>')">
                                                        <i class="fas fa-cogs"></i> Install Deps
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="git-status mt-3" id="gitStatus-<?= $id ?>" style="display: none;">
                                                <div class="text-muted">Loading git status...</div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Git Operations -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history"></i> Recent Git Operations
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="gitOperations">
                                    <div class="text-muted">No recent operations</div>
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
            <p class="mt-2" id="loadingMessage">Processing Git operation...</p>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="notification-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        class GitManager {
            constructor() {
                this.setupEventListeners();
                this.checkGitVersion();
                this.refreshAllStatus();
            }

            setupEventListeners() {
                document.getElementById('cloneForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.cloneRepository();
                });
            }

            async checkGitVersion() {
                try {
                    const response = await fetch('api/backend-control.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=execute_command&command=git --version'
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        document.getElementById('gitVersion').textContent = result.data.output.trim();
                        document.getElementById('gitStatus').innerHTML = '<span class="text-success">✓ Available</span>';
                    } else {
                        document.getElementById('gitVersion').textContent = 'Not installed';
                        document.getElementById('gitStatus').innerHTML = '<span class="text-danger">✗ Not available</span>';
                    }
                } catch (error) {
                    document.getElementById('gitVersion').textContent = 'Error checking';
                    document.getElementById('gitStatus').innerHTML = '<span class="text-danger">✗ Error</span>';
                }
            }

            async cloneRepository() {
                const repositoryUrl = document.getElementById('repositoryUrl').value;
                const targetPath = document.getElementById('targetPath').value;
                const branch = document.getElementById('branch').value;

                if (!repositoryUrl || !targetPath) {
                    this.showNotification('Repository URL dan Target Path harus diisi', 'error');
                    return;
                }

                this.showLoading('Cloning repository...');

                try {
                    const response = await fetch('api/backend-control.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=clone_repository&repository_url=${encodeURIComponent(repositoryUrl)}&target_path=${encodeURIComponent(targetPath)}&branch=${encodeURIComponent(branch)}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showNotification('Repository berhasil di-clone', 'success');
                        document.getElementById('cloneForm').reset();
                        this.addGitOperation('Clone', repositoryUrl, 'Success');
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    this.showNotification(`Error cloning repository: ${error.message}`, 'error');
                    this.addGitOperation('Clone', repositoryUrl, 'Failed');
                } finally {
                    this.hideLoading();
                }
            }

            async checkGitStatus(backendId) {
                const statusElement = document.getElementById(`gitStatus-${backendId}`);
                statusElement.style.display = 'block';
                statusElement.innerHTML = '<div class="text-muted">Checking git status...</div>';

                try {
                    const response = await fetch('api/backend-control.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=execute_command&command=cd "${this.getBackendPath(backendId)}" && git status`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        const output = result.data.output;
                        statusElement.innerHTML = `<pre>${output}</pre>`;
                        
                        // Update branch and status
                        this.updateBranchInfo(backendId, output);
                    } else {
                        statusElement.innerHTML = '<div class="text-danger">Error checking git status</div>';
                    }
                } catch (error) {
                    statusElement.innerHTML = '<div class="text-danger">Error: ' + error.message + '</div>';
                }
            }

            async pullRepository(backendId) {
                this.showLoading('Pulling repository...');

                try {
                    const response = await fetch('api/backend-control.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=pull_repository&repository_path=${encodeURIComponent(this.getBackendPath(backendId))}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showNotification('Repository berhasil di-pull', 'success');
                        this.addGitOperation('Pull', this.getBackendPath(backendId), 'Success');
                        this.checkGitStatus(backendId);
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    this.showNotification(`Error pulling repository: ${error.message}`, 'error');
                    this.addGitOperation('Pull', this.getBackendPath(backendId), 'Failed');
                } finally {
                    this.hideLoading();
                }
            }

            async installDependencies(backendId) {
                this.showLoading('Installing dependencies...');

                try {
                    const response = await fetch('api/backend-control.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=install_dependencies&backend_id=${backendId}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.showNotification('Dependencies berhasil diinstall', 'success');
                        this.addGitOperation('Install Dependencies', this.getBackendPath(backendId), 'Success');
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    this.showNotification(`Error installing dependencies: ${error.message}`, 'error');
                    this.addGitOperation('Install Dependencies', this.getBackendPath(backendId), 'Failed');
                } finally {
                    this.hideLoading();
                }
            }

            updateBranchInfo(backendId, gitStatus) {
                const branchMatch = gitStatus.match(/On branch (\w+)/);
                const statusMatch = gitStatus.match(/working tree clean/);
                
                const branchElement = document.getElementById(`branch-${backendId}`);
                const statusElement = document.getElementById(`status-${backendId}`);
                
                if (branchMatch) {
                    branchElement.textContent = branchMatch[1];
                }
                
                if (statusMatch) {
                    statusElement.innerHTML = '<span class="git-status-clean">✓ Clean</span>';
                } else {
                    statusElement.innerHTML = '<span class="git-status-dirty">⚠ Modified</span>';
                }
            }

            getBackendPath(backendId) {
                const backends = <?= json_encode($backends) ?>;
                return backends[backendId]?.path || '';
            }

            refreshAllStatus() {
                const backends = <?= json_encode($backends) ?>;
                Object.keys(backends).forEach(backendId => {
                    this.checkGitStatus(backendId);
                });
            }

            addGitOperation(operation, repository, status) {
                const operationsContainer = document.getElementById('gitOperations');
                const operationDiv = document.createElement('div');
                operationDiv.className = 'mb-2 p-2 border rounded';
                operationDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${operation}</strong>: ${repository}
                        </div>
                        <div>
                            <span class="badge ${status === 'Success' ? 'bg-success' : 'bg-danger'}">${status}</span>
                            <small class="text-muted ms-2">${new Date().toLocaleTimeString()}</small>
                        </div>
                    </div>
                `;
                
                operationsContainer.insertBefore(operationDiv, operationsContainer.firstChild);
                
                // Keep only last 10 operations
                const operations = operationsContainer.children;
                if (operations.length > 10) {
                    operationsContainer.removeChild(operations[operations.length - 1]);
                }
            }

            showLoading(message) {
                if (window.backendController) {
                    window.backendController.showLoading(message);
                }
            }

            hideLoading() {
                if (window.backendController) {
                    window.backendController.hideLoading();
                }
            }

            showNotification(message, type = 'info') {
                if (window.backendController) {
                    window.backendController.showNotification(message, type);
                }
            }
        }

        // Global functions
        function refreshGitStatus() {
            if (window.gitManager) {
                window.gitManager.refreshAllStatus();
            }
        }

        function checkGitStatus(backendId) {
            if (window.gitManager) {
                window.gitManager.checkGitStatus(backendId);
            }
        }

        function pullRepository(backendId) {
            if (window.gitManager) {
                window.gitManager.pullRepository(backendId);
            }
        }

        function installDependencies(backendId) {
            if (window.gitManager) {
                window.gitManager.installDependencies(backendId);
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            window.gitManager = new GitManager();
        });
    </script>
</body>
</html>
