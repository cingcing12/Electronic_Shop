<?php
require_once 'init.php';

// Make sure user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['product_id'], $data['quantity'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$product_id = (int)$data['product_id'];
$quantity = (int)$data['quantity'];
$user_id = $_SESSION['user_id'];

if ($quantity < 1) {
    $quantity = 1; // enforce minimum quantity
}

// Update the cart item in the database
try {
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute([
        ':quantity' => $quantity,
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);

    // Optionally, check if the row was actually updated:
    if ($stmt->rowCount() === 0) {
        // No row updated, maybe product not in cart — you could insert it or return error
        echo json_encode(['success' => false, 'error' => 'Product not found in cart']);
        exit;
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
