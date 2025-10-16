<?php
require_once 'init.php';

$q = trim($_GET['q'] ?? '');

header('Content-Type: application/json');

if($q === ''){
    echo json_encode([]);
    exit;
}

// Fetch products with name like search term (limit 10)
$stmt = $pdo->prepare("SELECT id, name, image FROM products WHERE name LIKE ? LIMIT 10");
$stmt->execute(["%$q%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
