<?php
require_once 'init.php';
if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'account.php';
    redirect('login.php');
}
$user = get_user($pdo, $_SESSION['user_id']);

include 'includes/header.php';
?>

<h2>Your Account</h2>
<p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
<p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

<h3>Your Orders</h3>
<?php
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($orders): ?>
  <ul>
  <?php foreach ($orders as $o): ?>
    <li>Order #<?php echo $o['id']; ?> — Amount: <?php echo number_format($o['total_amount'],2); ?> — Status: <?php echo htmlspecialchars($o['status']); ?></li>
  <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p>No orders yet.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
