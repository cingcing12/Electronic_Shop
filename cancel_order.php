<?php
require_once 'init.php';
header('Content-Type: application/json');

if(!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success'=>false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id']);

// Check if order belongs to user and is pending
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=? AND status='pending'");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if($order){
    $update = $pdo->prepare("UPDATE orders SET status='cancelled' WHERE id=?");
    $update->execute([$order_id]);
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
