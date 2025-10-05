<?php
require_once 'init.php';
if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'cart.php';
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$items = get_cart_items($pdo, $user_id);

include 'includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4">Your Cart update code</h2>

  <?php if (empty($items)): ?>
    <div class="alert alert-info">Your cart is empty.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th style="width: 150px;">Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $index => $it): 
            $subtotal = $it['price'] * $it['quantity'];
          ?>
            <tr 
              data-index="<?= $index; ?>" 
              data-price="<?= $it['price']; ?>" 
              data-product-id="<?= $it['product_id']; ?>"
            >
              <td class="text-start d-flex align-items-center gap-2">
                <img src="<?= htmlspecialchars($it['image']); ?>" alt="img" style="width: 60px; height: 60px; object-fit: cover;">
                <?= htmlspecialchars($it['name']); ?>
              </td>
              <td>$<span class="price"><?= number_format($it['price'], 2, '.', ''); ?></span></td>
              <td>
                <div class="input-group mx-auto" style="max-width: 120px;">
                  <button class="btn btn-outline-secondary btn-decrease" type="button">−</button>
                  <input type="text" class="form-control text-center qty-input" value="<?= (int)$it['quantity']; ?>" readonly>
                  <button class="btn btn-outline-secondary btn-increase" type="button">+</button>
                </div>
              </td>
              <td>$<span class="subtotal"><?= number_format($subtotal, 2, '.', ''); ?></span></td>
              <td>
                <button class="btn btn-danger btn-sm btn-remove">Remove</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-end">Total</th>
            <th colspan="2">$<span id="cart-total">0.00</span></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex justify-content-end">
      <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
    </div>
  <?php endif; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('tbody tr');
    const totalEl = document.getElementById('cart-total');

    function updateTotal() {
      let total = 0;
      document.querySelectorAll('.subtotal').forEach(el => {
        total += parseFloat(el.textContent);
      });
      totalEl.innerHTML = total.toFixed(2);
    }

    function updateQuantityOnServer(productId, newQty, qtyInput, subtotalEl, price) {
      fetch('update_cart_quantity.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({product_id: productId, quantity: newQty})
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          qtyInput.value = newQty;
          subtotalEl.textContent = (price * newQty).toFixed(2);
          updateTotal();
        } else {
          alert('Failed to update quantity: ' + (data.error || 'Unknown error'));
        }
      })
      .catch(() => alert('Network error. Please try again.'));
    }

    function removeItem(productId, row) {
  Swal.fire({
    title: 'Remove Item?',
    text: "Are you sure you want to remove this product from your cart?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, remove it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('remove_cart_item.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ product_id: productId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          row.remove();
          updateTotal();

          Swal.fire({
            title: 'Removed!',
            text: 'The product has been removed from your cart.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            position: "top-end"
          });

          if (document.querySelectorAll('tbody tr').length === 0) {
            setTimeout(() => location.reload(), 1600);
          }
        } else {
          Swal.fire('Error', data.error || 'Failed to remove item.', 'error');
        }
      })
      .catch(() => {
        Swal.fire('Network Error', 'Please try again.', 'error');
      });
    }
  });
}


    rows.forEach(row => {
      const price = parseFloat(row.getAttribute('data-price'));
      const productId = row.getAttribute('data-product-id');
      const qtyInput = row.querySelector('.qty-input');
      const subtotalEl = row.querySelector('.subtotal');
      const btnIncrease = row.querySelector('.btn-increase');
      const btnDecrease = row.querySelector('.btn-decrease');
      const btnRemove = row.querySelector('.btn-remove');

      btnIncrease.addEventListener('click', () => {
        let qty = parseInt(qtyInput.value);
        updateQuantityOnServer(productId, qty + 1, qtyInput, subtotalEl, price);
      });

      btnDecrease.addEventListener('click', () => {
        let qty = parseInt(qtyInput.value);
        if (qty > 1) {
          updateQuantityOnServer(productId, qty - 1, qtyInput, subtotalEl, price);
        }
      });

      btnRemove.addEventListener('click', () => {
        removeItem(productId, row);
      });
    });

    updateTotal();
  });
</script>

<?php include 'includes/footer.php'; ?>
