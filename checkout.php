<?php
require_once 'init.php';

if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'checkout.php';
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image 
    FROM cart_items c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
$titleName = "Checkout";
include 'includes/header.php';
?>

<div class="container my-5">
  <div class="row">
    <!-- Checkout form -->
    <div class="col-md-6">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body">
          <h4 class="card-title mb-4 text-primary">🧾 Checkout Details</h4>

          <form id="checkoutForm" method="post">
            <div class="mb-3">
              <label for="address" class="form-label fw-bold">Shipping Address</label>
              <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your delivery address..." required></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Payment Method</label>
              <select name="payment_method" id="payment_method" class="form-select" required>
                <option value="cash">💵 Cash on Delivery</option>
                <option value="card">💳 Credit / Debit Card</option>
              </select>
            </div>

            <div id="cardFields" style="display:none;">
              <div class="mb-3">
                <label for="card_number" class="form-label fw-bold">Card Number</label>
                <input type="text" class="form-control" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
              </div>
              <div class="mb-3">
                <label for="card_name" class="form-label fw-bold">Cardholder Name</label>
                <input type="text" class="form-control" id="card_name" name="card_name" placeholder="Name on Card">
              </div>
            </div>

            <button type="submit" class="btn btn-success w-100 mt-3 py-2 fs-5">Confirm & Place Order</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Cart summary -->
    <div class="col-md-6">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body">
          <h4 class="card-title mb-4 text-primary">🛒 Order Summary</h4>
          <?php if (empty($cartItems)): ?>
            <p>Your cart is empty.</p>
          <?php else: ?>
            <?php foreach ($cartItems as $item): ?>
              <div class="d-flex align-items-center mb-3">
                <img src="<?= htmlspecialchars($item['image']) ?>" width="60" height="60" class="rounded me-3" alt="">
                <div class="flex-grow-1">
                  <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                  <small>Qty: <?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></small>
                </div>
                <span class="text-end fw-bold">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
              </div>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between fs-5 fw-bold">
              <span>Total</span>
              <span>$<?= number_format($total, 2) ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById("payment_method").addEventListener("change", function() {
  document.getElementById("cardFields").style.display = this.value === "card" ? "block" : "none";
});

// Handle checkout via AJAX to show SweetAlert
document.getElementById("checkoutForm").addEventListener("submit", function(e){
  e.preventDefault();

  const formData = new FormData(this);

  fetch('process_order.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.success){
      Swal.fire({
        icon: 'success',
        title: 'Order Placed!',
        html: `✅ Your order #<strong>${data.orderId}</strong> has been successfully placed.<br>
               <a href="account.php" class="btn btn-primary mt-2">View My Orders</a>`,
        showConfirmButton: false,
        timer: 5000
      });

      // Clear cart summary
      const summaryCard = document.querySelector(".col-md-6:nth-child(2) .card-body");
      summaryCard.innerHTML = "<p>Your cart is now empty.</p>";

      // Reset checkout form
      this.reset();
      document.getElementById("cardFields").style.display = "none";

      // Update cart badge in header
      const cartBadge = document.getElementById("cartCount");
      if(cartBadge) cartBadge.textContent = "0";

    } else {
      Swal.fire('Error', data.error || 'Something went wrong!', 'error');
    }
  })
  .catch(() => {
    Swal.fire('Error', 'Network error. Please try again.', 'error');
  });
});
</script>


<?php include 'includes/footer.php'; ?>
