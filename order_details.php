<?php
require_once 'init.php';
if(!is_logged_in() || !isset($_GET['order_id'])){
    echo json_encode(['success'=>false]); exit;
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->execute([$order_id,$user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order) { echo json_encode(['success'=>false]); exit; }

// Fetch items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi
    JOIN products p ON oi.product_id=p.id
    WHERE oi.order_id=?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success'=>true,
    'items'=>$items,
    'total'=>$order['total_amount'],
    'payment_method'=>$order['payment_method'] ?? 'Cash on Delivery',
    'address'=>$order['address'] ?? 'Not specified',
    'status'=>ucfirst($order['status'])
]);
