<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'app/config/config.php';
require_once 'layouts/lib/Includes.php';
echo "<h2>Testing Layout System</h2>";

// Test 1: Check Layout class
echo "<h3>1. Layout Class</h3>";
if (class_exists('App\Core\Lib\Layout')) {
    echo "✓ Layout class exists<br>";
} else {
    echo "❌ Layout class not found<br>";
    // Try to load it
    require_once DIR_CORE_LIB . 'Layout.php';
    if (class_exists('App\Core\Lib\Layout')) {
        echo "✓ Layout class loaded manually<br>";
    }
}

// Test 2: Check view file
echo "<h3>2. View File</h3>";
$viewFile = DIR_VIEWS . 'auth/login.php';
echo "View file: {$viewFile}<br>";
echo "Exists: " . (file_exists($viewFile) ? 'YES' : 'NO') . "<br>";

// Test 3: Check layout file
echo "<h3>3. Layout File</h3>";
$layoutFile = DIR_VIEWS . 'frontend/frontend.php';
echo "Layout file: {$layoutFile}<br>";
echo "Exists: " . (file_exists($layoutFile) ? 'YES' : 'NO') . "<br>";

// Test 4: Test simple view without layout
echo "<h3>4. Simple View Test</h3>";
$simpleView = DIR_VIEWS . 'auth/login.php';
if (file_exists($simpleView)) {
    echo "Including view directly...<br>";
    include $simpleView;
} else {
    echo "View file not found<br>";
}