<?php
// admin/orders.php

require_once __DIR__ . '/../init.php';

// Fetch orders
$stmt = $pdo->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<h2>Orders</h2>

<?php foreach ($orders as $order): ?>
  <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
    <strong>Order #<?= $order['id'] ?></strong><br>
    User: <?= htmlspecialchars($order['username']) ?><br>
    Total: $<?= number_format($order['total_amount'], 2) ?><br>
    Status: <?= htmlspecialchars($order['status']) ?><br>
    Date: <?= $order['created_at'] ?><br>

    <h4>Items:</h4>
    <ul>
      <?php
        $stmtItems = $pdo->prepare("
            SELECT oi.*, p.name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmtItems->execute([$order['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as $item):
      ?>
        <li><?= htmlspecialchars($item['name']) ?> — Qty: <?= $item['quantity'] ?> @ $<?= number_format($item['unit_price'], 2) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endforeach; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
