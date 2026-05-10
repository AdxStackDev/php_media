<?php
session_start();

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Hardcoded credentials (in production, use database with hashed passwords)
    $validUsername = "admin";
    $validPassword = "1234";
    
    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        header('Location: plan.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IPTV Player</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">IPTV Player Login</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium mb-2">Username</label>
                <input type="text" id="username" name="username" required
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter username">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter password">
            </div>
            
            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                Login
            </button>
        </form>
        
        <div class="mt-4 text-center text-sm text-gray-400">
            <p>Demo credentials: admin / 1234</p>
        </div>
    </div>
</body>
</html>