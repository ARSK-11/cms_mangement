// CMS Management JavaScript
class BackendController {
    constructor() {
        this.autoRefreshInterval = null;
        this.currentBackendId = null;
    }

    init() {
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    setupEventListeners() {
        // Setup any additional event listeners here
    }

    startAutoRefresh() {
        this.stopAutoRefresh();
        this.autoRefreshInterval = setInterval(() => {
            this.refreshStatus();
        }, 5000); // Refresh every 5 seconds
    }

    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }

    async makeRequest(action, data = {}) {
        const formData = new FormData();
        formData.append('action', action);
        
        // Add backend_id if not specified
        if (!data.backend_id && this.currentBackendId) {
            formData.append('backend_id', this.currentBackendId);
        }
        
        // Add other data
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });

        try {
            const response = await fetch('api/backend-control.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    }

    async startBackend(backendId = null) {
        if (backendId) this.currentBackendId = backendId;
        
        this.showLoading('Starting backend...');
        
        try {
            const result = await this.makeRequest('start', { backend_id: this.currentBackendId });
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.refreshStatus();
            } else {
                this.showNotification(result.message, 'error');
            }
        } catch (error) {
            this.showNotification(`Error starting backend: ${error.message}`, 'error');
        } finally {
            this.hideLoading();
        }
    }

    async stopBackend(backendId = null) {
        if (backendId) this.currentBackendId = backendId;
        
        this.showLoading('Stopping backend...');
        
        try {
            const result = await this.makeRequest('stop', { backend_id: this.currentBackendId });
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.refreshStatus();
            } else {
                this.showNotification(result.message, 'error');
            }
        } catch (error) {
            this.showNotification(`Error stopping backend: ${error.message}`, 'error');
        } finally {
            this.hideLoading();
        }
    }

    async restartBackend(backendId = null) {
        if (backendId) this.currentBackendId = backendId;
        
        this.showLoading('Restarting backend...');
        
        try {
            const result = await this.makeRequest('restart', { backend_id: this.currentBackendId });
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.refreshStatus();
            } else {
                this.showNotification(result.message, 'error');
            }
        } catch (error) {
            this.showNotification(`Error restarting backend: ${error.message}`, 'error');
        } finally {
            this.hideLoading();
        }
    }

    async installDependencies(backendId = null) {
        if (backendId) this.currentBackendId = backendId;
        
        this.showLoading('Installing dependencies...');
        
        try {
            const result = await this.makeRequest('install_dependencies', { backend_id: this.currentBackendId });
            
            if (result.success) {
                this.showNotification(result.message, 'success');
            } else {
                this.showNotification(result.message, 'error');
            }
        } catch (error) {
            this.showNotification(`Error installing dependencies: ${error.message}`, 'error');
        } finally {
            this.hideLoading();
        }
    }

    async killProcess(pid) {
        this.showLoading('Killing process...');
        
        try {
            const result = await this.makeRequest('kill_process', { pid: pid });
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                this.refreshStatus();
            } else {
                this.showNotification(result.message, 'error');
            }
        } catch (error) {
            this.showNotification(`Error killing process: ${error.message}`, 'error');
        } finally {
            this.hideLoading();
        }
    }

    async refreshStatus(backendId = null) {
        if (backendId) this.currentBackendId = backendId;
        
        try {
            const result = await this.makeRequest('status', { backend_id: this.currentBackendId });
            
            if (result.success) {
                this.updateStatusDisplay(result.data);
            } else {
                console.error('Failed to refresh status:', result.message);
            }
        } catch (error) {
            console.error('Error refreshing status:', error);
        }
    }

    updateStatusDisplay(data) {
        // Update backend status
        const statusElement = document.getElementById('backendStatus');
        if (statusElement) {
            statusElement.innerHTML = data.status ? 
                '<span class="text-success">Running</span>' : 
                '<span class="text-danger">Stopped</span>';
        }

        // Update port status
        const portElement = document.getElementById('portStatus');
        if (portElement) {
            portElement.innerHTML = data.port_open ? 
                '<span class="text-success">Open</span>' : 
                '<span class="text-danger">Closed</span>';
        }

        // Update process count
        const processElement = document.getElementById('processCount');
        if (processElement) {
            processElement.textContent = data.processes ? data.processes.length : 0;
        }

        // Update process table
        this.updateProcessTable(data.processes || []);
    }

    updateProcessTable(processes) {
        const tableBody = document.getElementById('processTable');
        if (!tableBody) return;

        if (processes.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada proses yang aktif</td></tr>';
            return;
        }

        tableBody.innerHTML = processes.map(process => `
            <tr>
                <td>${process.pid}</td>
                <td>${process.name}</td>
                <td>${process.script}</td>
                <td>${process.port}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="killProcess(${process.pid})">
                        <i class="fas fa-times"></i> Kill
                    </button>
                </td>
            </tr>
        `).join('');
    }

    showLoading(message) {
        const overlay = document.getElementById('loadingOverlay');
        const messageElement = document.getElementById('loadingMessage');
        
        if (overlay) {
            if (messageElement) messageElement.textContent = message;
            overlay.style.display = 'flex';
        }
    }

    hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notificationContainer');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `alert alert-${this.getAlertType(type)} alert-dismissible fade show notification`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        container.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    getAlertType(type) {
        switch (type) {
            case 'success': return 'success';
            case 'error': return 'danger';
            case 'warning': return 'warning';
            default: return 'info';
        }
    }
}

// Global functions for onclick handlers
function startBackend(backendId) { 
    if (window.backendController) {
        window.backendController.startBackend(backendId);
    }
}

function stopBackend(backendId) { 
    if (window.backendController) {
        window.backendController.stopBackend(backendId);
    }
}

function restartBackend(backendId) { 
    if (window.backendController) {
        window.backendController.restartBackend(backendId);
    }
}

function installDependencies(backendId) { 
    if (window.backendController) {
        window.backendController.installDependencies(backendId);
    }
}

function killProcess(pid) { 
    if (window.backendController) {
        window.backendController.killProcess(pid);
    }
}

function refreshStatus(backendId) { 
    if (window.backendController) {
        window.backendController.refreshStatus(backendId);
    }
}

// Initialize controller when DOM is loaded
let backendController;
document.addEventListener('DOMContentLoaded', () => {
    backendController = new BackendController();
    backendController.init();
    
    // Set current backend ID from URL parameter or default
    const urlParams = new URLSearchParams(window.location.search);
    const backendId = urlParams.get('backend') || 'backend1';
    backendController.currentBackendId = backendId;
    
    backendController.refreshStatus();
});
