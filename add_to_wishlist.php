<?php
require_once 'init.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in to add items to the wishlist.']);
    exit;
}

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID.']);
    exit;
}

// Call the add_to_wishlist function
if (add_to_wishlist($pdo, $_SESSION['user_id'], $product_id)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add to wishlist.']);
}

function add_to_wishlist($pdo, $user_id, $product_id) {
    // Check if the product exists in the database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        return false; // Product not found
    }

    // Check if the product is already in the wishlist
    $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        return false; // Product already in the wishlist
    } else {
        // If the product is not in the wishlist, insert it
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $product_id]);
    }
}
?>
