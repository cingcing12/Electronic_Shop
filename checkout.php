<?php
require_once 'init.php';
if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'checkout.php';
    redirect('login.php');
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In real system: validate address, payment info, etc.
    $orderId = place_order($pdo, $user_id);
    if ($orderId) {
        echo "<p>Order placed! Your order number: $orderId</p>";
        // You might redirect to order confirmation page
        exit;
    } else {
        echo "<p>Error: Your cart is empty or could not place order.</p>";
    }
}

include 'includes/header.php';
?>

<h2>Checkout</h2>
<p>Click Confirm to place your order.</p>

<form method="post">
  <button type="submit">Confirm Order</button>
</form>

<?php include 'includes/footer.php'; ?>
