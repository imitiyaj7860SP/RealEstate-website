<?php
session_start();
require_once 'db_connection.php';
$isLoggedIn = isset($_SESSION['user_id']);

// Get filter parameters
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$price_range = isset($_GET['price_range']) ? trim($_GET['price_range']) : '';
$property_type = isset($_GET['property_type']) ? trim($_GET['property_type']) : '';

try {
    // Build the query with filters
    $query = "
        SELECT p.*, u.name as listed_by 
        FROM properties p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.status = 'available'
    ";
    
    $params = [];
    
    if ($location) {
        $query .= " AND p.location LIKE ?";
        $params[] = "%$location%";
    }
    
    if ($price_range) {
        switch($price_range) {
            case '0-500000':
                $query .= " AND p.price <= 500000";
                break;
            case '500000-1000000':
                $query .= " AND p.price > 500000 AND p.price <= 1000000";
                break;
            case '1000000-2000000':
                $query .= " AND p.price > 1000000 AND p.price <= 2000000";
                break;
            case '2000000+':
                $query .= " AND p.price > 2000000";
                break;
        }
    }
    
    if ($property_type) {
        $query .= " AND p.property_type = ?";
        $params[] = $property_type;
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    // Execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Your Property - Real Estate Registry</title>
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
            <?php if ($isLoggedIn): ?>
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
        <!-- Search Section -->
        <div class="mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-4">Search Properties</h2>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" class="w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <select name="price_range" class="w-full p-2 border rounded-md">
                            <option value="">Any</option>
                            <option value="0-500000" <?php echo $price_range === '0-500000' ? 'selected' : ''; ?>>Under ₹5,00,000</option>
                            <option value="500000-1000000" <?php echo $price_range === '500000-1000000' ? 'selected' : ''; ?>>₹5,00,000 - ₹10,00,000</option>
                            <option value="1000000-2000000" <?php echo $price_range === '1000000-2000000' ? 'selected' : ''; ?>>₹10,00,000 - ₹20,00,000</option>
                            <option value="2000000+" <?php echo $price_range === '2000000+' ? 'selected' : ''; ?>>Above ₹20,00,000</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                        <select name="property_type" class="w-full p-2 border rounded-md">
                            <option value="">Any</option>
                            <option value="apartment" <?php echo $property_type === 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                            <option value="house" <?php echo $property_type === 'house' ? 'selected' : ''; ?>>House</option>
                            <option value="villa" <?php echo $property_type === 'villa' ? 'selected' : ''; ?>>Villa</option>
                            <option value="land" <?php echo $property_type === 'land' ? 'selected' : ''; ?>>Land</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Property Listings -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($properties as $property): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 hover:shadow-xl">
                <?php if ($property['property_image']): ?>
                    <img src="<?php echo htmlspecialchars($property['property_image']); ?>" 
                         alt="<?php echo htmlspecialchars($property['title']); ?>"
                         class="w-full h-48 object-cover">
                <?php else: ?>
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No image available</span>
                    </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($property['title']); ?></h3>
                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($property['location']); ?></p>
                    <p class="text-green-600 font-semibold mb-2">₹<?php echo number_format($property['price']); ?></p>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-sm text-gray-500">Listed by: <?php echo htmlspecialchars($property['listed_by']); ?></span>
                        <a href="view_property.php?id=<?php echo $property['id']; ?>" 
                           class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($properties)): ?>
            <div class="col-span-3 text-center py-8">
                <p class="text-gray-500">No properties found matching your criteria.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 