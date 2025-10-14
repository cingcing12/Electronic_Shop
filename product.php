<?php
require_once 'init.php';

if (!function_exists('get_product')) {
    function get_product($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) redirect('index.php');

$product = get_product($pdo, $product_id);
if (!$product) {
    echo "<div class='container my-5'><div class='alert alert-danger'>Product not found.</div></div>";
    include 'includes/footer.php';
    exit;
}

// User ID
$user_id = $_SESSION['user_id'] ?? null;

// Check if in wishlist
$in_wishlist = false;
if ($user_id) {
    $w_stmt = $pdo->prepare("SELECT id FROM wishlist_items WHERE user_id=:uid AND product_id=:pid");
    $w_stmt->execute(['uid'=>$user_id,'pid'=>$product_id]);
    $in_wishlist = $w_stmt->rowCount() > 0;
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    if (!$user_id) {
        $_SESSION['after_login_redirect'] = "product.php?id=$product_id";
        redirect('login.php');
    }

    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id=:uid AND product_id=:pid");
    $stmt->execute(['uid'=>$user_id,'pid'=>$product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        $new_qty = $cart_item['quantity'] + 1;
        $update = $pdo->prepare("UPDATE cart_items SET quantity=:qty WHERE id=:id");
        $update->execute(['qty'=>$new_qty,'id'=>$cart_item['id']]);
        $_SESSION['cart_alert'] = "Quantity updated in cart!";
    } else {
        $insert = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:uid,:pid,1)");
        $insert->execute(['uid'=>$user_id,'pid'=>$product_id]);
        $_SESSION['cart_alert'] = "Product added to cart!";
    }
    redirect("product.php?id=$product_id");
}

// Handle Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    if (!$user_id) {
        $_SESSION['after_login_redirect'] = "product.php?id=$product_id";
        redirect('login.php');
    }

    if ($in_wishlist) {
        redirect("wishlist.php");
    } else {
        $insert = $pdo->prepare("INSERT INTO wishlist_items (user_id, product_id) VALUES (:uid,:pid)");
        $insert->execute(['uid'=>$user_id,'pid'=>$product_id]);
        $_SESSION['wishlist_alert'] = "Added to wishlist!";
        redirect("product.php?id=$product_id");
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
  <div class="row g-4 align-items-center">
    <!-- Product Images -->
    <div class="col-md-6">
      <div class="product-image-card position-relative rounded-4 overflow-hidden shadow-sm">
        <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="img-fluid product-img">

        <!-- Wishlist Button -->
        <?php if($user_id): ?>
        <form method="post" class="position-absolute top-0 end-0 m-2">
            <button name="add_to_wishlist" class="btn btn-lg wishlist-btn" style="background:white;<?= $in_wishlist ? 'color:#ff4d6d;' : 'color:#555;' ?>" title="Wishlist">
                <i class="bi <?= $in_wishlist ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
            </button>
        </form>
        <?php else: ?>
        <a href="login.php" class="position-absolute top-0 end-0 m-2 btn btn-outline-danger btn-lg wishlist-btn">
            <i class="bi bi-heart"></i>
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Product Details -->
    <div class="col-md-6 d-flex flex-column justify-content-center">
      <h1 class="fw-bold mb-3"><?= htmlspecialchars($product['name']); ?></h1>
      <p class="text-muted fs-5 mb-3"><?= nl2br(htmlspecialchars($product['description'])); ?></p>
      <p class="fs-3 fw-bold text-gradient-price mb-4">$<?= number_format($product['price'], 2); ?></p>

      <form method="post" class="d-flex gap-3 align-items-center">
        <button name="add_to_cart" class="btn btn-primary btn-lg btn-hover-shadow">
          <i class="bi bi-cart-plus me-2"></i> Add to Cart
        </button>
      </form>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
.text-gradient-price {
  background: linear-gradient(90deg, #ff6f61, #ff9472);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Product Image Hover */
.product-image-card {
  border-radius: 1rem;
  overflow: hidden;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.product-image-card:hover {
  transform: scale(1.03);
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}
.product-img {
  width: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.product-image-card:hover .product-img {
  transform: scale(1.1);
}

/* Wishlist Button */
.wishlist-btn {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.product-image-card:hover .wishlist-btn {
  transform: translateY(-2px);
  opacity:1;
}

/* Buttons Hover */
.btn-hover-shadow {
  border-radius: 50px;
  padding: 0.75rem 1.5rem;
  transition: all 0.3s ease;
}
.btn-hover-shadow:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}
</style>

<?php include 'includes/footer.php'; ?>

<?php
// SweetAlert for Add to Cart
if (isset($_SESSION['cart_alert'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    $msg = $_SESSION['cart_alert'];
    echo "<script>Swal.fire({icon:'success',title:'$msg',timer:1500,showConfirmButton:false});</script>";
    unset($_SESSION['cart_alert']);
}

// SweetAlert for Add to Wishlist
if (isset($_SESSION['wishlist_alert'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    $msg = $_SESSION['wishlist_alert'];
    echo "<script>Swal.fire({icon:'success',title:'$msg',timer:1500,showConfirmButton:false});</script>";
    unset($_SESSION['wishlist_alert']);
}
?>
