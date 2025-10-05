<?php
require_once 'init.php';

if (!function_exists('get_product')) {
    function get_product($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Get product ID from query string
$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    redirect('index.php'); // redirect if no id
}

// Fetch product data
$product = get_product($pdo, $product_id);

if (!$product) {
    // Show a message if product not found and stop further execution
    echo "<div class='container my-5'><div class='alert alert-danger'>Product not found.</div></div>";
    include 'includes/footer.php';
    exit;
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    if (!is_logged_in()) {
        $_SESSION['after_login_redirect'] = "product.php?id=$product_id";
        redirect('login.php');
    }
    add_to_cart($pdo, $_SESSION['user_id'], $product_id, 1);

    // Set success flag in session to show SweetAlert
    $_SESSION['cart_success'] = true;
    redirect("product.php?id=$product_id");
}

// Handle Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    if (!is_logged_in()) {
        $_SESSION['after_login_redirect'] = "product.php?id=$product_id";
        redirect('login.php');
    }
    add_to_wishlist($pdo, $_SESSION['user_id'], $product_id);
    redirect('wishlist.php');
}

include 'includes/header.php';
?>

<div class="container my-5">
  <div class="row">
    <!-- Product Image -->
    <div class="col-md-6 mb-4">
      <div class="border rounded shadow-sm p-3">
        <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="img-fluid rounded">
      </div>
    </div>

    <!-- Product Details -->
    <div class="col-md-6">
      <h2 class="mb-3"><?= htmlspecialchars($product['name']); ?></h2>
      <p class="text-muted fs-5"><?= nl2br(htmlspecialchars($product['description'])); ?></p>
      <p class="h4 text-primary fw-bold mb-4">$<?= number_format($product['price'], 2); ?></p>

      <form method="post" class="d-flex gap-2">
        <button name="add_to_cart" class="btn btn-primary btn-lg">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button name="add_to_wishlist" class="btn btn-outline-secondary btn-lg">
          <i class="bi bi-heart"></i> Add to Wishlist
        </button>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php if (isset($_SESSION['cart_success']) && $_SESSION['cart_success']): ?>
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
  <?php unset($_SESSION['cart_success']); // Clear the success flag ?>
<?php endif; ?>