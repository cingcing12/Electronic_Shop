<?php
require_once 'init.php';

// Get latest products (limit to 12)
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 12");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add to Cart request
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (!is_logged_in()) {
        $_SESSION['after_login_redirect'] = "index.php"; // Redirect to this page after login
        redirect('login.php');
    }
    add_to_cart($pdo, $_SESSION['user_id'], $product_id, 1);
    $_SESSION['cart_success'] = true; // Set success flag for SweetAlert
    redirect('index.php'); // Refresh the page after adding to cart
}

// Handle Add to Wishlist request
if (isset($_POST['add_to_wishlist'])) {
    $product_id = $_POST['product_id'];

    if (!is_logged_in()) {
        $_SESSION['after_login_redirect'] = "index.php"; // Redirect to this page after login
        redirect('login.php');
    }

    // Check if the product is already in the wishlist
    $stmt = $pdo->prepare("SELECT * FROM wishlist_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $wishlist_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($wishlist_item) {
        $_SESSION['wishlist_error'] = true; // Set error flag for SweetAlert
        redirect('index.php'); // Refresh the page after showing the error
    } else {
        add_to_wishlist($pdo, $_SESSION['user_id'], $product_id);
        $_SESSION['wishlist_success'] = true; // Set success flag for SweetAlert
        redirect('index.php'); // Refresh the page after adding to wishlist
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
  <h2 class="mb-4 text-center">✨ Featured Products</h2>

  <div class="row g-4">
    <?php foreach ($products as $p): ?>
      <div class="col-sm-6 col-md-4 col-lg-3 col-6">
        <div class="card h-100 shadow-sm border-0">
          <a href="product.php?id=<?= $p['id']; ?>" class="text-decoration-none text-dark">
            <img src="<?= htmlspecialchars($p['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']); ?>" style="height: 200px; object-fit: cover;">
          </a>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-1"><?= htmlspecialchars($p['name']); ?></h5>
            <p class="text-primary fw-bold mb-2">$<?= number_format($p['price'], 2); ?></p>

            <div class="mt-auto d-grid gap-2">
              <?php if (is_logged_in()): ?>
                <!-- Add to Cart Form -->
                <form method="POST" class="d-inline">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <button name="add_to_cart" class="btn btn-sm btn-outline-primary">Add to Cart</button>
                </form>

                <!-- Add to Wishlist Form -->
                <form method="POST" class="d-inline">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <button name="add_to_wishlist" class="btn btn-sm btn-outline-secondary">Add to Wishlist</button>
                </form>
              <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-outline-primary">Add to Cart</a>
                <a href="login.php" class="btn btn-sm btn-outline-secondary">Add to Wishlist</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php
// Show SweetAlert if product is added to cart successfully
if (isset($_SESSION['cart_success']) && $_SESSION['cart_success']) {
    unset($_SESSION['cart_success']); // Clear the session flag
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Product added to cart!',
        showConfirmButton: false,
        timer: 1500
      });
    </script>
<?php } ?>

<?php
// Show SweetAlert if product is added to wishlist successfully
if (isset($_SESSION['wishlist_success']) && $_SESSION['wishlist_success']) {
    unset($_SESSION['wishlist_success']); // Clear the session flag
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Product added to wishlist!',
        showConfirmButton: false,
        timer: 1500
      });
    </script>
<?php } ?>

<?php
// Show SweetAlert if product is already in wishlist
if (isset($_SESSION['wishlist_error']) && $_SESSION['wishlist_error']) {
    unset($_SESSION['wishlist_error']); // Clear the session flag
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      Swal.fire({
        position: 'top-end',
        icon: 'error',
        title: 'This product is already in your wishlist!',
        showConfirmButton: false,
        timer: 1500
      });
    </script>
<?php } ?>

<?php include 'includes/footer.php'; ?>
