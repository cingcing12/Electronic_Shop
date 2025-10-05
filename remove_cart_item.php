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

$stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
if ($stmt->execute([$user_id, $product_id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
