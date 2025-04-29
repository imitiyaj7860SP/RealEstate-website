<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $short_description = $_POST['short_description'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $location = $_POST['location'];
    $map_link = $_POST['map_link'];
    $property_type = $_POST['property_type'];
    $owner_name = $_POST['owner_name'];
    $contact_info = $_POST['contact_info'];
    $road_access = $_POST['road_access'];
    $utilities = $_POST['utilities'];
    $nearby_landmarks = $_POST['nearby_landmarks'];
    
    // Handle file uploads
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $title_deed = "";
    $encumbrance = "";
    $tax_receipt = "";
    $property_image = "";

    // Function to handle file upload
    function uploadFile($file, $prefix) {
        global $upload_dir;
        $target_file = $upload_dir . $prefix . "_" . time() . "_" . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        }
        return "";
    }

    if (isset($_FILES['title_deed'])) {
        $title_deed = uploadFile($_FILES['title_deed'], "deed");
    }
    if (isset($_FILES['encumbrance'])) {
        $encumbrance = uploadFile($_FILES['encumbrance'], "encum");
    }
    if (isset($_FILES['tax_receipt'])) {
        $tax_receipt = uploadFile($_FILES['tax_receipt'], "tax");
    }
    if (isset($_FILES['property_image'])) {
        $property_image = uploadFile($_FILES['property_image'], "prop");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO properties (user_id, title, short_description, price, area, location, map_link, 
            property_type, owner_name, contact_info, road_access, utilities, nearby_landmarks, 
            title_deed, encumbrance, tax_receipt, property_image, verification_status, status, listing_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'available', 'sale')");
        
        $stmt->execute([$user_id, $title, $short_description, $price, $area, $location, $map_link, 
            $property_type, $owner_name, $contact_info, $road_access, $utilities, $nearby_landmarks, 
            $title_deed, $encumbrance, $tax_receipt, $property_image]);

        header("Location: index.php?success=1");
        exit;
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
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
        <h2 class="text-3xl font-semibold mb-6">List Your Property</h2>
        
        <div class="max-w-4xl mx-auto mt-8 px-4 mb-12">
            <div class="bg-white rounded-lg shadow-xl p-6 border border-gray-200">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">List Your Property</h2>

                <?php if ($success): ?>
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4 text-blue-700">Basic Information</h3>
                        </div>
                        
                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Property Title</label>
                            <input type="text" name="title" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Short Description</label>
                            <textarea name="short_description" required rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300"></textarea>
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" name="price" required min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Area (sq ft)</label>
                            <input type="number" name="area" required min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" name="location" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Map Link</label>
                            <input type="url" name="map_link"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <!-- Property Details -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4 text-blue-700 pt-2">Property Details</h3>
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Property Type</label>
                            <select name="property_type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                                <option value="apartment">Apartment</option>
                                <option value="house">House</option>
                                <option value="villa">Villa</option>
                                <option value="land">Land</option>
                            </select>
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                            <input type="text" name="owner_name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Contact Information</label>
                            <input type="text" name="contact_info" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Utilities</label>
                            <input type="text" name="utilities"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Nearby Landmarks</label>
                            <input type="text" name="nearby_landmarks"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>

                        <div class="flex items-center transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <input type="checkbox" name="road_access" id="road_access"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition duration-300">
                            <label for="road_access" class="ml-2 block text-sm text-gray-700">
                                Road Access Available
                            </label>
                        </div>

                        <!-- Documents -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4 text-blue-700 pt-2">Documents</h3>
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Title Deed</label>
                            <input type="file" name="title_deed" accept=".pdf,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Encumbrance Certificate</label>
                            <input type="file" name="encumbrance" accept=".pdf,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Tax Receipt</label>
                            <input type="file" name="tax_receipt" accept=".pdf,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition duration-300">
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Property Images</label>
                            <input type="file" name="property_image" accept="image/*" required
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition duration-300">
                        </div>

                        <!-- Verification -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4 text-blue-700 pt-2">Verification</h3>
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Verification Status</label>
                            <select name="verification_status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="transition duration-300 hover:shadow-md p-3 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Verified By</label>
                            <input type="text" name="verified_by"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-300">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-1">
                            List Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>