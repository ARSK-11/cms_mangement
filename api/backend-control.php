<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$backendId = $_POST['backend_id'] ?? $_GET['backend_id'] ?? DEFAULT_BACKEND;

$result = ['success' => false, 'message' => 'Action tidak valid'];

switch ($action) {
    case 'start':
        $result = startBackend($backendId);
        break;
        
    case 'stop':
        $result = stopBackend($backendId);
        break;
        
    case 'restart':
        $result = restartBackend($backendId);
        break;
        
    case 'status':
        $backend = getBackend($backendId);
        if ($backend) {
            $status = checkBackendStatus($backendId);
            $processes = getBackendProcesses($backendId);
            $result = [
                'success' => true,
                'data' => [
                    'backend' => $backend,
                    'status' => $status,
                    'processes' => $processes,
                    'port_open' => isPortOpen($backend['port'])
                ]
            ];
        } else {
            $result = ['success' => false, 'message' => 'Backend tidak ditemukan'];
        }
        break;
        
    case 'kill_process':
        $pid = $_POST['pid'] ?? $_GET['pid'] ?? '';
        if ($pid) {
            if (killProcess($pid)) {
                $result = ['success' => true, 'message' => "Proses PID $pid berhasil dihentikan"];
            } else {
                $result = ['success' => false, 'message' => 'Gagal menghentikan proses'];
            }
        } else {
            $result = ['success' => false, 'message' => 'PID tidak valid'];
        }
        break;
        
    case 'logs':
        $lines = $_POST['lines'] ?? $_GET['lines'] ?? 100;
        $logs = getBackendLogs($lines, $backendId);
        $result = ['success' => true, 'data' => $logs];
        break;
        
    case 'system_info':
        $systemInfo = getSystemInfo();
        $result = ['success' => true, 'data' => $systemInfo];
        break;
        
    case 'clean_logs':
        $days = $_POST['days'] ?? $_GET['days'] ?? 7;
        $deletedCount = cleanOldLogs($days);
        $result = ['success' => true, 'message' => "$deletedCount file log berhasil dihapus"];
        break;
        
    case 'upload_file':
        if (isset($_FILES['file'])) {
            $result = uploadFile($_FILES['file']);
        } else {
            $result = ['success' => false, 'message' => 'Tidak ada file yang diupload'];
        }
        break;
        
    case 'get_uploaded_files':
        $files = getUploadedFiles();
        $result = ['success' => true, 'data' => $files];
        break;
        
    case 'delete_file':
        $filename = $_POST['filename'] ?? $_GET['filename'] ?? '';
        if ($filename) {
            $result = deleteUploadedFile($filename);
        } else {
            $result = ['success' => false, 'message' => 'Nama file tidak valid'];
        }
        break;
        
    case 'clone_repository':
        $repositoryUrl = $_POST['repository_url'] ?? '';
        $targetPath = $_POST['target_path'] ?? '';
        $branch = $_POST['branch'] ?? 'main';
        
        if ($repositoryUrl && $targetPath) {
            $result = cloneRepository($repositoryUrl, $targetPath, $branch);
        } else {
            $result = ['success' => false, 'message' => 'URL repository dan target path harus diisi'];
        }
        break;
        
    case 'pull_repository':
        $repositoryPath = $_POST['repository_path'] ?? '';
        
        if ($repositoryPath) {
            $result = pullRepository($repositoryPath);
        } else {
            $result = ['success' => false, 'message' => 'Path repository harus diisi'];
        }
        break;
        
    case 'install_dependencies':
        $result = installDependencies($backendId);
        break;
        
    case 'get_backends':
        $backends = getAllBackends();
        $backendList = [];
        
        foreach ($backends as $id => $backend) {
            $backendList[] = [
                'id' => $id,
                'name' => $backend['name'],
                'path' => $backend['path'],
                'port' => $backend['port'],
                'script' => $backend['script'],
                'description' => $backend['description'],
                'status' => checkBackendStatus($id),
                'processes' => getBackendProcesses($id)
            ];
        }
        
        $result = ['success' => true, 'data' => $backendList];
        break;
        
    case 'execute_command':
        $command = $_POST['command'] ?? '';
        $workingDir = $_POST['working_dir'] ?? '';
        
        if ($command) {
            $fullCommand = getCommandPrefix();
            if ($workingDir) {
                $fullCommand .= 'cd "' . $workingDir . '" && ';
            }
            $fullCommand .= $command . ' 2>&1';
            
            $output = shell_exec($fullCommand);
            $result = [
                'success' => true,
                'data' => [
                    'command' => $command,
                    'output' => $output,
                    'working_dir' => $workingDir
                ]
            ];
        } else {
            $result = ['success' => false, 'message' => 'Command tidak boleh kosong'];
        }
        break;
        
    default:
        $result = ['success' => false, 'message' => 'Action tidak dikenali: ' . $action];
        break;
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
