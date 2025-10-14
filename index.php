<?php
require_once 'init.php';

// Fetch latest 12 products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 12");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add to Cart and Wishlist
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (!is_logged_in()) {
        $_SESSION['after_login_redirect'] = "index.php";
        redirect('login.php');
    }
    add_to_cart($pdo, $_SESSION['user_id'], $product_id, 1);
    $_SESSION['cart_success'] = true;
    redirect('index.php');
}

if (isset($_POST['add_to_wishlist'])) {
    $product_id = $_POST['product_id'];
    if (!is_logged_in()) {
        $_SESSION['after_login_redirect'] = "index.php";
        redirect('login.php');
    }

    $stmt = $pdo->prepare("SELECT * FROM wishlist_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    if ($stmt->fetch()) {
        $_SESSION['wishlist_error'] = true;
    } else {
        add_to_wishlist($pdo, $_SESSION['user_id'], $product_id);
        $_SESSION['wishlist_success'] = true;
    }
    redirect('index.php');
}

include 'includes/header.php';
?>

<!-- 🌈 Modern ShopEasy Redesign -->
<style>
body {
  background: #f4f6fb;
  font-family: "Poppins", sans-serif;
  color: #333;
}

/* ===== HERO SECTION ===== */
.hero {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  text-align: center;
  padding: 120px 20px;
  border-radius: 0 0 60px 60px;
  position: relative;
  overflow: hidden;
}
.hero::after {
  content: "";
  position: absolute;
  top: -50px; left: 50%;
  width: 120%;
  height: 140%;
  background: radial-gradient(circle at top right, rgba(255,255,255,0.1), transparent);
  transform: translateX(-50%) rotate(5deg);
}
.hero h1 {
  font-weight: 800;
  font-size: 3rem;
}
.hero p {
  font-size: 1.15rem;
  opacity: 0.9;
}
.hero .btn {
  background: #fff;
  color: #6366f1;
  border: none;
  padding: 12px 35px;
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.3s;
}
.hero .btn:hover {
  transform: translateY(-3px);
  background: #f1f1f1;
}

/* ===== CATEGORY SECTION ===== */
.categories {
  margin: 80px auto;
  text-align: center;
}
.category-card {
  background: rgba(255,255,255,0.9);
  border-radius: 25px;
  padding: 30px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.06);
  transition: 0.4s ease;
  backdrop-filter: blur(10px);
}
.category-card:hover {
  transform: translateY(-8px);
  background: linear-gradient(135deg, #eef2ff, #fdfbff);
  box-shadow: 0 15px 30px rgba(99,102,241,0.2);
}
.category-card i {
  font-size: 2.6rem;
  color: #6366f1;
  margin-bottom: 12px;
  transition: 0.3s;
}
.category-card:hover i {
  transform: scale(1.2);
  color: #8b5cf6;
}

/* ===== PRODUCT SECTION ===== */
.product-section {
  margin-top: 90px;
}
.product-section h2 {
  font-weight: 800;
  color: #1e1e2f;
  margin-bottom: 50px;
}
.card {
  border-radius: 25px;
  border: none;
  overflow: hidden;
  transition: 0.4s ease;
  box-shadow: 0 6px 18px rgba(0,0,0,0.05);
  background: #fff;
}
.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.1);
}
.card-img-top {
  height: 230px;
  object-fit: cover;
  transition: 0.4s;
}
.card:hover .card-img-top {
  transform: scale(1.05);
}
.card-title {
  font-weight: 600;
  margin-top: 8px;
}
.card .btn {
  border-radius: 30px;
  font-weight: 500;
  padding: 6px 18px;
}
.btn-outline-primary {
  border-color: #6366f1;
  color: #6366f1;
}
.btn-outline-primary:hover {
  background: #6366f1;
  color: white;
}
.btn-outline-secondary:hover {
  background: #8b5cf6;
  color: white;
}

/* ===== CTA SECTION ===== */
.cta {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  text-align: center;
  padding: 90px 20px;
  border-radius: 30px;
  margin-top: 100px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}
.cta h3 {
  font-weight: 800;
  font-size: 2.2rem;
}
.cta p {
  font-size: 1.1rem;
  opacity: 0.9;
}
.cta .btn {
  background: white;
  color: #6366f1;
  font-weight: 600;
  padding: 12px 35px;
  border-radius: 50px;
  transition: all 0.3s ease;
}
.cta .btn:hover {
  background: #f1f1f1;
  transform: translateY(-4px);
}

/* ===== NEWSLETTER ===== */
.newsletter {
  background: white;
  padding: 60px;
  border-radius: 25px;
  margin: 100px auto;
  box-shadow: 0 8px 30px rgba(0,0,0,0.05);
  text-align: center;
}
.newsletter h4 {
  font-weight: 700;
  color: #1e1e2f;
}
.newsletter input {
  max-width: 400px;
  display: inline-block;
  border-radius: 50px;
  border: 1px solid #ddd;
  padding: 10px 15px;
}
.newsletter .btn {
  border-radius: 50px;
  background: #6366f1;
  border: none;
  padding: 10px 25px;
  font-weight: 600;
}
.newsletter .btn:hover {
  background: #8b5cf6;
}
</style>


<!-- 💫 HERO SECTION -->
<section class="hero">
  <div class="container">
    <h1>Welcome to ShopEasy 🛍️</h1>
    <p>Your one-stop shop for premium products and unbeatable deals.</p>
    <a href="#products" class="btn btn-light mt-4 px-4 py-2 rounded-pill fw-semibold">Shop Now</a>
  </div>
</section>

<?php
require_once 'init.php';

// Fetch categories
$sql = "SELECT * FROM categories ORDER BY name ASC";
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="categories container my-5">
  <h2 class="mb-5 fw-bold text-center">Explore Categories</h2>
  <div class="row g-4">
    <?php if (count($categories) > 0): ?>
        <?php foreach($categories as $row): ?>
            <div class="col-md-3 col-6">
                <a href="category.php?category_id=<?php echo $row['id']; ?>" class="text-decoration-none">
                    <div class="category-card text-center p-4 rounded-4 shadow-sm">
                        <div class="icon-wrapper mb-3">
    <i class="bi <?php echo htmlspecialchars($row['icon']); ?>"></i>
</div>
                        <h6 class="fw-semibold text-dark"><?php echo htmlspecialchars($row['name']); ?></h6>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">No categories found.</p>
    <?php endif; ?>
  </div>
</section>




<?php
// Fetch wishlist IDs for logged-in user (PDO)
$wishlist_ids = [];
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT product_id FROM wishlist_items WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $wishlist_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

?>

<!-- 🛒 FEATURED PRODUCTS -->
<section id="products" class="container product-section my-5">
  <h2 class="text-center fw-bold mb-4">📣 Featured Products</h2>
  <div class="row g-4">
    <?php foreach ($products as $p): ?>
      <div class="col-sm-6 col-md-4 col-lg-3 col-6">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative product-card">
          
          <!-- Product Image -->
          <a href="product.php?id=<?= $p['id']; ?>">
            <img src="<?= htmlspecialchars($p['image']); ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['name']); ?>">
          </a>

          <!-- Wishlist Heart -->
          <?php if (is_logged_in()): ?>
              <?php if (in_array($p['id'], $wishlist_ids)): ?>
                <!-- Already in wishlist: redirect to wishlist.php -->
                <a href="wishlist.php" class="wishlist-btn active nav-link" title="Already in Wishlist">❤️</a>
              <?php else: ?>
                <!-- Not in wishlist: add to wishlist -->
                <form method="POST" class="wishlist-form">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <button name="add_to_wishlist" class="wishlist-btn" title="Add to Wishlist">🤍</button>
                </form>
              <?php endif; ?>
          <?php else: ?>
            <a href="login.php" class="wishlist-btn" title="Login to add wishlist">🤍</a>
          <?php endif; ?>

          <div class="card-body">
            <h6 class="card-title fw-semibold text-truncate"><?= htmlspecialchars($p['name']); ?></h6>
            <p class="fw-bold text-primary mb-3">$<?= number_format($p['price'], 2); ?></p>

            <!-- Add to Cart -->
            <?php if (is_logged_in()): ?>
              <form method="POST" class="cart-form">
                <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                <div style="width: fit-content;"><button name="add_to_cart" class="btn btn-sm btn-outline-primary w-100 add-cart-btn"><i class="bi bi-cart3"></i> Add to Cart</button></div>
              </form>
            <?php else: ?>
              
              <div style="width: fit-content;"><a href="login.php" class="btn btn-sm btn-outline-primary w-100 add-cart-btn"><i class="bi bi-cart3"></i> Add to Cart</a></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>


<!-- 🌈 Modern Product Card CSS -->
<style>
.product-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.product-img {
  width: 100%;
  height: 230px;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

/* Wishlist Heart */
.wishlist-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: white;
  border: none;
  font-size: 1.3rem;
  cursor: pointer;
  transition: transform 0.2s ease, color 0.2s ease;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  opacity: 0;
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

/* Add to Cart button */
.add-cart-btn {
  border-radius: 30px;
  transition: all 0.3s ease;
  opacity: 1;
}


.add-cart-btn:hover {
  background-color: #007bff;
  color: #fff;
}

.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>

<!-- 💥 PROMO CTA -->
<section class="cta container">
  <h3>Get 20% Off Your First Order!</h3>
  <p>Sign up now and enjoy exclusive deals and new arrivals every week.</p>
  <a href="register.php" class="btn mt-3">Join Now</a>
</section>

<!-- 💌 NEWSLETTER -->
<section class="newsletter container">
  <h4 class="fw-bold mb-3">Stay Updated!</h4>
  <p>Subscribe to our newsletter to receive latest offers and product updates.</p>
  <form class="d-flex justify-content-center mt-3">
    <input type="email" class="form-control me-2" placeholder="Enter your email">
    <button class="btn btn-primary rounded-pill px-4">Subscribe</button>
  </form>
</section>

<?php
// SweetAlert popups
if (isset($_SESSION['cart_success'])) {
  unset($_SESSION['cart_success']);
  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>Swal.fire({icon:'success', title:'Added to Cart!', timer:1500, showConfirmButton:false});</script>";
}
if (isset($_SESSION['wishlist_success'])) {
  unset($_SESSION['wishlist_success']);
  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>Swal.fire({icon:'success', title:'Added to Wishlist!', timer:1500, showConfirmButton:false});</script>";
}
if (isset($_SESSION['wishlist_error'])) {
  unset($_SESSION['wishlist_error']);
  echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>Swal.fire({icon:'error', title:'Already in Wishlist!', timer:1500, showConfirmButton:false});</script>";
}

include 'includes/footer.php';
?>
