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
    <title>Terminal - CMS Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .terminal-container {
            background: #1e1e1e;
            color: #f8f8f2;
            border-radius: 8px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.4;
            height: 500px;
            overflow-y: auto;
            position: relative;
        }
        
        .terminal-output {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .terminal-input {
            background: transparent;
            border: none;
            color: #f8f8f2;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            outline: none;
            width: 100%;
            caret-color: #f8f8f2;
        }
        
        .terminal-prompt {
            color: #50fa7b;
            font-weight: bold;
        }
        
        .terminal-command {
            color: #8be9fd;
        }
        
        .terminal-error {
            color: #ff5555;
        }
        
        .terminal-success {
            color: #50fa7b;
        }
        
        .command-history {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .quick-command {
            cursor: pointer;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin: 2px;
            transition: all 0.2s ease;
        }
        
        .quick-command:hover {
            background: #f8f9fa;
            border-color: #007bff;
        }
        
        .working-dir {
            background: #e9ecef;
            border-radius: 4px;
            padding: 8px 12px;
            font-family: monospace;
            font-size: 12px;
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
                            <a class="nav-link active" href="terminal.php">
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
                        <i class="fas fa-terminal"></i> Terminal
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearTerminal()">
                                <i class="fas fa-trash"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportHistory()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Working Directory -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-folder"></i> Working Directory
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="working-dir" id="workingDir">
                                    <?= getcwd() ?>
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="changeWorkingDir()">
                                        <i class="fas fa-folder-open"></i> Change Directory
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terminal -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-terminal"></i> Command Terminal
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="terminal-container" id="terminalContainer">
                                    <div class="terminal-output" id="terminalOutput">
                                        <div class="terminal-prompt">Welcome to CMS Management Terminal</div>
                                        <div class="terminal-prompt">Type 'help' for available commands</div>
                                        <div class="terminal-prompt">Type 'exit' to close terminal</div>
                                        <br>
                                    </div>
                                    <div class="terminal-input-line">
                                        <span class="terminal-prompt">$ </span>
                                        <input type="text" class="terminal-input" id="terminalInput" placeholder="Enter command..." autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Commands -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt"></i> Quick Commands
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6>System Commands:</h6>
                                <div class="quick-commands mb-3">
                                    <span class="quick-command" onclick="executeQuickCommand('dir')">dir</span>
                                    <span class="quick-command" onclick="executeQuickCommand('ls')">ls</span>
                                    <span class="quick-command" onclick="executeQuickCommand('pwd')">pwd</span>
                                    <span class="quick-command" onclick="executeQuickCommand('whoami')">whoami</span>
                                    <span class="quick-command" onclick="executeQuickCommand('date')">date</span>
                                    <span class="quick-command" onclick="executeQuickCommand('time')">time</span>
                                </div>
                                
                                <h6>Node.js Commands:</h6>
                                <div class="quick-commands mb-3">
                                    <span class="quick-command" onclick="executeQuickCommand('node --version')">node --version</span>
                                    <span class="quick-command" onclick="executeQuickCommand('npm --version')">npm --version</span>
                                    <span class="quick-command" onclick="executeQuickCommand('npm list')">npm list</span>
                                    <span class="quick-command" onclick="executeQuickCommand('npm outdated')">npm outdated</span>
                                </div>
                                
                                <h6>Git Commands:</h6>
                                <div class="quick-commands mb-3">
                                    <span class="quick-command" onclick="executeQuickCommand('git --version')">git --version</span>
                                    <span class="quick-command" onclick="executeQuickCommand('git status')">git status</span>
                                    <span class="quick-command" onclick="executeQuickCommand('git branch')">git branch</span>
                                    <span class="quick-command" onclick="executeQuickCommand('git log --oneline -5')">git log</span>
                                </div>
                                
                                <h6>Backend Commands:</h6>
                                <div class="quick-commands">
                                    <?php foreach ($backends as $id => $backend): ?>
                                        <span class="quick-command" onclick="executeQuickCommand('cd <?= $backend['path'] ?> && npm install', '<?= $backend['name'] ?>')">
                                            npm install (<?= $backend['name'] ?>)
                                        </span>
                                        <span class="quick-command" onclick="executeQuickCommand('cd <?= $backend['path'] ?> && npm start', '<?= $backend['name'] ?>')">
                                            npm start (<?= $backend['name'] ?>)
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Command History -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history"></i> Command History
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="command-history" id="commandHistory">
                                    <div class="text-muted">No commands executed yet</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Change Directory Modal -->
    <div class="modal fade" id="changeDirModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Working Directory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newWorkingDir" class="form-label">New Directory Path:</label>
                        <input type="text" class="form-control" id="newWorkingDir" value="<?= getcwd() ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmChangeDir()">Change Directory</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2" id="loadingMessage">Executing command...</p>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="notification-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        class Terminal {
            constructor() {
                this.terminalContainer = document.getElementById('terminalContainer');
                this.terminalOutput = document.getElementById('terminalOutput');
                this.terminalInput = document.getElementById('terminalInput');
                this.commandHistory = [];
                this.historyIndex = -1;
                this.workingDir = '<?= getcwd() ?>';
                
                this.setupEventListeners();
                this.printWelcome();
            }

            setupEventListeners() {
                this.terminalInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        this.executeCommand();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        this.navigateHistory('up');
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        this.navigateHistory('down');
                    }
                });

                this.terminalInput.addEventListener('input', () => {
                    this.scrollToBottom();
                });
            }

            printWelcome() {
                this.printOutput('Welcome to CMS Management Terminal', 'success');
                this.printOutput('Type "help" for available commands', 'info');
                this.printOutput('Type "exit" to close terminal', 'info');
                this.printOutput('');
            }

            async executeCommand() {
                const command = this.terminalInput.value.trim();
                if (!command) return;

                this.printPrompt(command);
                this.terminalInput.value = '';
                this.addToHistory(command);

                if (command.toLowerCase() === 'exit') {
                    this.printOutput('Terminal closed', 'info');
                    return;
                }

                if (command.toLowerCase() === 'help') {
                    this.showHelp();
                    return;
                }

                if (command.toLowerCase() === 'clear') {
                    this.clearTerminal();
                    return;
                }

                try {
                    this.showLoading('Executing command...');
                    
                    const response = await fetch('api/backend-control.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=execute_command&command=${encodeURIComponent(command)}&working_dir=${encodeURIComponent(this.workingDir)}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        this.printOutput(result.data.output || 'Command executed successfully', 'success');
                        if (result.data.working_dir && result.data.working_dir !== this.workingDir) {
                            this.workingDir = result.data.working_dir;
                            this.updateWorkingDir();
                        }
                    } else {
                        this.printOutput(`Error: ${result.message}`, 'error');
                    }
                } catch (error) {
                    this.printOutput(`Error: ${error.message}`, 'error');
                } finally {
                    this.hideLoading();
                }
            }

            printPrompt(command) {
                const promptDiv = document.createElement('div');
                promptDiv.innerHTML = `<span class="terminal-prompt">$ </span><span class="terminal-command">${command}</span>`;
                this.terminalOutput.appendChild(promptDiv);
                this.scrollToBottom();
            }

            printOutput(message, type = 'info') {
                const outputDiv = document.createElement('div');
                outputDiv.className = `terminal-${type}`;
                outputDiv.textContent = message;
                this.terminalOutput.appendChild(outputDiv);
                this.scrollToBottom();
            }

            showHelp() {
                const helpText = `
Available Commands:
- help: Show this help message
- clear: Clear terminal output
- exit: Close terminal
- dir/ls: List directory contents
- pwd: Show current working directory
- cd: Change directory
- node --version: Show Node.js version
- npm --version: Show npm version
- git --version: Show Git version

Quick Commands:
- Click on any quick command button to execute it
- Use arrow keys to navigate command history
- Use Tab for auto-completion (if available)
                `.trim();
                
                this.printOutput(helpText, 'info');
            }

            addToHistory(command) {
                this.commandHistory.push(command);
                this.historyIndex = this.commandHistory.length;
                this.updateHistoryDisplay();
            }

            navigateHistory(direction) {
                if (direction === 'up' && this.historyIndex > 0) {
                    this.historyIndex--;
                } else if (direction === 'down' && this.historyIndex < this.commandHistory.length) {
                    this.historyIndex++;
                }

                if (this.historyIndex >= 0 && this.historyIndex < this.commandHistory.length) {
                    this.terminalInput.value = this.commandHistory[this.historyIndex];
                } else {
                    this.terminalInput.value = '';
                }
            }

            updateHistoryDisplay() {
                const historyContainer = document.getElementById('commandHistory');
                if (this.commandHistory.length === 0) {
                    historyContainer.innerHTML = '<div class="text-muted">No commands executed yet</div>';
                    return;
                }

                const historyHtml = this.commandHistory.slice(-10).reverse().map((cmd, index) => 
                    `<div class="mb-1"><small class="text-muted">${this.commandHistory.length - index}:</small> ${cmd}</div>`
                ).join('');
                
                historyContainer.innerHTML = historyHtml;
            }

            clearTerminal() {
                this.terminalOutput.innerHTML = '';
                this.printWelcome();
            }

            updateWorkingDir() {
                document.getElementById('workingDir').textContent = this.workingDir;
            }

            scrollToBottom() {
                this.terminalContainer.scrollTop = this.terminalContainer.scrollHeight;
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
        }

        // Global functions
        function clearTerminal() {
            if (window.terminal) {
                window.terminal.clearTerminal();
            }
        }

        function executeQuickCommand(command, description = '') {
            if (window.terminal) {
                if (description) {
                    window.terminal.printOutput(`Executing: ${command} (${description})`, 'info');
                }
                window.terminal.terminalInput.value = command;
                window.terminal.executeCommand();
            }
        }

        function changeWorkingDir() {
            const modal = new bootstrap.Modal(document.getElementById('changeDirModal'));
            modal.show();
        }

        function confirmChangeDir() {
            const newDir = document.getElementById('newWorkingDir').value;
            if (newDir && window.terminal) {
                window.terminal.workingDir = newDir;
                window.terminal.updateWorkingDir();
                window.terminal.printOutput(`Working directory changed to: ${newDir}`, 'success');
            }
            bootstrap.Modal.getInstance(document.getElementById('changeDirModal')).hide();
        }

        function exportHistory() {
            if (window.terminal && window.terminal.commandHistory.length > 0) {
                const historyText = window.terminal.commandHistory.join('\n');
                const blob = new Blob([historyText], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'terminal-history.txt';
                a.click();
                URL.revokeObjectURL(url);
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            window.terminal = new Terminal();
        });
    </script>
</body>
</html>
