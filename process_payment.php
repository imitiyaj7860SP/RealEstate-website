<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['property_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Get property details and ensure it's available
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND status = 'available'");
    $stmt->execute([$_POST['property_id']]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        throw new Exception('Property not available');
    }

    // Debug log
    error_log("Processing payment - User ID: " . $_SESSION['user_id'] . ", Property ID: " . $_POST['property_id']);

    // First update the property status and set buyer_id
    $updateQuery = "UPDATE properties SET status = 'sold', buyer_id = :buyer_id WHERE id = :property_id";
    $stmt = $pdo->prepare($updateQuery);
    $params = [
        ':buyer_id' => $_SESSION['user_id'],
        ':property_id' => $_POST['property_id']
    ];
    
    if (!$stmt->execute($params)) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('Failed to update property status: ' . $errorInfo[2]);
    }

    // Verify the update
    $stmt = $pdo->prepare("SELECT status, buyer_id FROM properties WHERE id = ?");
    $stmt->execute([$_POST['property_id']]);
    $updatedProperty = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($updatedProperty['buyer_id'] != $_SESSION['user_id']) {
        throw new Exception('Failed to set buyer_id properly');
    }

    // Then create the purchase record
    $stmt = $pdo->prepare("INSERT INTO purchases (property_id, buyer_id, amount, purchase_date) VALUES (?, ?, ?, NOW())");
    if (!$stmt->execute([$_POST['property_id'], $_SESSION['user_id'], $property['price']])) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('Failed to create purchase record: ' . $errorInfo[2]);
    }

    $pdo->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Property purchased successfully',
        'debug' => [
            'buyer_id' => $_SESSION['user_id'],
            'property_id' => $_POST['property_id'],
            'updated_status' => $updatedProperty['status'],
            'updated_buyer_id' => $updatedProperty['buyer_id']
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => [
            'user_id' => $_SESSION['user_id'],
            'property_id' => $_POST['property_id']
        ]
    ]);
}
?> 