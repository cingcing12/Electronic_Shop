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

$titleName = "Home";

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


<!-- Hero Section -->
<section class="hero position-relative overflow-hidden">
  <!-- Animated Particles Background -->
  <canvas id="heroParticles" class="position-absolute top-0 start-0 w-100 h-100"></canvas>

  <div class="container py-5 position-relative" style="z-index:2">
    <div class="row align-items-center">

      <!-- Left: Title, Subtitle, Features, CTA -->
      <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0 hero-text">
        <h1 class="fw-bold display-4 text-primary mb-3">Welcome to Electronic_Shop ⚡</h1>
        <p class="lead text-muted mb-4">Discover the latest gadgets, premium electronics, and unbeatable deals all in one place.</p>

        <!-- Features -->
        <div class="d-flex flex-column flex-sm-row gap-3 mb-4 justify-content-center justify-content-lg-start">
          <div class="feature bg-light p-3 rounded-3 shadow-sm text-center">
            <i class="bi bi-truck fs-3 text-primary mb-2"></i>
            <p class="mb-0 small">Fast Delivery</p>
          </div>
          <div class="feature bg-light p-3 rounded-3 shadow-sm text-center">
            <i class="bi bi-shield-check fs-3 text-primary mb-2"></i>
            <p class="mb-0 small">Secure Payments</p>
          </div>
          <div class="feature bg-light p-3 rounded-3 shadow-sm text-center">
            <i class="bi bi-star fs-3 text-primary mb-2"></i>
            <p class="mb-0 small">Top Quality</p>
          </div>
        </div>

        <!-- CTA Button -->
        <a href="#products" class="btn btn-primary btn-lg rounded-pill hero-btn">Shop Now</a>
      </div>

      <!-- Right: Swiper Slider (No Add to Cart) -->
      <div class="col-lg-6">
        <div class="swiper heroSwiper shadow-lg rounded-4">
          <div class="swiper-wrapper">
            <div class="swiper-slide hero-slide-container">
              <img src="https://img.pacifiko.com/PROD/resize/1/500x500/B0DW29H85Z.jpg" alt="Product 1" class="w-100 rounded-4">
              <div class="slide-info">
                <h5 class="text-dark">ASUS ROG Strix SCAR 18</h5>
                <p>$3999.99</p>
              </div>
            </div>

            <div class="swiper-slide hero-slide-container">
              <img src="https://easypc.com.ph/cdn/shop/files/YGT_V300_MAtx_Tempered_Glass_Gaming_PC_Case_Black-b_2048x.png?v=1701411825" alt="Product 2" class="w-100 rounded-4">
              <div class="slide-info">
                <h5 class="text-dark">Gaming PC Case</h5>
                <p>$349.99</p>
              </div>
            </div>

            <div class="swiper-slide hero-slide-container">
              <img src="https://xiaomistoreph.com/cdn/shop/files/Xiaomi_CurvedGamingMonitorG34WQI_WBG_1_1024x1024.jpg?v=1749552823" alt="Product 3" class="w-100 rounded-4">
              <div class="slide-info">
                <h5 class="text-dark">Curved Gaming Monitor</h5>
                <p>$499.99</p>
              </div>
            </div>
          </div>
          <div class="swiper-pagination mt-3"></div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
const heroSwiper = new Swiper('.heroSwiper', {
  loop: true,
  autoplay: { delay: 3000, disableOnInteraction: false },
  pagination: { el: '.swiper-pagination', clickable: true },
  effect: 'coverflow',
  coverflowEffect: { rotate: 20, slideShadows: true },
  slidesPerView: 1,
  spaceBetween: 20,
});
</script>

<!-- Particles JS -->
<script>
const canvas = document.getElementById('heroParticles');
const ctx = canvas.getContext('2d');
canvas.width = canvas.offsetWidth;
canvas.height = canvas.offsetHeight;

const particles = [];
const numParticles = 50;

for(let i=0; i<numParticles; i++){
  particles.push({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height,
    r: Math.random()*3+1,
    dx: (Math.random()-0.5)*0.5,
    dy: (Math.random()-0.5)*0.5
  });
}

function animateParticles(){
  ctx.clearRect(0,0,canvas.width,canvas.height);
  particles.forEach(p=>{
    ctx.beginPath();
    ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
    ctx.fillStyle = "rgba(13,110,253,0.2)";
    ctx.fill();
    p.x += p.dx;
    p.y += p.dy;
    if(p.x < 0 || p.x > canvas.width) p.dx *= -1;
    if(p.y < 0 || p.y > canvas.height) p.dy *= -1;
  });
  requestAnimationFrame(animateParticles);
}
animateParticles();
window.addEventListener('resize', () => {
  canvas.width = canvas.offsetWidth;
  canvas.height = canvas.offsetHeight;
});
</script>

<style>
.hero {
  min-height: 80vh;
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #e0f2ff, #f0f9ff);
  display: flex; align-items: center;
}
.hero-text { animation: slideUp 1s ease forwards; z-index:2; }
.hero h1 { font-size:3rem; color:#0d6efd; }
.hero p { font-size:1.2rem; color:#555; margin-bottom:2rem; }
.hero-btn { transition: all 0.3s ease; }
.hero-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(13,110,253,0.3); }

/* Features */
.feature { min-width:100px; transition: all 0.3s ease; }
.feature:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

/* Swiper Slide Info Overlay (No Button) */
.hero-slide-container { position:relative; overflow:hidden; }
.slide-info {
  position:absolute; bottom:10px; left:10px;
  background:rgba(255,255,255,0.85); padding:10px 15px; border-radius:10px;
  box-shadow:0 5px 15px rgba(0,0,0,0.1); opacity:0; transform:translateY(20px);
  transition: all 0.3s ease;
}
.hero-slide-container:hover .slide-info { opacity:1; transform:translateY(0); }
.slide-info h5 { margin:0; font-size:1rem; font-weight:600; }
.slide-info p { margin:0.2rem 0; font-size:0.9rem; }

/* Animations */
@keyframes slideUp { 0%{opacity:0;transform:translateY(30px);}100%{opacity:1;transform:translateY(0);} }

/* Responsive */
@media(max-width:992px){
.hero h1{font-size:2.2rem;}
.hero p{font-size:1rem;}
.hero-slide-container img{height:250px; object-fit:cover;}
.feature{min-width:auto; flex:1;}
}
</style>


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

    <?php if (is_logged_in()): ?>
    <button 
        class="wishlist-btn ajax-add-wishlist <?= in_array($p['id'], $wishlist_ids) ? 'active' : '' ?>" 
        data-id="<?= $p['id']; ?>" 
        title="<?= in_array($p['id'], $wishlist_ids) ? 'Already in Wishlist' : 'Add to Wishlist'; ?>">
        <?= in_array($p['id'], $wishlist_ids) ? '❤️' : '🤍'; ?>
    </button>
<?php else: ?>
    <a href="login.php" class="wishlist-btn" title="Login to add wishlist">🤍</a>
<?php endif; ?>


          <div class="card-body">
            <h6 class="card-title fw-semibold text-truncate"><?= htmlspecialchars($p['name']); ?></h6>
            <p class="fw-bold text-primary mb-3">$<?= number_format($p['price'], 2); ?></p>

            <!-- Add to Cart Button -->
            <?php if (is_logged_in()): ?>
              <button class="btn btn-sm btn-outline-primary ajax-add-cart" data-id="<?= $p['id']; ?>">
  <i class="bi bi-cart3"></i> Add to Cart
</button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

  function updateCounts(data, btn=null, type=null) {
    if(data.cart_count !== undefined){
      const cartElem = document.querySelector('#cartCount');
      if(cartElem) cartElem.textContent = data.cart_count;
    }
    if(data.wishlist_count !== undefined){
      const wishElem = document.querySelector('#wishlistCount');
      if(wishElem) wishElem.textContent = data.wishlist_count;
    }

    // Toggle wishlist heart
    if(btn && type === 'add_to_wishlist'){
      if(data.status === 'success'){
        btn.innerHTML = '❤️';
        btn.classList.add('active');
        btn.title = 'Already in Wishlist';
      } else if(data.status === 'removed'){ 
        btn.innerHTML = '🤍';
        btn.classList.remove('active');
        btn.title = 'Add to Wishlist';
      }
    }
  }

  function handleAjax(btn, actionType){
    const product_id = btn.dataset.id;
    fetch('ajax_handler.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: new URLSearchParams({action: actionType, product_id})
    })
    .then(res => res.json())
    .then(data => {
      if(data.status === 'login_required'){
        window.location.href = 'login.php';
      } else if(data.status === 'success' || data.status === 'removed'){
        Swal.fire({icon:'success', title:data.message, timer:1500, showConfirmButton:false});
        updateCounts(data, btn, actionType);
      } else if(data.status === 'error'){
        Swal.fire({icon:'error', title:data.message, timer:1500, showConfirmButton:false});
      }
    })
    .catch(()=>{
      Swal.fire({icon:'error', title:'Something went wrong!', timer:1500, showConfirmButton:false});
    });
  }

  // Add to Cart
  document.querySelectorAll('.ajax-add-cart').forEach(btn=>{
    btn.addEventListener('click', ()=>handleAjax(btn, 'add_to_cart'));
  });

  // Add/Remove Wishlist
  document.querySelectorAll('.ajax-add-wishlist').forEach(btn=>{
    btn.addEventListener('click', ()=>handleAjax(btn, 'add_to_wishlist'));
  });

});

</script>





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

/* Wishlist Heart Button on Top-Right */
.wishlist-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 38px;
  height: 38px;
  background: white;
  color: #ff4d6d;
  border: none;
  border-radius: 50%;
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
  z-index: 10;
}

/* Hover Effect */
.wishlist-btn:hover {
  transform: scale(1.2);
  box-shadow: 0 6px 18px rgba(0,0,0,0.2);
}

/* Active State */
.wishlist-btn.active {
  background: #ffe6ea;
  color: #ff4d6d;
}

/* Ensure product card is positioned relatively */
.product-card {
  position: relative; /* important for absolute wishlist btn */
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 20px;
}

/* Product Image Zoom */
.product-img {
  width: 100%;
  height: 230px;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

</style>

<!-- 💥 PREMIUM PROMO CTA -->
<section class="cta-premium py-5 position-relative overflow-hidden text-center">
  <!-- Background Gradient Animation -->
  <div class="cta-bg position-absolute top-0 start-0 w-100 h-100"></div>

  <!-- Floating particles/icons -->
  <div class="cta-particles">
    <i class="bi bi-lightning-charge-fill particle"></i>
    <i class="bi bi-star-fill particle"></i>
    <i class="bi bi-cpu-fill particle"></i>
    <i class="bi bi-headphones particle"></i>
    <i class="bi bi-tv-fill particle"></i>
  </div>

  <div class="container position-relative" style="z-index:2">
    <h3 class="fw-bold display-5 mb-3 shimmer-text">🎉 Get <span>20% Off</span> Your First Order!</h3>
    <p class="lead text-light mb-4">Sign up now and enjoy exclusive deals and new arrivals every week.</p>
    <a href="register.php" class="btn btn-light btn-lg rounded-pill cta-btn">Join Now</a>
  </div>
</section>

<style>
/* CTA Premium Section */
.cta-premium {
  border-radius: 2rem;
  background: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa, #818cf8);
  background-size: 400% 400%;
  position: relative;
  overflow: hidden;
  box-shadow: 0 12px 35px rgba(0,0,0,0.25);
  animation: gradientBG 15s ease infinite;
}

.cta-bg {
  position: absolute;
  top:0; left:0;
  width:100%; height:100%;
  filter: blur(80px);
  z-index:1;
  opacity:0.5;
}

/* Gradient Animation */
@keyframes gradientBG {
  0%{background-position:0% 50%;}
  50%{background-position:100% 50%;}
  100%{background-position:0% 50%;}
}

/* Shimmer Text */
.shimmer-text span {
  background: linear-gradient(90deg, #fff, #ffd700, #fff);
  background-size: 200% 100%;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shimmer 2.5s infinite;
}

@keyframes shimmer {
  0%{background-position:-200% 0;}
  100%{background-position:200% 0;}
}

/* Button */
.cta-btn {
  font-weight: 700;
  padding: 14px 40px;
  transition: all 0.4s ease;
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}
.cta-btn:hover {
  transform: translateY(-5px) scale(1.08);
  box-shadow: 0 15px 35px rgba(0,0,0,0.35);
}

/* Floating Particles */
.cta-particles .particle {
  position: absolute;
  font-size: 1.5rem;
  color: rgba(255,255,255,0.6);
  animation: float 6s linear infinite;
}

.cta-particles .particle:nth-child(1){ top:10%; left:20%; animation-delay: 0s; }
.cta-particles .particle:nth-child(2){ top:30%; left:80%; animation-delay: 1.2s; }
.cta-particles .particle:nth-child(3){ top:60%; left:15%; animation-delay: 2.5s; }
.cta-particles .particle:nth-child(4){ top:50%; left:60%; animation-delay: 3.7s; }
.cta-particles .particle:nth-child(5){ top:80%; left:40%; animation-delay: 5s; }

@keyframes float {
  0% { transform: translateY(0) rotate(0deg); opacity:0.6; }
  50% { transform: translateY(-20px) rotate(180deg); opacity:1; }
  100% { transform: translateY(0) rotate(360deg); opacity:0.6; }
}

/* Scroll Entrance Animation */
.cta-premium h3, .cta-premium p, .cta-btn {
  opacity:0;
  transform: translateY(30px);
  animation: fadeInUp 1s forwards;
}
.cta-premium h3 { animation-delay: 0.3s; }
.cta-premium p { animation-delay: 0.6s; }
.cta-premium .cta-btn { animation-delay: 0.9s; }

@keyframes fadeInUp {
  to { opacity:1; transform:translateY(0); }
}

/* Responsive */
@media(max-width:768px){
  .cta-premium h3{ font-size:2rem; }
  .cta-premium p{ font-size:1rem; }
}
</style>


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


