<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // Get purchased properties (properties where the user is the buyer)
    $stmt = $pdo->prepare("
        SELECT p.*, pur.purchase_date 
        FROM properties p 
        INNER JOIN purchases pur ON p.id = pur.property_id 
        WHERE pur.buyer_id = ? 
        ORDER BY pur.purchase_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $purchased_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get listed properties (properties where the user is the seller and not the buyer)
    $stmt = $pdo->prepare("
        SELECT * FROM properties 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $listed_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Real Estate Registry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-100">
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="flex items-center space-x-4">
                    <a href="profile.php" class="nav-link text-gray-700 hover:text-gray-900 transition duration-300">My Profile</a>
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="nav-link bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-300">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="nav-link bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-300">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">My Profile</h1>

        <!-- Purchased Properties -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">My Purchased Properties</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($purchased_properties as $property): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <?php if ($property['property_image']): ?>
                            <img src="<?php echo htmlspecialchars($property['property_image']); ?>" 
                                 alt="Property Image" 
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($property['location']); ?></p>
                            <p class="text-green-600 font-semibold">Rs. <?php echo number_format($property['price']); ?></p>
                            <p class="text-sm text-gray-500 mt-2">
                                Purchased on: <?php echo date('M d, Y', strtotime($property['purchase_date'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($purchased_properties)): ?>
                    <p class="text-gray-500">No properties purchased yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Listed Properties -->
        <div>
            <h2 class="text-2xl font-semibold mb-4">My Listed Properties</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($listed_properties as $property): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <?php if ($property['property_image']): ?>
                            <img src="<?php echo htmlspecialchars($property['property_image']); ?>" 
                                 alt="Property Image" 
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($property['location']); ?></p>
                            <p class="text-green-600 font-semibold">Rs. <?php echo number_format($property['price']); ?></p>
                            <p class="text-sm text-gray-500 mt-2">
                                Status: <span class="font-semibold"><?php echo ucfirst($property['status']); ?></span>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($listed_properties)): ?>
                    <p class="text-gray-500">No properties listed yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 