<?php
require_once 'init.php';

// Check category
if (!isset($_GET['category_id'])) die("Category not specified.");
$category_id = (int)$_GET['category_id'];

// Fetch category name
$cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = :id");
$cat_stmt->execute(['id' => $category_id]);
$category_row = $cat_stmt->fetch(PDO::FETCH_ASSOC);
if (!$category_row) die("Category not found.");
$category = $category_row['name'];
$titleName = $category;
include 'includes/header.php';
// Fetch products
$stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = :id ORDER BY created_at DESC");
$stmt->execute(['id' => $category_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch wishlist for logged-in user
$wishlist_ids = [];
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $w_stmt = $pdo->prepare("SELECT product_id FROM wishlist_items WHERE user_id = :user_id");
    $w_stmt->execute(['user_id' => $user_id]);
    $wishlist_ids = $w_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Handle Add to Cart
if (isset($_POST['add_to_cart']) && is_logged_in()) {
    $product_id = (int)$_POST['product_id'];

    // Check if product already in cart
    $c_stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
    $c_stmt->execute(['user_id'=>$user_id,'product_id'=>$product_id]);
    $cart_item = $c_stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Already in cart → increase quantity by 1
        $new_qty = $cart_item['quantity'] + 1;
        $update = $pdo->prepare("UPDATE cart_items SET quantity = :qty WHERE id = :id");
        $update->execute(['qty'=>$new_qty, 'id'=>$cart_item['id']]);
        $alert_msg = "Quantity updated in cart!";
    } else {
        // Not in cart → add new
        $insert = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)");
        $insert->execute(['user_id'=>$user_id,'product_id'=>$product_id]);
        $alert_msg = "Product added to cart!";
    }

    // SweetAlert
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>Swal.fire({icon:'success', title:'$alert_msg', timer:1500, showConfirmButton:false}).then(()=>{window.location='?category_id=$category_id'});</script>";
}


// Handle Add to Wishlist
if (isset($_POST['add_to_wishlist']) && is_logged_in()) {
    $product_id = (int)$_POST['product_id'];
    if (in_array($product_id, $wishlist_ids)) {
        // Already in wishlist → go to wishlist page
        echo "<script>window.location='wishlist.php';</script>";
        exit;
    } else {
        $insert = $pdo->prepare("INSERT INTO wishlist_items (user_id, product_id) VALUES (:user_id, :product_id)");
        $insert->execute(['user_id'=>$user_id,'product_id'=>$product_id]);
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>Swal.fire({icon:'success', title:'Added to Wishlist', timer:1500, showConfirmButton:false}).then(()=>{window.location='?category_id=$category_id'});</script>";
    }
}
?>

<!-- 🛒 Category Products -->
<section id="products" class="container my-5">
  <h2 class="fw-bold mb-5 "><?= htmlspecialchars($category); ?> Products</h2>

  <?php if (count($products) > 0): ?>
    <div class="row g-4">
      <?php foreach ($products as $p): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card product-card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">

            <!-- Product Image -->
            <div class="product-img-wrapper position-relative overflow-hidden">
              <a href="product.php?id=<?= $p['id']; ?>">
                <img src="<?= htmlspecialchars($p['image']); ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['name']); ?>">
              </a>

              <!-- Wishlist Button -->
              <?php if (is_logged_in()): ?>
                  <?php if (in_array($p['id'], $wishlist_ids)): ?>
                    <!-- Already in wishlist -->
                    <a href="wishlist.php" class="wishlist-btn active nav-link" title="Already in Wishlist">❤️</a>
                  <?php else: ?>
                    <form method="POST" class="wishlist-form">
                      <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                      <button name="add_to_wishlist" class="wishlist-btn" title="Add to Wishlist">🤍</button>
                    </form>
                  <?php endif; ?>
              <?php else: ?>
                <a href="login.php" class="wishlist-btn nav-link" title="Login to add wishlist">🤍</a>
              <?php endif; ?>
            </div>

            <!-- Card Body -->
            <div class="card-body">
              <h6 class="card-title fw-semibold text-truncate"><?= htmlspecialchars($p['name']); ?></h6>
              <p class="fw-bold text-primary mb-3">$<?= number_format($p['price'], 2); ?></p>

              <!-- Add to Cart -->
              <?php if (is_logged_in()): ?>
                <form method="POST">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <div style="width: fit-content;"><button name="add_to_cart" class="btn btn-sm btn-outline-primary w-100 add-cart-btn">
                    <i class="bi bi-cart3"></i> Add to Cart
                  </button></div>
                </form>
              <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-outline-primary w-100">
                  <i class="bi bi-cart3"></i> Add to Cart
                </a>
              <?php endif; ?>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-center text-secondary fs-5">No products found in this category.</p>
  <?php endif; ?>
</section>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* Gradient Heading */
.text-gradient-primary {
    background: linear-gradient(90deg, #ff6f61, #ff9472);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Product Card */
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.18);
}

/* Product Image */
.product-img-wrapper {
    position: relative;
    overflow: hidden;
}
.product-img {
    width: 100%;
    height: 230px;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.product-card:hover .product-img {
    transform: scale(1.05);
}

/* Wishlist Heart */
.wishlist-btn {
    position: absolute;
    top: -2px;
    right: -2px;
    background: white;
    border: none;
    font-size: 1.4rem;
    cursor: pointer;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.2s ease;
}
.product-card:hover .wishlist-btn {
    opacity: 1;
}
.wishlist-btn:hover {
    transform: scale(1.2);
}
.wishlist-btn.active {
    color: #ff4d6d;
    background: #ffe6ea;
}

/* Add to Cart */
.add-cart-btn {
    border-radius: 30px;
    transition: all 0.3s ease;
}
.add-cart-btn:hover {
    transform: scale(1.05);
}

/* Text Truncate */
.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

<?php
include 'includes/footer.php';
?>
