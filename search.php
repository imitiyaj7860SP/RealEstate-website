<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Properties - Real Estate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Updated Navigation Bar -->
    <nav class="bg-white shadow-lg w-full p-4 flex justify-between items-center text-gray-800">
        <h1 class="text-2xl font-light">Real Estate Registry</h1>
        <div class="flex items-center space-x-6">
            <ul class="flex space-x-6 text-sm">
                <li class="font-bold border-b-2 border-transparent transition duration-300 transform hover:scale-150"><a href="index.php">Home</a></li>
                <li class="font-bold border-b-2 border-transparent transition duration-300 transform hover:scale-150"><a href="search.php">Buy</a></li>
                <li class="border-b-2 font-bold border-transparent transition duration-300 transform hover:scale-150"><a href="search.php?type=rent">Rent</a></li>
                <li class="border-b-2 border-transparent font-bold transition duration-300 transform hover:scale-150"><a href="sell.php">Sell</a></li>
                <li class="border-b-2 border-transparent font-bold transition duration-300 transform hover:scale-150"><a href="contact.php">Contact</a></li>
            </ul>
            <?php if ($isLoggedIn): ?>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-800">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-300">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-8 px-4">
        // ... rest of the search page content ...
    </div>
</body>
</html> 