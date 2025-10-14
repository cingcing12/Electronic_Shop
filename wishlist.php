<?php
require_once 'init.php';

if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'wishlist.php';
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$list = get_wishlist_items($pdo, $user_id);

// Handle Remove from Wishlist
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
    <h2 class="wishlist-title text-center mb-5">My Wishlist</h2>

    <?php if (empty($list)): ?>
        <div class="empty-wishlist">
            <p>Your wishlist is empty. <a href="index.php">Start shopping</a></p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($list as $item): ?>
                <div class="col">
                    <div class="wishlist-card">
                        <div class="img-wrapper">
                            <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                            <span class="price-badge">$<?= number_format($item['price'],2); ?></span>
                            <button data-bs-toggle="modal" data-bs-target="#removeModal" data-product-id="<?= $item['product_id']; ?>" data-product-name="<?= htmlspecialchars($item['name']); ?>">
                                Remove
                            </button>
                        </div>
                        <div class="card-body">
                            <h5><?= htmlspecialchars($item['name']); ?></h5>
                            <div class="actions">
                                <a href="product.php?id=<?= $item['product_id']; ?>">View</a>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1">
                                    <button name="add_to_cart">Add to Cart</button>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Remove Item</h5>
                <button type="button" data-bs-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove <strong id="product-name"></strong> from your wishlist?
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal">Cancel</button>
                <form method="POST">
                    <input type="hidden" name="product_id" id="product-id">
                    <button name="remove_from_wishlist" type="submit">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const removeModal = document.getElementById('removeModal');
removeModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    removeModal.querySelector('#product-id').value = button.getAttribute('data-product-id');
    removeModal.querySelector('#product-name').textContent = button.getAttribute('data-product-name');
});

// SweetAlert notifications
<?php if(isset($_SESSION['wishlist_removed'])): unset($_SESSION['wishlist_removed']); ?>
Swal.fire({icon:'success',title:'Removed from wishlist',position:'top-end',timer:1500,showConfirmButton:false});
<?php endif; ?>

<?php if(isset($_SESSION['cart_added'])): unset($_SESSION['cart_added']); ?>
Swal.fire({icon:'success',title:'Added to cart',position:'top-end',timer:1500,showConfirmButton:false});
<?php endif; ?>
</script>
