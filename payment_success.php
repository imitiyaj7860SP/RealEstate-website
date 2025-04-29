<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

    // Get property details
    $property_id = isset($_GET['property_id']) ? $_GET['property_id'] : null;
    
    if (!$property_id) {
        header("Location: index.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$property_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        header("Location: index.php");
        exit;
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Real Estate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg w-full p-4 flex justify-between items-center text-gray-800">
        <h1 class="text-2xl font-light">Real Estate Registry</h1>
        <div class="flex items-center space-x-6">
            <ul class="flex space-x-6 text-sm">
                <li class="font-bold border-b-2 border-transparent transition duration-300 transform hover:scale-150"><a href="index.php">Home</a></li>
                <li class="font-bold border-b-2 border-transparent transition duration-300 transform hover:scale-150"><a href="buy.php">Buy</a></li>
                <li class="border-b-2 font-bold border-transparent transition duration-300 transform hover:scale-150"><a href="properties.php?type=rent">Rent</a></li>
                <li class="border-b-2 border-transparent font-bold transition duration-300 transform hover:scale-150"><a href="sell.php">Sell</a></li>
                <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="contactus.php">Contact Us</a></li>
            </ul>
            <div class="flex items-center space-x-4">
                <span class="text-gray-800">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-8 px-4">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="p-8 text-center">
                <div class="mb-8">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Payment Successful!</h1>
                <p class="text-gray-600 mb-8">Thank you for your purchase. Your transaction has been completed successfully.</p>
                
                <!-- Property Summary -->
                <div class="max-w-md mx-auto mb-8 p-4 bg-gray-50 rounded-lg text-left">
                    <h2 class="text-lg font-semibold mb-2">Purchase Summary</h2>
                    <p class="text-gray-600">Property: <?php echo htmlspecialchars($property['title']); ?></p>
                    <p class="text-gray-600">Location: <?php echo htmlspecialchars($property['location']); ?></p>
                    <p class="text-xl font-bold text-green-600 mt-2">Amount Paid: ₹<?php echo number_format($property['price']); ?></p>
                </div>
                
                <div class="space-x-4">
                    <a href="index.php" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-300">
                        Return to Home
                    </a>
                    <a href="buy.php" class="inline-block bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-300">
                        Browse More Properties
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 