<?php
require_once 'init.php';
require_once 'includes/functions.php'; // Make sure redirect() is here

// Fetch single product
$product_id = $_GET['id'] ?? null;
if (!$product_id) redirect('index.php');

$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

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

// Fetch related products
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id=:cat AND id!=:pid LIMIT 12");
$related_stmt->execute(['cat'=>$product['category_id'], 'pid'=>$product_id]);
$related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

$titleName = $product["name"];
include 'includes/header.php';
?>

<!-- MAIN PRODUCT -->
<div class="container my-5">
  <div class="row g-4 align-items-center">
    <!-- Product Image -->
    <div class="col-md-6">
      <div class="product-image-card position-relative rounded-4 overflow-hidden shadow-sm">
        <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="img-fluid product-imgTop2">

        <!-- Wishlist Button -->
        <?php if($user_id): ?>
        <button class="btn btn-lg wishlist-btn <?= in_array($product_id, $wishlist_ids) ? 'active' : '' ?>" 
                data-id="<?= $product_id ?>" title="Wishlist">
            <i class="bi <?= in_array($product_id, $wishlist_ids) ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
        </button>
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

      <button class="btn btn-primary btn-lg btn-hover-shadow add-cart-btn" data-id="<?= $product['id'] ?>">
        <i class="bi bi-cart-plus me-2"></i> Add to Cart
      </button>
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
          
          <a href="product.php?id=<?= $p['id']; ?>">
            <img src="<?= htmlspecialchars($p['image']); ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['name']); ?>">
          </a>

          <?php if ($user_id): ?>
            <button class="btn wishlist-btn <?= in_array($p['id'], $wishlist_ids) ? 'active' : '' ?>" 
                    data-id="<?= $p['id'] ?>" title="Wishlist">
                <i class="bi <?= in_array($p['id'], $wishlist_ids) ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
            </button>
          <?php else: ?>
            <a href="login.php" class="wishlist-btn nav-link">🤍</a>
          <?php endif; ?>

          <div class="card-body">
            <h6 class="card-title fw-semibold text-truncate"><?= htmlspecialchars($p['name']); ?></h6>
            <p class="fw-bold text-primary mb-3">$<?= number_format($p['price'],2); ?></p>
            <button class="btn btn-sm btn-outline-primary w-100 add-cart-btn" data-id="<?= $p['id'] ?>">
              <i class="bi bi-cart3"></i> Add to Cart
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
.text-gradient-price { background: linear-gradient(90deg,#ff6f61,#ff9472); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.product-card, .product-image-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.product-card:hover, .product-image-card:hover { transform: translateY(-6px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
.product-img, .product-imgTop { width:100%; height:250px; object-fit:cover; transition: transform 0.4s ease; }
.product-card:hover .product-img, .product-image-card:hover .product-imgTop { transform: scale(1.05); }
.wishlist-btn { position:absolute; top:10px; right:10px; background:white; border:none; font-size:1.3rem; cursor:pointer; border-radius:50%; width:38px; height:38px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(0,0,0,0.1); transition: 0.2s ease; }
.wishlist-btn.active { color:#ff4d6d; background:#ffe6ea; }
.add-cart-btn { border-radius:30px; transition: all 0.3s ease; }
.add-cart-btn:hover { background-color:#007bff; color:#fff; }
.text-truncate { white-space: nowrap; overflow:hidden; text-overflow: ellipsis; }
@media (max-width: 767px) { .wishlist-btn { opacity:1 !important; top:8px; right:8px; } }
</style>

<?php include 'includes/footer.php'; ?>

<!-- AJAX + SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

  // Update Cart Count
  function updateCartCount(change = 0, newCount = null) {
    const el = document.querySelector('#cartCount');
    if(!el) return;

    if(newCount !== null){
      el.textContent = newCount; // server returned count
    } else {
      let current = parseInt(el.textContent) || 0;
      el.textContent = current + change; // increment/decrement
    }
  }

  // Update Wishlist Count
  function updateWishlistCount(change = 0, newCount = null) {
    const el = document.querySelector('#wishlistCount');
    if(!el) return;

    if(newCount !== null){
      el.textContent = newCount;
    } else {
      let current = parseInt(el.textContent) || 0;
      el.textContent = current + change;
    }
  }

  // Add to Cart
  document.querySelectorAll('.add-cart-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      const pid = this.dataset.id;
      fetch('ajax_handler.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:new URLSearchParams({action:'add_to_cart', product_id:pid})
      })
      .then(res => res.json())
      .then(data => {
        if(data.status==='login_required'){
          window.location.href='login.php';
        } else if(data.status==='success'){
          Swal.fire({icon:'success',title:data.message,timer:1500,showConfirmButton:false});
          // Update count from server or increment by 1
          updateCartCount(1, data.cart_count);
        } else {
          Swal.fire({icon:'error',title:data.message,timer:1500,showConfirmButton:false});
        }
      });
    });
  });

  // Add to Wishlist
  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      const pid = this.dataset.id;
      fetch('ajax_handler.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:new URLSearchParams({action:'add_to_wishlist', product_id:pid})
      })
      .then(res => res.json())
      .then(data => {
        if(data.status==='login_required'){
          window.location.href='login.php';
        } else if(data.status==='success'){
          Swal.fire({icon:'success',title:data.message,timer:1500,showConfirmButton:false});
          btn.innerHTML = '<i class="bi bi-heart-fill"></i>';
          btn.classList.add('active');
          // Update count from server or increment by 1
          updateWishlistCount(1, data.wishlist_count);
        } else {
          Swal.fire({icon:'error',title:data.message,timer:1500,showConfirmButton:false});
        }
      });
    });
  });

});
</script>
