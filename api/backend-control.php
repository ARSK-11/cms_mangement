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

switch ($action) {
    case 'start':
        $result = startBackend();
        break;
        
    case 'stop':
        $result = stopBackend();
        break;
        
    case 'restart':
        $result = restartBackend();
        break;
        
    case 'status':
        $result = [
            'success' => true,
            'status' => checkBackendStatus(),
            'processes' => getBackendProcesses(),
            'port_open' => isPortOpen(BACKEND_PORT)
        ];
        break;
        
    case 'kill_process':
        $pid = $_POST['pid'] ?? $_GET['pid'] ?? 0;
        if ($pid) {
            $result = killProcess($pid);
        } else {
            $result = ['success' => false, 'message' => 'PID tidak valid'];
        }
        break;
        
    case 'logs':
        $lines = $_POST['lines'] ?? $_GET['lines'] ?? 100;
        $logs = getBackendLogs($lines);
        $result = [
            'success' => true,
            'logs' => $logs
        ];
        break;
        
    case 'system_info':
        $result = [
            'success' => true,
            'info' => getSystemInfo()
        ];
        break;
        
    case 'clean_logs':
        $days = $_POST['days'] ?? $_GET['days'] ?? 7;
        cleanOldLogs($days);
        $result = [
            'success' => true,
            'message' => "Log lama berhasil dibersihkan (lebih dari $days hari)"
        ];
        break;
        
    default:
        $result = [
            'success' => false,
            'message' => 'Action tidak valid'
        ];
        break;
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
