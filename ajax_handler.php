<?php
require_once 'init.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Prevent notices breaking JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json');

// Helper: send JSON and exit
function sendJson($status, $message, $cart_count = null, $wishlist_count = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($cart_count !== null) $response['cart_count'] = $cart_count;
    if ($wishlist_count !== null) $response['wishlist_count'] = $wishlist_count;
    echo json_encode($response);
    exit;
}

// Check login
if (!isset($_SESSION['user_id'])) {
    sendJson('login_required', 'Please login first');
}

$user_id = $_SESSION['user_id'];

// Validate POST
if (empty($_POST['action']) || empty($_POST['product_id'])) {
    sendJson('error', 'Invalid request');
}

$action = $_POST['action'];
$product_id = intval($_POST['product_id']);

// Check product exists
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
if (!$stmt->fetch()) {
    sendJson('error', 'Product not found');
}

// ===== Add to Cart =====
if ($action === 'add_to_cart') {
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id=? AND product_id=?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $newQty = $item['quantity'] + 1;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity=? WHERE id=?");
        $stmt->execute([$newQty, $item['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $product_id]);
    }

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id=?");
    $stmt->execute([$user_id]);
    $cart_count = (int)$stmt->fetchColumn();

    sendJson('success', 'Added to Cart!', $cart_count);
}

// ===== Add to Wishlist =====
if ($action === 'add_to_wishlist') {
    $stmt = $pdo->prepare("SELECT id FROM wishlist_items WHERE user_id=? AND product_id=?");
    $stmt->execute([$user_id, $product_id]);

    if ($stmt->fetch()) {
        sendJson('error', 'Already in Wishlist!');
    }

    $stmt = $pdo->prepare("INSERT INTO wishlist_items (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $product_id]);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist_items WHERE user_id=?");
    $stmt->execute([$user_id]);
    $wishlist_count = (int)$stmt->fetchColumn();

    sendJson('success', 'Added to Wishlist!', null, $wishlist_count);
}

// ===== Remove from Wishlist =====
if ($action === 'remove_from_wishlist') {
    $stmt = $pdo->prepare("DELETE FROM wishlist_items WHERE user_id=? AND product_id=?");
    $stmt->execute([$user_id, $product_id]);

    // Get updated wishlist count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist_items WHERE user_id=?");
    $stmt->execute([$user_id]);
    $wishlist_count = (int)$stmt->fetchColumn();

    sendJson('success', 'Removed from Wishlist!', null, $wishlist_count);
}

// Fallback
sendJson('error', 'Invalid action');
?>
