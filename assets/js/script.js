// CMS Management JavaScript
class BackendController {
    constructor() {
        this.apiUrl = 'api/backend-control.php';
        this.refreshInterval = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    setupEventListeners() {
        // Setup refresh button
        const refreshBtn = document.querySelector('[onclick="refreshStatus()"]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.refreshStatus());
        }
    }

    startAutoRefresh() {
        // Auto refresh setiap 10 detik
        this.refreshInterval = setInterval(() => {
            this.refreshStatus();
        }, 10000);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    async makeRequest(action, data = {}) {
        try {
            const formData = new FormData();
            formData.append('action', action);
            
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }

            const response = await fetch(this.apiUrl, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            return {
                success: false,
                message: 'Request gagal: ' + error.message
            };
        }
    }

    async startBackend() {
        this.showLoading('Menjalankan backend...');
        
        const result = await this.makeRequest('start');
        
        this.hideLoading();
        this.showNotification(result.message, result.success ? 'success' : 'error');
        
        if (result.success) {
            setTimeout(() => this.refreshStatus(), 2000);
        }
    }

    async stopBackend() {
        if (!confirm('Apakah Anda yakin ingin menghentikan backend?')) {
            return;
        }

        this.showLoading('Menghentikan backend...');
        
        const result = await this.makeRequest('stop');
        
        this.hideLoading();
        this.showNotification(result.message, result.success ? 'success' : 'error');
        
        if (result.success) {
            setTimeout(() => this.refreshStatus(), 2000);
        }
    }

    async restartBackend() {
        if (!confirm('Apakah Anda yakin ingin restart backend?')) {
            return;
        }

        this.showLoading('Restarting backend...');
        
        const result = await this.makeRequest('restart');
        
        this.hideLoading();
        this.showNotification(result.message, result.success ? 'success' : 'error');
        
        if (result.success) {
            setTimeout(() => this.refreshStatus(), 3000);
        }
    }

    async killProcess(pid) {
        if (!confirm(`Apakah Anda yakin ingin menghentikan proses PID ${pid}?`)) {
            return;
        }

        this.showLoading('Menghentikan proses...');
        
        const result = await this.makeRequest('kill_process', { pid: pid });
        
        this.hideLoading();
        this.showNotification(result.message, result.success ? 'success' : 'error');
        
        if (result.success) {
            setTimeout(() => this.refreshStatus(), 1000);
        }
    }

    async refreshStatus() {
        const result = await this.makeRequest('status');
        
        if (result.success) {
            this.updateStatusDisplay(result);
        }
    }

    updateStatusDisplay(data) {
        // Update status badge
        const statusBadge = document.querySelector('.badge');
        if (statusBadge) {
            statusBadge.textContent = data.status ? 'Running' : 'Stopped';
            statusBadge.className = `badge ${data.status ? 'bg-success' : 'bg-danger'}`;
        }

        // Update port status
        const portBadge = document.querySelector('.badge:last-child');
        if (portBadge) {
            portBadge.textContent = `Port ${window.BACKEND_PORT || 3000}`;
            portBadge.className = `badge ${data.port_open ? 'bg-success' : 'bg-warning'}`;
        }

        // Update process count
        const processCount = document.querySelector('.badge.bg-info');
        if (processCount) {
            processCount.textContent = data.processes.length;
        }

        // Update process table
        this.updateProcessTable(data.processes);
    }

    updateProcessTable(processes) {
        const tbody = document.querySelector('tbody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (processes.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="6" class="text-center text-muted">No active backend processes found.</td>';
            tbody.appendChild(row);
        } else {
            processes.forEach(process => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${process.pid}</td>
                    <td>${process.command}</td>
                    <td>${process.cpu}%</td>
                    <td>${process.memory}%</td>
                    <td><span class="badge bg-success">Running</span></td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="backendController.killProcess(${process.pid})">
                            <i class="fas fa-times"></i> Kill
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
    }

    showLoading(message) {
        // Create loading overlay
        let overlay = document.getElementById('loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">${message}</p>
                </div>
            `;
            document.body.appendChild(overlay);
        } else {
            overlay.querySelector('p').textContent = message;
        }
        overlay.style.display = 'flex';
    }

    hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    showNotification(message, type = 'info') {
        // Create notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Global functions untuk onclick handlers
function startBackend() {
    backendController.startBackend();
}

function stopBackend() {
    backendController.stopBackend();
}

function restartBackend() {
    backendController.restartBackend();
}

function killProcess(pid) {
    backendController.killProcess(pid);
}

function refreshStatus() {
    backendController.refreshStatus();
}

// Initialize controller when DOM is loaded
let backendController;
document.addEventListener('DOMContentLoaded', () => {
    backendController = new BackendController();
    
    // Set global variables
    window.BACKEND_PORT = 3000; // Sesuaikan dengan konfigurasi
    
    // Initial status check
    backendController.refreshStatus();
});
