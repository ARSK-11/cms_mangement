<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$uploadedFiles = getUploadedFiles();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload - CMS Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .upload-area.dragover {
            border-color: #007bff;
            background: #e3f2fd;
        }
        
        .upload-area.dragover .upload-icon {
            color: #007bff;
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .file-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .file-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .file-icon {
            font-size: 2rem;
            margin-right: 15px;
        }
        
        .progress {
            height: 20px;
            border-radius: 10px;
        }
        
        .upload-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin: 10px;
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
                            <a class="nav-link active" href="upload.php">
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
                    <h1 class="h2">
                        <i class="fas fa-upload"></i> File Upload
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshFiles()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload Area -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload Files
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="upload-area" id="uploadArea">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h4>Drag & Drop files here</h4>
                                    <p class="text-muted">atau klik untuk memilih file</p>
                                    <input type="file" id="fileInput" multiple style="display: none;" accept=".zip,.tar,.gz,.rar,.js,.json,.txt,.md,.yml,.yaml">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                        <i class="fas fa-folder-open"></i> Choose Files
                                    </button>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            Maksimal ukuran: <?= formatBytes(MAX_UPLOAD_SIZE) ?> | 
                                            Ekstensi yang diizinkan: <?= implode(', ', ALLOWED_EXTENSIONS) ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- Upload Progress -->
                                <div id="uploadProgress" style="display: none;">
                                    <h6 class="mt-3">Upload Progress:</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="uploadStatus">Preparing upload...</small>
                                </div>

                                <!-- Upload Preview -->
                                <div id="uploadPreview" class="mt-3" style="display: none;">
                                    <h6>Files to upload:</h6>
                                    <div id="previewList" class="row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Uploaded Files -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-folder-open"></i> Uploaded Files
                                    <span class="badge bg-secondary ms-2" id="fileCount"><?= count($uploadedFiles) ?></span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="fileList">
                                    <?php if (empty($uploadedFiles)): ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                            <p>Belum ada file yang diupload</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($uploadedFiles as $file): ?>
                                            <div class="file-item" data-filename="<?= htmlspecialchars($file['name']) ?>">
                                                <div class="d-flex align-items-center">
                                                    <div class="file-icon text-primary">
                                                        <i class="fas fa-file"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?= htmlspecialchars($file['name']) ?></h6>
                                                        <small class="text-muted">
                                                            Size: <?= formatBytes($file['size']) ?> | 
                                                            Modified: <?= date('Y-m-d H:i:s', $file['modified']) ?>
                                                        </small>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="downloadFile('<?= htmlspecialchars($file['name']) ?>')">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFile('<?= htmlspecialchars($file['name']) ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
        class FileUploader {
            constructor() {
                this.uploadArea = document.getElementById('uploadArea');
                this.fileInput = document.getElementById('fileInput');
                this.uploadProgress = document.getElementById('uploadProgress');
                this.uploadPreview = document.getElementById('uploadPreview');
                this.previewList = document.getElementById('previewList');
                this.progressBar = document.querySelector('.progress-bar');
                this.uploadStatus = document.getElementById('uploadStatus');
                
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Drag and drop events
                this.uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    this.uploadArea.classList.add('dragover');
                });

                this.uploadArea.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    this.uploadArea.classList.remove('dragover');
                });

                this.uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    this.uploadArea.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    this.handleFiles(files);
                });

                // File input change
                this.fileInput.addEventListener('change', (e) => {
                    this.handleFiles(e.target.files);
                });

                // Click to upload
                this.uploadArea.addEventListener('click', () => {
                    this.fileInput.click();
                });
            }

            handleFiles(files) {
                if (files.length === 0) return;

                this.showPreview(files);
                this.uploadFiles(files);
            }

            showPreview(files) {
                this.previewList.innerHTML = '';
                
                Array.from(files).forEach(file => {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'col-md-3 mb-2';
                    fileDiv.innerHTML = `
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">${file.name}</h6>
                                <small class="text-muted">${this.formatBytes(file.size)}</small>
                            </div>
                        </div>
                    `;
                    this.previewList.appendChild(fileDiv);
                });

                this.uploadPreview.style.display = 'block';
            }

            async uploadFiles(files) {
                this.showProgress();
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    await this.uploadFile(file, i + 1, files.length);
                }

                this.hideProgress();
                this.hidePreview();
                this.refreshFiles();
                this.showNotification('Upload selesai!', 'success');
            }

            async uploadFile(file, current, total) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('action', 'upload_file');

                this.updateProgress((current / total) * 100, `Uploading ${file.name} (${current}/${total})`);

                try {
                    const response = await fetch('api/backend-control.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    
                    if (!result.success) {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    this.showNotification(`Error uploading ${file.name}: ${error.message}`, 'error');
                }
            }

            updateProgress(percentage, status) {
                this.progressBar.style.width = percentage + '%';
                this.progressBar.textContent = Math.round(percentage) + '%';
                this.uploadStatus.textContent = status;
            }

            showProgress() {
                this.uploadProgress.style.display = 'block';
                this.updateProgress(0, 'Preparing upload...');
            }

            hideProgress() {
                this.uploadProgress.style.display = 'none';
            }

            hidePreview() {
                this.uploadPreview.style.display = 'none';
                this.fileInput.value = '';
            }

            formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            showNotification(message, type = 'info') {
                if (window.backendController) {
                    window.backendController.showNotification(message, type);
                }
            }
        }

        // Global functions
        function refreshFiles() {
            location.reload();
        }

        async function deleteFile(filename) {
            if (!confirm(`Yakin ingin menghapus file "${filename}"?`)) {
                return;
            }

            try {
                const response = await fetch('api/backend-control.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_file&filename=${encodeURIComponent(filename)}`
                });

                const result = await response.json();
                
                if (result.success) {
                    document.querySelector(`[data-filename="${filename}"]`).remove();
                    updateFileCount();
                    if (window.backendController) {
                        window.backendController.showNotification(result.message, 'success');
                    }
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                if (window.backendController) {
                    window.backendController.showNotification(`Error: ${error.message}`, 'error');
                }
            }
        }

        function downloadFile(filename) {
            window.open(`uploads/${filename}`, '_blank');
        }

        function updateFileCount() {
            const fileCount = document.querySelectorAll('.file-item').length;
            document.getElementById('fileCount').textContent = fileCount;
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new FileUploader();
        });
    </script>
</body>
</html>
