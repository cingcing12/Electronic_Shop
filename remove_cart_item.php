<?php
require_once 'init.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$product_id = (int)$data['product_id'];

try {
    // Delete the item from the cart
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

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
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
