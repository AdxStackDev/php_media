<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the requested URL
$requestUrl = isset($_GET['url']) ? $_GET['url'] : '';

// Define your routes
$routes = [
    '' => 'login.php',           // Default route
    'login' => 'login.php',
    'plan' => 'plan.php',
    'channel' => 'channel.php',
    'logout' => 'logout.php',
];

// Check if the requested URL matches any defined routes
if (array_key_exists($requestUrl, $routes)) {
    $file = $routes[$requestUrl];
    
    // Check if file exists
    if (file_exists($file)) {
        include $file;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - File Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold mb-4">404</h1>
        <p class="text-xl mb-4">File Not Found</p>
        <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Go to Login</a>
    </div>
</body>
</html>';
    }
} else {
    // Handle 404 error if the route is not found
    header("HTTP/1.0 404 Not Found");
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold mb-4">404</h1>
        <p class="text-xl mb-4">Page Not Found</p>
        <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Go to Login</a>
    </div>
</body>
</html>';
}
?>