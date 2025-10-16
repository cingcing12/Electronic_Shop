<?php
require_once 'init.php';

// Fetch single product
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

// Wishlist IDs for current user
$wishlist_ids = [];
if ($user_id) {
    $w_stmt = $pdo->prepare("SELECT product_id FROM wishlist_items WHERE user_id=:uid");
    $w_stmt->execute(['uid'=>$user_id]);
    $wishlist_ids = $w_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Handle Add to Cart (main product or related)
if (isset($_POST['add_to_cart'])) {
    $pid = $_POST['product_id'] ?? $product_id;
    if (!$user_id) {
        $_SESSION['after_login_redirect'] = "product.php?id=$product_id";
        redirect('login.php');
    }

    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id=:uid AND product_id=:pid");
    $stmt->execute(['uid'=>$user_id,'pid'=>$pid]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        $new_qty = $cart_item['quantity'] + 1;
        $update = $pdo->prepare("UPDATE cart_items SET quantity=:qty WHERE id=:id");
        $update->execute(['qty'=>$new_qty,'id'=>$cart_item['id']]);
        $_SESSION['cart_alert'] = "Quantity updated in cart!";
    } else {
        $insert = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:uid,:pid,1)");
        $insert->execute(['uid'=>$user_id,'pid'=>$pid]);
        $_SESSION['cart_alert'] = "Product added to cart!";
    }
    redirect("product.php?id=$product_id");
}

// Handle Add to Wishlist (main product or related)
if (isset($_POST['add_to_wishlist'])) {
    $pid = $_POST['product_id'] ?? $product_id;
    if (!$user_id) {
        $_SESSION['after_login_redirect'] = "product.php?id=$product_id";
        redirect('login.php');
    }

    if (!in_array($pid, $wishlist_ids)) {
        $insert = $pdo->prepare("INSERT INTO wishlist_items (user_id, product_id) VALUES (:uid,:pid)");
        $insert->execute(['uid'=>$user_id,'pid'=>$pid]);
        $_SESSION['wishlist_alert'] = "Added to wishlist!";
    }
    redirect("product.php?id=$product_id");
}

// Fetch related products (same category, exclude current)
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id=:cat AND id!=:pid LIMIT 12");
$related_stmt->execute(['cat'=>$product['category_id'], 'pid'=>$product_id]);
$related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<!-- MAIN PRODUCT -->
<div class="container my-5">
  <div class="row g-4 align-items-center">
    <!-- Product Image -->
    <div class="col-md-6">
      <div class="product-image-card position-relative rounded-4 overflow-hidden shadow-sm">
        <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="img-fluid product-imgTop">

        <!-- Wishlist Button -->
        <?php if($user_id): ?>
        <form method="post" class="position-absolute top-0 end-0 m-2">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            <button name="add_to_wishlist" class="btn btn-lg wishlist-btn" style="background:white;<?= in_array($product_id, $wishlist_ids) ? 'color:#ff4d6d;' : 'color:#555;' ?>" title="Wishlist">
                <i class="bi <?= in_array($product_id, $wishlist_ids) ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
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
        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
        <button name="add_to_cart" class="btn btn-primary btn-lg btn-hover-shadow">
          <i class="bi bi-cart-plus me-2"></i> Add to Cart
        </button>
      </form>
    </div>
  </div>
</div>

<!-- RELATED PRODUCTS -->
<section class="container my-5">
  <h3 class="fw-bold mb-4">Related Products</h3>
  <div class="row g-3">
    <?php foreach($related_products as $p): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative product-card">
          
          <!-- Product Image -->
          <a href="product.php?id=<?= $p['id']; ?>">
            <img src="<?= htmlspecialchars($p['image']); ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['name']); ?>">
          </a>

          <!-- Wishlist -->
          <?php if ($user_id): ?>
            <?php if (in_array($p['id'], $wishlist_ids)): ?>
              <form method="post" class="position-absolute top-0 end-0 m-2">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <button name="add_to_wishlist" class="wishlist-btn active" title="Already in Wishlist">❤️</button>
              </form>
            <?php else: ?>
              <form method="post" class="position-absolute top-0 end-0 m-2">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <button name="add_to_wishlist" class="wishlist-btn" title="Add to Wishlist">🤍</button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <a href="login.php" class="wishlist-btn position-absolute top-0 end-0 m-2" title="Login to add wishlist">🤍</a>
          <?php endif; ?>

          <div class="card-body">
            <h6 class="card-title fw-semibold text-truncate"><?= htmlspecialchars($p['name']); ?></h6>
            <p class="fw-bold text-primary mb-3">$<?= number_format($p['price'],2); ?></p>

            <!-- Add to Cart -->
            <?php if ($user_id): ?>
              <form method="post">
                <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                <button name="add_to_cart" class="btn btn-sm btn-outline-primary w-100 add-cart-btn">
                  <i class="bi bi-cart3"></i> Add to Cart
                </button>
              </form>
            <?php else: ?>
              <a href="login.php" class="btn btn-sm btn-outline-primary w-100 add-cart-btn">
                <i class="bi bi-cart3"></i> Add to Cart
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
.text-gradient-price {
  background: linear-gradient(90deg, #ff6f61, #ff9472);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.product-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.product-card:hover { transform: translateY(-6px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
.product-img { width:100%; height:250px; object-fit:cover; transition: transform 0.4s ease; }
.product-card:hover .product-img, .product-image-card:hover .product-imgTop { transform: scale(1.05); }
.wishlist-btn { position:absolute; top:10px; right:10px; background:white; border:none; font-size:1.3rem; cursor:pointer; transition: transform 0.2s ease, color 0.2s ease; border-radius:50%; width:38px; height:38px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(0,0,0,0.1); opacity:0; }
.product-card:hover .wishlist-btn, .product-image-card .wishlist-btn { opacity:1; }
.wishlist-btn:hover { transform:scale(1.2); }
.wishlist-btn.active { color:#ff4d6d; background:#ffe6ea; }
.add-cart-btn { border-radius:30px; transition: all 0.3s ease; }
.add-cart-btn:hover { background-color:#007bff; color:#fff; }
.text-truncate { white-space: nowrap; overflow:hidden; text-overflow: ellipsis; }
@media (max-width: 767px) { .wishlist-btn { opacity:1 !important; top:8px; right:8px; } }
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
