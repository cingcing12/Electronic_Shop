<?php
// includes/functions.php

/**
 * Redirect to a URL and exit
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get user info
 */
function get_user($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Find a product by id
 */
function get_product($pdo, $product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Add product to cart
 */
function add_to_cart($pdo, $user_id, $product_id, $qty = 1) {
    // Check if the product is already in the cart
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // If the product is already in the cart, update the quantity
        $newQty = $row['quantity'] + $qty;
        $upd = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $upd->execute([$newQty, $row['id']]);
    } else {
        // If the product is not in the cart, insert it with the given quantity
        $ins = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $ins->execute([$user_id, $product_id, $qty]);
    }
}


/**
 * Add product to wishlist
 */
function add_to_wishlist($pdo, $user_id, $product_id) {
    // Prevent duplicates
    $stmt = $pdo->prepare("SELECT * FROM wishlist_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        $ins = $pdo->prepare("INSERT INTO wishlist_items (user_id, product_id) VALUES (?, ?)");
        $ins->execute([$user_id, $product_id]);
    }
}

/**
 * Get cart items for user
 */
function get_cart_items($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT ci.*, p.name, p.price, p.image
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get wishlist items
 */
function get_wishlist_items($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT wi.*, p.name, p.price, p.image
        FROM wishlist_items wi
        JOIN products p ON wi.product_id = p.id
        WHERE wi.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * Place an order (from cart) for user
 */
function place_order($pdo, $user_id) {
    $cartItems = get_cart_items($pdo, $user_id);
    if (empty($cartItems)) {
        return false;
    }
    // Calculate total
    $total = 0;
    foreach ($cartItems as $ci) {
        $total += $ci['price'] * $ci['quantity'];
    }
    // Insert into orders
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->execute([$user_id, $total]);
    $orderId = $pdo->lastInsertId();
    // Insert each item
    $ins = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $ci) {
        $ins->execute([$orderId, $ci['product_id'], $ci['quantity'], $ci['price']]);
    }
    // Clear cart
    $del = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $del->execute([$user_id]);
    return $orderId;
}

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        if (!is_logged_in()) {
            return false;
        }

        global $pdo;

        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($user && $user['role'] === 'admin');
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

function remove_from_wishlist($pdo, $user_id, $product_id) {
    $stmt = $pdo->prepare("DELETE FROM wishlist_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}



?>
