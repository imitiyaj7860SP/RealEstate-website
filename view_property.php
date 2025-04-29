<?php
session_start();
require_once 'db_connection.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

try {
    // Get property details
    $stmt = $pdo->prepare("SELECT p.*, u.name as listed_by FROM properties p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
    $stmt->execute([$_GET['id']]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        header("Location: index.php");
        exit;
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> - Real Estate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-6xl mx-auto mt-8 px-4">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Property Image -->
            <div class="relative h-96">
                <?php if ($property['property_image']): ?>
                    <img src="<?php echo htmlspecialchars($property['property_image']); ?>" 
                         alt="<?php echo htmlspecialchars($property['title']); ?>"
                         class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No image available</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Property Details -->
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($property['title']); ?></h1>
                        <p class="text-gray-600"><?php echo htmlspecialchars($property['location']); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">₹<?php echo number_format($property['price']); ?></p>
                        <p class="text-gray-600"><?php echo number_format($property['area']); ?> sq ft</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Description</h2>
                    <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($property['short_description'])); ?></p>
                </div>

                <!-- Property Features -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Property Details</h2>
                        <div class="space-y-3">
                            <p><span class="font-medium">Property Type:</span> <?php echo htmlspecialchars($property['property_type']); ?></p>
                            <p><span class="font-medium">Listed By:</span> <?php echo htmlspecialchars($property['listed_by']); ?></p>
                            <p><span class="font-medium">Owner Name:</span> <?php echo htmlspecialchars($property['owner_name']); ?></p>
                            <p><span class="font-medium">Contact:</span> <?php echo htmlspecialchars($property['contact_info']); ?></p>
                            <p><span class="font-medium">Road Access:</span> <?php echo $property['road_access'] ? 'Available' : 'Not Available'; ?></p>
                            <p><span class="font-medium">Utilities:</span> <?php echo htmlspecialchars($property['utilities']); ?></p>
                            <p><span class="font-medium">Nearby Landmarks:</span> <?php echo htmlspecialchars($property['nearby_landmarks']); ?></p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold mb-4">Verification Details</h2>
                        <div class="space-y-3">
                            <p><span class="font-medium">Status:</span> 
                                <span class="px-2 py-1 rounded text-sm <?php 
                                    echo $property['verification_status'] === 'verified' ? 'bg-green-100 text-green-800' : 
                                        ($property['verification_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); 
                                ?>">
                                    <?php echo ucfirst(htmlspecialchars($property['verification_status'])); ?>
                                </span>
                            </p>
                            <?php if ($property['verified_by']): ?>
                                <p><span class="font-medium">Verified By:</span> <?php echo htmlspecialchars($property['verified_by']); ?></p>
                            <?php endif; ?>
                            <p><span class="font-medium">Listed On:</span> <?php echo date('F j, Y', strtotime($property['created_at'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Documents</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php if ($property['title_deed']): ?>
                            <a href="<?php echo htmlspecialchars($property['title_deed']); ?>" target="_blank" 
                               class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <p class="font-medium">Title Deed</p>
                                <p class="text-sm text-gray-600">Click to view</p>
                            </a>
                        <?php endif; ?>
                        <?php if ($property['encumbrance']): ?>
                            <a href="<?php echo htmlspecialchars($property['encumbrance']); ?>" target="_blank"
                               class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <p class="font-medium">Encumbrance Certificate</p>
                                <p class="text-sm text-gray-600">Click to view</p>
                            </a>
                        <?php endif; ?>
                        <?php if ($property['tax_receipt']): ?>
                            <a href="<?php echo htmlspecialchars($property['tax_receipt']); ?>" target="_blank"
                               class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <p class="font-medium">Tax Receipt</p>
                                <p class="text-sm text-gray-600">Click to view</p>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Map -->
                <?php if ($property['map_link']): ?>
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Location</h2>
                        <div class="aspect-w-16 aspect-h-9">
                            <?php
                            $map_url = $property['map_link'];
                            // Check if it's a Google Maps share link and convert it to embed link if needed
                            if (strpos($map_url, 'maps.google.com') !== false || strpos($map_url, 'google.com/maps') !== false) {
                                if (strpos($map_url, 'output=embed') === false) {
                                    $map_url = str_replace('/maps/', '/maps/embed?', $map_url);
                                    if (strpos($map_url, '?') === false) {
                                        $map_url .= '?output=embed';
                                    } else {
                                        $map_url .= '&output=embed';
                                    }
                                }
                            }
                            ?>
                            <iframe src="<?php echo htmlspecialchars($map_url); ?>" 
                                    width="100%" height="450" style="border:0;" allowfullscreen="" 
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Buy Button -->
                <div class="flex justify-between items-center mt-8">
                    <a href="javascript:history.back()" 
                       class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-300">
                        Back to Properties
                    </a>
                    <?php if ($property['listing_type'] === 'sale' && $property['status'] === 'available'): ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($property['user_id'] !== $_SESSION['user_id']): ?>
                                <button id="buyButton" 
                                        class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 transition duration-300 text-lg font-semibold"
                                        onclick="buyProperty(<?php echo $property['id']; ?>)">
                                    Buy Now
                                </button>
                            <?php else: ?>
                                <span class="text-gray-600">This is your property listing</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php?redirect=view_property.php?id=<?php echo $property['id']; ?>" 
                               class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 transition duration-300 text-lg font-semibold">
                                Login to Buy
                            </a>
                        <?php endif; ?>
                    <?php elseif ($property['status'] === 'sold'): ?>
                        <span class="text-red-600 font-semibold">This property has been sold</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function buyProperty(propertyId) {
        if (confirm('Are you sure you want to buy this property?')) {
            window.location.href = 'payment.php?property_id=' + propertyId;
        }
    }
    </script>
</body>
</html> 