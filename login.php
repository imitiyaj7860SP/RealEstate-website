<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'property_db';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = '';
$success = '';
$view = isset($_GET['view']) ? $_GET['view'] : 'login';

// Handle Registration
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
        $view = 'register';
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
        $view = 'register';
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
        $view = 'register';
    } else {
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already exists";
                $view = 'register';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
                $stmt = $conn->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $username, $email, $hashed_password]);
                $success = "Registration successful! Please login.";
                $view = 'login';
            }
        } catch(PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
            $view = 'register';
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    if (empty($email) || empty($password)) {
        $error = "Both email and password are required";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid email or password";
            }
        } catch(PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Real Estate Registry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: scale(1.1);
        }
        body {
            background-image: url('assets/images/real-estate-bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .content-wrapper {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="overlay"></div>
    <div class="content-wrapper">
        <!-- White Navigation Bar -->
        <nav class="bg-white shadow-lg w-full p-4 flex justify-between items-center">
            <h1 class="text-2xl font-light nav-link text-gray-800">Real Estate Registry</h1>
            <div class="flex items-center space-x-6">
                <ul class="flex space-x-6 text-sm">
                    <li class="nav-link font-bold border-b-2 border-transparent transition duration-300"><a href="index.php" class="text-gray-700 hover:text-gray-900">Home</a></li>
                    <li class="nav-link font-bold border-b-2 border-transparent transition duration-300"><a href="buy.php" class="text-gray-700 hover:text-gray-900">Buy</a></li>
                    <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="sell.php" class="text-gray-700 hover:text-gray-900">Sell</a></li>
                    <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="contactus.php">Contact Us</a></li>
                </ul>
                <a href="login.php" class="nav-link bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-300">Login</a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mx-auto mt-8 px-4 flex justify-center">
            <div class="max-w-md w-full bg-white/95 backdrop-blur-sm rounded-lg shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="px-8 pt-8 pb-6 text-center">
                    <h1 class="text-3xl font-bold text-blue-600">RealEstate</h1>
                    <p class="mt-2 text-gray-600"><?php echo ($view == 'login') ? 'Sign in to your account' : 'Create your account'; ?></p>
                </div>

                <!-- Message Display -->
                <?php if ($error): ?>
                    <div class="mx-8 mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="mx-8 mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <div id="login-content" class="px-8 pt-6 pb-8 <?php echo ($view == 'login') ? '' : 'hidden'; ?>">
                    <form method="POST" class="space-y-5">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="login_email">
                                Email
                            </label>
                            <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   id="login_email" name="login_email" type="email" placeholder="your@email.com" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="login_password">
                                Password
                            </label>
                            <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   id="login_password" name="login_password" type="password" placeholder="••••••••" required>
                        </div>
                        <div>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150"
                                    type="submit" name="login">
                                Login
                            </button>
                        </div>
                    </form>
                    <div class="mt-6 text-center text-sm">
                        <a href="?view=register" class="text-blue-600 hover:text-blue-800">New User? Register Here</a>
                    </div>
                </div>

                <!-- Registration Form -->
                <div id="register-content" class="px-8 pt-6 pb-8 <?php echo ($view == 'register') ? '' : 'hidden'; ?>">
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="name">
                                Full Name
                            </label>
                            <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   id="name" name="name" type="text" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
                                Email
                            </label>
                            <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   id="email" name="email" type="email" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="password">
                                Password
                            </label>
                            <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   id="password" name="password" type="password" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="confirm_password">
                                Confirm Password
                            </label>
                            <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   id="confirm_password" name="confirm_password" type="password" required>
                        </div>
                        <div>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150"
                                    type="submit" name="register">
                                Register
                            </button>
                        </div>
                    </form>
                    <div class="mt-6 text-center text-sm">
                        <a href="?view=login" class="text-blue-600 hover:text-blue-800">Already have an account? Login</a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-4 text-center text-xs text-gray-500 border-t">
                    &copy; 2025 RealEstate. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>