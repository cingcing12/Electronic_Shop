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

<!-- 🌈 Custom CSS -->
<style>
body {
  background-color: #f8f9fc;
  font-family: "Poppins", sans-serif;
  color: #333;
}

/* Hero */
.hero {
  background: linear-gradient(135deg, #6a11cb, #2575fc);
  color: white;
  padding: 100px 0;
  text-align: center;
  border-radius: 0 0 40px 40px;
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
}
.hero h1 {
  font-weight: 700;
  font-size: 2.8rem;
}
.hero p {
  font-size: 1.2rem;
  margin-top: 10px;
  opacity: 0.9;
}

/* Categories */
.categories {
  margin: 60px auto;
  text-align: center;
}
.category-card {
  background: white;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  transition: 0.3s;
}
.category-card:hover {
  transform: translateY(-5px);
  background: linear-gradient(135deg, #2575fc1a, #6a11cb1a);
}
.category-card i {
  font-size: 2.5rem;
  color: #2575fc;
  margin-bottom: 15px;
}

/* Products */
.product-section h2 {
  font-weight: 700;
  margin-bottom: 40px;
}
.card {
  border-radius: 20px;
  transition: 0.3s;
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}
.card-img-top {
  height: 220px;
  object-fit: cover;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
}
.card-title {
  font-weight: 600;
}

/* CTA */
.cta {
  background: linear-gradient(135deg, #2575fc, #6a11cb);
  color: white;
  text-align: center;
  padding: 80px 20px;
  border-radius: 25px;
  margin-top: 80px;
}
.cta h3 {
  font-size: 2rem;
  font-weight: 700;
}
.cta p {
  font-size: 1.1rem;
}
.cta .btn {
  background: white;
  color: #2575fc;
  border: none;
  font-weight: 600;
  padding: 10px 30px;
  border-radius: 50px;
  transition: 0.3s;
}
.cta .btn:hover {
  background: #f1f1f1;
}

/* Newsletter */
.newsletter {
  background: #fff;
  padding: 50px;
  border-radius: 20px;
  margin: 80px auto;
  box-shadow: 0 6px 20px rgba(0,0,0,0.05);
  text-align: center;
}
.newsletter input {
  max-width: 400px;
  display: inline-block;
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


<!-- 🛒 FEATURED PRODUCTS -->
<section id="products" class="container product-section my-5">
  <h2 class="text-center">🔥 Featured Products</h2>
  <div class="row g-4">
    <?php foreach ($products as $p): ?>
      <div class="col-sm-6 col-md-4 col-lg-3 col-6">
        <div class="card border-0 shadow-sm h-100">
          <a href="product.php?id=<?= $p['id']; ?>">
            <img src="<?= htmlspecialchars($p['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']); ?>">
          </a>
          <div class="card-body text-center">
            <h6 class="card-title"><?= htmlspecialchars($p['name']); ?></h6>
            <p class="fw-bold text-primary mb-3">$<?= number_format($p['price'], 2); ?></p>
            <div class="d-flex justify-content-center gap-2">
              <?php if (is_logged_in()): ?>
                <form method="POST">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <button name="add_to_cart" class="btn btn-sm btn-outline-primary">🛒 Cart</button>
                </form>
                <form method="POST">
                  <input type="hidden" name="product_id" value="<?= $p['id']; ?>">
                  <button name="add_to_wishlist" class="btn btn-sm btn-outline-secondary">❤️ Wishlist</button>
                </form>
              <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-outline-primary">🛒 Cart</a>
                <a href="login.php" class="btn btn-sm btn-outline-secondary">❤️ Wishlist</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

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
