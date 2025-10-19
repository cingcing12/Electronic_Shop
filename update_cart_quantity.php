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

try {
    // Update the cart item in the database
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute([
        ':quantity' => $quantity,
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'error' => 'Product not found in cart']);
        exit;
    }

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS cart_count FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = (int)($stmt->fetch(PDO::FETCH_ASSOC)['cart_count'] ?? 0);

    echo json_encode([
        'success' => true,
        'cart_count' => $cart_count
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
