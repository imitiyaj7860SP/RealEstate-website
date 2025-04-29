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

    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ? AND listing_type = 'sale'");
    $stmt->execute([$property_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        header("Location: index.php");
        exit;
    }

    $error = '';
    $success = '';

    // Handle payment submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $card_number = preg_replace('/\s+/', '', $_POST['card_number']);
        $expiry_month = $_POST['expiry_month'];
        $expiry_year = $_POST['expiry_year'];
        $cvv = $_POST['cvv'];
        $card_holder = trim($_POST['card_holder']);

        // Basic validation
        $is_valid = true;
        
        // Validate card number (16 digits)
        if (!preg_match('/^[0-9]{16}$/', $card_number)) {
            $error = "Invalid card number. Must be 16 digits.";
            $is_valid = false;
        }
        
        // Validate expiry date
        $current_year = date('Y');
        $current_month = date('m');
        if ($expiry_year < $current_year || 
            ($expiry_year == $current_year && $expiry_month < $current_month)) {
            $error = "Card has expired.";
            $is_valid = false;
        }
        
        // Validate CVV (3 or 4 digits)
        if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
            $error = "Invalid CVV.";
            $is_valid = false;
        }
        
        // Validate card holder name
        if (empty($card_holder)) {
            $error = "Card holder name is required.";
            $is_valid = false;
        }

        if ($is_valid) {
            // In a real application, you would process the payment here
            // For this dummy version, we'll just mark it as successful
            
            // Update property status or create a transaction record
            try {
                $conn->beginTransaction();
                
                // Check if property is already sold
                $stmt = $conn->prepare("SELECT status FROM properties WHERE id = ?");
                $stmt->execute([$property_id]);
                $current_status = $stmt->fetchColumn();
                
                if ($current_status === 'sold') {
                    throw new Exception("This property has already been sold.");
                }
                
                // Create transaction record
                $stmt = $conn->prepare("INSERT INTO transactions (property_id, buyer_id, amount, status, created_at) VALUES (?, ?, ?, 'completed', NOW())");
                $stmt->execute([$property_id, $_SESSION['user_id'], $property['price']]);
                
                // Update property status and set buyer_id
                $stmt = $conn->prepare("UPDATE properties SET status = 'sold', buyer_id = ? WHERE id = ?");
                $stmt->execute([$_SESSION['user_id'], $property_id]);
                
                // Create purchase record
                $stmt = $conn->prepare("INSERT INTO purchases (property_id, buyer_id, amount, purchase_date) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$property_id, $_SESSION['user_id'], $property['price']]);
                
                $conn->commit();
                
                // Redirect to success page
                header("Location: payment_success.php?property_id=" . $property_id);
                exit;
                
            } catch(Exception $e) {
                $conn->rollBack();
                $error = "Transaction failed: " . $e->getMessage();
            }
        }
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
    <title>Payment - Real Estate</title>
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
                <li class="border-b-2 border-transparent font-bold transition duration-300 transform hover:scale-150"><a href="sell.php">Sell</a></li>
                <li class="nav-link border-b-2 border-transparent font-bold transition duration-300"><a href="contactus.php">Contact Us</a></li>

            </ul>
            <div class="flex items-center space-x-4">
                <a href="profile.php" class="text-gray-800 hover:text-gray-600 border-b-2 border-transparent transition duration-300 transform hover:scale-150">My Profile</a>
                <span class="text-gray-800">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-8 px-4">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="p-8">
                <h1 class="text-2xl font-bold mb-6">Payment Details</h1>
                
                <!-- Property Summary -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <h2 class="text-lg font-semibold mb-2">Purchase Summary</h2>
                    <p class="text-gray-600">Property: <?php echo htmlspecialchars($property['title']); ?></p>
                    <p class="text-gray-600">Location: <?php echo htmlspecialchars($property['location']); ?></p>
                    <p class="text-xl font-bold text-green-600 mt-2">Total Amount: ₹<?php echo number_format($property['price']); ?></p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Payment Form -->
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="card_holder">
                            Card Holder Name
                        </label>
                        <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                               id="card_holder" name="card_holder" type="text" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="card_number">
                            Card Number
                        </label>
                        <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                               id="card_number" name="card_number" type="text" maxlength="19" 
                               pattern="[0-9\s]{13,19}" placeholder="1234 5678 9012 3456" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                Expiry Date
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="expiry_month" class="shadow-sm border border-gray-200 rounded-md py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo sprintf('%02d', $m); ?>"><?php echo sprintf('%02d', $m); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="expiry_year" class="shadow-sm border border-gray-200 rounded-md py-3 px-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <?php for($y = date('Y'); $y <= date('Y') + 10; $y++): ?>
                                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="cvv">
                                CVV
                            </label>
                            <input class="shadow-sm appearance-none border border-gray-200 rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   id="cvv" name="cvv" type="password" maxlength="4" pattern="\d*" required>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-8">
                        <a href="javascript:history.back()" 
                           class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-300">
                            Back
                        </a>
                        <button type="submit" 
                                class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 transition duration-300 text-lg font-semibold">
                            Pay ₹<?php echo number_format($property['price']); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Format card number with spaces
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.replace(/\d{4}(?=.)/g, '$& ');
            e.target.value = formattedValue;
        });
    </script>
</body>
</html> 