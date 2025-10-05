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
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add to cart.']);
}

function add_to_cart($pdo, $user_id, $product_id, $qty = 1) {
    // Check if the product exists in the database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        return false; // Product not found
    }

    // Check if the product is already in the cart
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // If the product is already in the cart, update the quantity
        $newQty = $item['quantity'] + $qty;
        $upd = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $upd->execute([$newQty, $item['id']]);
    } else {
        // If the product is not in the cart, insert it
        $ins = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $ins->execute([$user_id, $product_id, $qty]);
    }

    return true;
}
?>
