<?php
require_once 'init.php';

if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'wishlist.php';
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$list = get_wishlist_items($pdo, $user_id);

// Handle Remove
if (isset($_POST['remove_from_wishlist'])) {
    remove_from_wishlist($pdo, $user_id, $_POST['product_id']);
    $_SESSION['wishlist_removed'] = true;
    redirect('wishlist.php');
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    add_to_cart($pdo, $user_id, $_POST['product_id'], $_POST['quantity'] ?? 1);
    $_SESSION['cart_added'] = true;
    redirect('wishlist.php');
}

include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="fw-bold mb-5">Your Wishlist</h2>

    <?php if (empty($list)): ?>
        <!-- Empty Wishlist Styled Like Empty Cart -->
        <style>
          .empty-wishlist {
            text-align: center;
            padding: 100px 20px;
            background: linear-gradient(135deg, #eef2ff, #f9fafb);
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            animation: fadeIn 0.8s ease-in-out;
          }
          .empty-wishlist img {
            width: 220px;
            opacity: 0.9;
            margin-bottom: 25px;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));
          }
          .empty-wishlist h3 {
            font-weight: 700;
            color: #1e1e2f;
          }
          .empty-wishlist p {
            color: #555;
            font-size: 1.05rem;
            margin-bottom: 30px;
          }
          .empty-wishlist .btn {
            border-radius: 50px;
            font-weight: 600;
            padding: 12px 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(99,102,241,0.3);
          }
          .empty-wishlist .btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(99,102,241,0.35);
          }
          @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
          }
        </style>

        <div class="empty-wishlist my-5">
          <img src="https://cdn-icons-png.flaticon.com/512/4903/4903482.png" alt="Empty Wishlist">
          <h3>Your Wishlist is Empty 💔</h3>
          <p>You haven’t saved any items yet.<br>Browse our collection and add your favorites!</p>
          <a href="index.php" class="btn">Explore Products</a>
        </div>

    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($list as $item): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 col-6">
                    <div class="card wishlist-card border-0 shadow-sm h-100">
                        <div class="position-relative">
                            <img src="<?= htmlspecialchars($item['image']); ?>" 
                                 alt="<?= htmlspecialchars($item['name']); ?>" 
                                 class="card-img-top rounded-top wishlist-img">
                            <span class="price-tag badge bg-primary position-absolute top-0 start-0 m-2 px-2 py-1">
                                $<?= number_format($item['price'], 2); ?>
                            </span>
                            <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-2 shadow-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#removeModal" 
                                    data-product-id="<?= $item['product_id']; ?>" 
                                    data-product-name="<?= htmlspecialchars($item['name']); ?>">
                                <i class="bi bi-x-lg text-danger"></i>
                            </button>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h6 class="card-title text-center fw-semibold mb-3"><?= htmlspecialchars($item['name']); ?></h6>
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <a href="product.php?id=<?= $item['product_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary w-50">View</a>
                                <form method="POST" class="w-50">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button name="add_to_cart" class="btn btn-sm btn-primary w-100">Add</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Remove Modal -->
<div class="modal fade" id="removeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">Remove Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove <strong id="product-name"></strong> from your wishlist?
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST">
                    <input type="hidden" name="product_id" id="product-id">
                    <button name="remove_from_wishlist" type="submit" class="btn btn-danger">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JS -->
<script>
const removeModal = document.getElementById('removeModal');
removeModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    removeModal.querySelector('#product-id').value = button.getAttribute('data-product-id');
    removeModal.querySelector('#product-name').textContent = button.getAttribute('data-product-name');
});

// SweetAlert messages
<?php if(isset($_SESSION['wishlist_removed'])): unset($_SESSION['wishlist_removed']); ?>
Swal.fire({ icon:'success', title:'Removed from wishlist', position:'top-end', timer:1500, showConfirmButton:false });
<?php endif; ?>

<?php if(isset($_SESSION['cart_added'])): unset($_SESSION['cart_added']); ?>
Swal.fire({ icon:'success', title:'Added to cart', position:'top-end', timer:1500, showConfirmButton:false });
<?php endif; ?>
</script>

<style>
/* Wishlist Card Styles */
.wishlist-card {
    transition: all 0.25s ease-in-out;
    border-radius: 1rem;
    overflow: hidden;
}
.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.wishlist-img {
    object-fit: cover;
    height: 230px;
}
.price-tag {
    font-size: 0.9rem;
    border-radius: 0.5rem;
}
.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}
@media (max-width: 768px) {
    .wishlist-img { height: 200px; }
}
</style>
