<?php
require_once 'init.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in to add items to the cart.']);
    exit;
}

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID.']);
    exit;
}

// Call the add_to_cart function
if (add_to_cart($pdo, $_SESSION['user_id'], $product_id)) {
    // Get current cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS cart_count FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['cart_count'] ?? 0;

    echo json_encode(['success' => true, 'cart_count' => (int)$count]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add to cart.']);
}

function add_to_cart($pdo, $user_id, $product_id, $qty = 1) {
    // Check if the product exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) return false;

    // Check if the product is already in the cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // Update quantity
        $newQty = $item['quantity'] + $qty;
        $upd = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $upd->execute([$newQty, $item['id']]);
    } else {
        // Insert new item
        $ins = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $ins->execute([$user_id, $product_id, $qty]);
    }

    return true;
}
?>
