<?php
session_start();

// Check if user is logged in and is a government official
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Government Official') {
    header("Location: index.php");
    exit;
}

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

// Handle verification action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id']) && isset($_POST['action'])) {
    $property_id = $_POST['property_id'];
    $action = $_POST['action'];
    $verified_by = $_SESSION['user_name'];
    
    try {
        $stmt = $conn->prepare("UPDATE properties SET verification_status = ?, verified_by = ? WHERE id = ?");
        $stmt->execute([$action, $verified_by, $property_id]);
        header("Location: verify.php?success=1");
        exit;
    } catch(PDOException $e) {
        $error = "Verification failed: " . $e->getMessage();
    }
}

// Fetch pending properties
try {
    $stmt = $conn->prepare("SELECT * FROM properties WHERE verification_status = 'Pending' ORDER BY created_at DESC");
    $stmt->execute();
    $pending_properties = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Properties - Land Real Estate Registry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-green-600">Land Registry</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            Property verification status updated successfully!
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Pending Property Verifications</h2>
            
            <?php if (empty($pending_properties)): ?>
            <div class="text-center py-8">
                <p class="text-gray-600 text-lg">No properties pending verification.</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($pending_properties as $property): ?>
                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="p-4">
                        <h4 class="font-semibold text-lg mb-2"><?= htmlspecialchars($property['title']) ?></h4>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($property['short_description']) ?></p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Property ID:</span>
                                <span class="font-medium"><?= htmlspecialchars($property['property_id']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Location:</span>
                                <span class="font-medium"><?= htmlspecialchars($property['location']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Price:</span>
                                <span class="text-green-600 font-bold">₹<?= number_format($property['price']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Type:</span>
                                <span class="font-medium"><?= htmlspecialchars($property['property_type']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Owner:</span>
                                <span class="font-medium"><?= htmlspecialchars($property['owner_name']) ?></span>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <h5 class="font-semibold mb-2">Documents:</h5>
                            <div class="space-y-2">
                                <?php if ($property['title_deed_path']): ?>
                                <a href="<?= htmlspecialchars($property['title_deed_path']) ?>" target="_blank"
                                   class="block text-blue-600 hover:underline">View Title Deed</a>
                                <?php endif; ?>
                                <?php if ($property['encumbrance_path']): ?>
                                <a href="<?= htmlspecialchars($property['encumbrance_path']) ?>" target="_blank"
                                   class="block text-blue-600 hover:underline">View Encumbrance Certificate</a>
                                <?php endif; ?>
                                <?php if ($property['tax_receipt_path']): ?>
                                <a href="<?= htmlspecialchars($property['tax_receipt_path']) ?>" target="_blank"
                                   class="block text-blue-600 hover:underline">View Tax Receipt</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end space-x-2">
                            <form method="POST" class="inline">
                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                <input type="hidden" name="action" value="Approved">
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                    Approve
                                </button>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                <input type="hidden" name="action" value="Rejected">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-8 py-4">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> Land Real Estate Registry. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 