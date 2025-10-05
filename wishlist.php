<?php
require_once 'init.php';

// Check if the user is logged in
if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'wishlist.php'; // Redirect to wishlist after login
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$list = get_wishlist_items($pdo, $user_id);

// Handle Remove from Wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $product_id = $_POST['product_id'];
    remove_from_wishlist($pdo, $user_id, $product_id);
    $_SESSION['wishlist_removed'] = true; // Set success flag for SweetAlert
    redirect('wishlist.php'); // Refresh the page after removing from wishlist
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;  // Default to 1 if no quantity is provided

    // Add product to cart (this function needs to be implemented)
    add_to_cart($pdo, $user_id, $product_id, $quantity);

    $_SESSION['cart_added'] = true; // Set success flag for SweetAlert
    redirect('wishlist.php'); // Refresh the page after adding to cart
}

include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Your Wishlist</h2>

    <?php if (empty($list)): ?>
        <div class="alert alert-info text-center">Your wishlist is empty.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($list as $it): ?>
                <div class="col">
                    <div class="card shadow-sm border-light">
                        <img src="<?= htmlspecialchars($it['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($it['name']); ?>" style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($it['name']); ?></h5>
                            <p class="card-text text-muted">$<?= number_format($it['price'], 2); ?></p>

                            <!-- Product Rating (Dynamic Star Display) -->
                            <div class="mb-3">
                                <?php
                                // Simulate a rating for demo purposes (4.5 stars)
                                $rating = 4.5;  // This would come from the database, but it's hardcoded for demo purposes

                                // Calculate the number of full, half, and empty stars
                                $fullStars = floor($rating); // Full stars
                                $emptyStars = 5 - ceil($rating); // Empty stars
                                $halfStars = 5 - $fullStars - $emptyStars; // Half stars

                                // Render the full stars (yellow)
                                for ($i = 0; $i < $fullStars; $i++) {
                                    echo '<i class="fas fa-star text-warning"></i>';
                                }

                                // Render the half star (yellow)
                                if ($halfStars > 0) {
                                    echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                }

                                // Render the empty stars (gray)
                                for ($i = 0; $i < $emptyStars; $i++) {
                                    echo '<i class="far fa-star text-warning"></i>';
                                }
                                ?>
                            </div>

                            <div class="mt-auto d-flex gap-2">
                                <a href="product.php?id=<?= $it['product_id']; ?>" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="View Product">View</a>
                                
                                <!-- Remove Button with Tooltip -->
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeModal" data-product-id="<?= $it['product_id']; ?>" data-product-name="<?= htmlspecialchars($it['name']); ?>" title="Remove from Wishlist">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>

                                <!-- Add to Cart Button -->
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $it['product_id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm" style="width: 60px; display: inline-block;">
                                    <button name="add_to_cart" class="btn btn-outline-success btn-sm">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for Confirming Removal -->
<div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeModalLabel">Confirm Removal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove <strong id="product-name"></strong> from your wishlist?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="removeForm" class="d-inline">
                    <input type="hidden" name="product_id" id="product-id">
                    <button name="remove_from_wishlist" type="submit" class="btn btn-danger">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Show SweetAlert if product is removed from wishlist successfully
if (isset($_SESSION['wishlist_removed']) && $_SESSION['wishlist_removed']) {
    unset($_SESSION['wishlist_removed']); // Clear the session flag
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Product removed from wishlist!',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
<?php } ?>

<?php
// Show SweetAlert if product is added to cart successfully
if (isset($_SESSION['cart_added']) && $_SESSION['cart_added']) {
    unset($_SESSION['cart_added']); // Clear the session flag
?>
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
<?php } ?>

<?php include 'includes/footer.php'; ?>

<script>
    // Enable Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle the modal data for removal
    const removeModal = document.getElementById('removeModal');
    removeModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const productId = button.getAttribute('data-product-id');
        const productName = button.getAttribute('data-product-name');
        
        const productIdInput = removeModal.querySelector('#product-id');
        const productNameLabel = removeModal.querySelector('#product-name');

        productIdInput.value = productId;
        productNameLabel.textContent = productName;
    });
</script>
