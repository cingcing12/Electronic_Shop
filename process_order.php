<?php
require_once 'init.php';
header('Content-Type: application/json');

if(!is_logged_in()){
    echo json_encode(['success' => false, 'error' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'] ?? 'cash';
$address = trim($_POST['address']);
$card_number = $_POST['card_number'] ?? null;
$card_name = $_POST['card_name'] ?? null;

if(empty($address)){
    echo json_encode(['success' => false, 'error' => 'Please enter your delivery address.']);
    exit;
}

// Place order using your existing place_order() function
$orderId = place_order($pdo, $user_id, $payment_method, $address, $card_number, $card_name);

if($orderId){
    // Clear cart_items after order
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);

    echo json_encode(['success' => true, 'orderId' => $orderId]);
} else {
    echo json_encode(['success' => false, 'error' => 'Cart is empty or order could not be placed.']);
}
