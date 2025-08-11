<?php
/**
 * Test File untuk CMS Management
 * File ini digunakan untuk mengecek apakah semua komponen CMS berfungsi dengan baik
 */

echo "<h1>CMS Management - System Test</h1>";
echo "<hr>";

// Test 1: Check PHP Version
echo "<h2>1. PHP Version Check</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<span style='color: green;'>✓ PHP version is compatible</span><br>";
} else {
    echo "<span style='color: red;'>✗ PHP version is too old. Required: 7.4+</span><br>";
}

// Test 2: Check Required Extensions
echo "<h2>2. Required Extensions Check</h2>";
$required_extensions = ['json', 'curl', 'openssl', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span style='color: green;'>✓ $ext extension is loaded</span><br>";
    } else {
        echo "<span style='color: red;'>✗ $ext extension is not loaded</span><br>";
    }
}

// Test 3: Check File Permissions
echo "<h2>3. File Permissions Check</h2>";
$files_to_check = [
    'includes/config.php' => 'readable',
    'includes/functions.php' => 'readable',
    'api/backend-control.php' => 'readable',
    'logs' => 'writable'
];

foreach ($files_to_check as $file => $permission) {
    if ($permission === 'readable') {
        if (is_readable($file)) {
            echo "<span style='color: green;'>✓ $file is readable</span><br>";
        } else {
            echo "<span style='color: red;'>✗ $file is not readable</span><br>";
        }
    } elseif ($permission === 'writable') {
        if (is_dir($file)) {
            if (is_writable($file)) {
                echo "<span style='color: green;'>✓ $file directory is writable</span><br>";
            } else {
                echo "<span style='color: red;'>✗ $file directory is not writable</span><br>";
            }
        } else {
            if (is_writable($file)) {
                echo "<span style='color: green;'>✓ $file is writable</span><br>";
            } else {
                echo "<span style='color: red;'>✗ $file is not writable</span><br>";
            }
        }
    }
}

// Test 4: Check Configuration
echo "<h2>4. Configuration Check</h2>";
try {
    require_once 'includes/config.php';
    echo "<span style='color: green;'>✓ Configuration file loaded successfully</span><br>";
    echo "Backend Path: " . BACKEND_PATH . "<br>";
    echo "Backend Port: " . BACKEND_PORT . "<br>";
    echo "Backend Script: " . BACKEND_SCRIPT . "<br>";
    
    // Check if backend path exists
    if (is_dir(BACKEND_PATH)) {
        echo "<span style='color: green;'>✓ Backend path exists</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Backend path does not exist</span><br>";
    }
    
    // Check if backend script exists
    $script_path = BACKEND_PATH . '/' . BACKEND_SCRIPT;
    if (file_exists($script_path)) {
        echo "<span style='color: green;'>✓ Backend script exists</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Backend script does not exist</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Configuration error: " . $e->getMessage() . "</span><br>";
}

// Test 5: Check Functions
echo "<h2>5. Functions Check</h2>";
try {
    require_once 'includes/functions.php';
    echo "<span style='color: green;'>✓ Functions file loaded successfully</span><br>";
    
    // Test OS detection
    $os = getOS();
    echo "Detected OS: $os<br>";
    
    // Test port check
    $port_open = isPortOpen(BACKEND_PORT);
    echo "Port " . BACKEND_PORT . " status: " . ($port_open ? 'Open' : 'Closed') . "<br>";
    
    // Test backend status
    $backend_status = checkBackendStatus();
    echo "Backend status: " . ($backend_status ? 'Running' : 'Stopped') . "<br>";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>✗ Functions error: " . $e->getMessage() . "</span><br>";
}

// Test 6: Check API Endpoint
echo "<h2>6. API Endpoint Check</h2>";
if (file_exists('api/backend-control.php')) {
    echo "<span style='color: green;'>✓ API endpoint exists</span><br>";
    
    // Test API call
    $test_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/api/backend-control.php?action=status';
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($test_url, false, $context);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo "<span style='color: green;'>✓ API is responding correctly</span><br>";
        } else {
            echo "<span style='color: orange;'>⚠ API responded but with invalid JSON</span><br>";
        }
    } else {
        echo "<span style='color: red;'>✗ API is not responding</span><br>";
    }
} else {
    echo "<span style='color: red;'>✗ API endpoint does not exist</span><br>";
}

// Test 7: Check Node.js
echo "<h2>7. Node.js Check</h2>";
$node_version = shell_exec('node --version 2>&1');
if ($node_version && !strpos($node_version, 'command not found')) {
    echo "<span style='color: green;'>✓ Node.js is installed: " . trim($node_version) . "</span><br>";
} else {
    echo "<span style='color: red;'>✗ Node.js is not installed or not in PATH</span><br>";
}

// Test 8: Check System Commands
echo "<h2>8. System Commands Check</h2>";
if (getOS() === 'WIN') {
    $tasklist = shell_exec('tasklist 2>&1');
    if ($tasklist && !strpos($tasklist, 'command not found')) {
        echo "<span style='color: green;'>✓ tasklist command is available</span><br>";
    } else {
        echo "<span style='color: red;'>✗ tasklist command is not available</span><br>";
    }
} else {
    $ps = shell_exec('ps aux 2>&1');
    if ($ps && !strpos($ps, 'command not found')) {
        echo "<span style='color: green;'>✓ ps command is available</span><br>";
    } else {
        echo "<span style='color: red;'>✗ ps command is not available</span><br>";
    }
}

// Test 9: Check Log Directory
echo "<h2>9. Log Directory Check</h2>";
if (!is_dir('logs')) {
    if (mkdir('logs', 0755, true)) {
        echo "<span style='color: green;'>✓ Log directory created successfully</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Failed to create log directory</span><br>";
    }
} else {
    echo "<span style='color: green;'>✓ Log directory exists</span><br>";
    if (is_writable('logs')) {
        echo "<span style='color: green;'>✓ Log directory is writable</span><br>";
    } else {
        echo "<span style='color: red;'>✗ Log directory is not writable</span><br>";
    }
}

// Test 10: Security Check
echo "<h2>10. Security Check</h2>";
$dangerous_functions = ['exec', 'shell_exec', 'system', 'passthru'];
$disabled_functions = explode(',', ini_get('disable_functions'));
$enabled_dangerous = [];

foreach ($dangerous_functions as $func) {
    if (!in_array($func, $disabled_functions)) {
        $enabled_dangerous[] = $func;
    }
}

if (empty($enabled_dangerous)) {
    echo "<span style='color: red;'>✗ All dangerous functions are disabled. CMS may not work properly.</span><br>";
} else {
    echo "<span style='color: green;'>✓ Required functions are available: " . implode(', ', $enabled_dangerous) . "</span><br>";
}

echo "<hr>";
echo "<h2>Test Summary</h2>";
echo "<p>Jika semua test menunjukkan ✓ hijau, maka CMS Management siap digunakan.</p>";
echo "<p>Jika ada test yang menunjukkan ✗ merah, silakan perbaiki masalah tersebut sebelum menggunakan CMS.</p>";

echo "<br><a href='index.php'>Go to Dashboard</a>";
?>
