<?php
require_once 'init.php';

if (!is_logged_in()) {
    $_SESSION['after_login_redirect'] = 'wishlist.php';
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$list = get_wishlist_items($pdo, $user_id);
$titleName = "Wishlist";
include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="fw-bold mb-5">Your Wishlist</h2>

    <?php if (empty($list)): ?>
        <div class="empty-wishlist my-5 text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/4903/4903482.png" alt="Empty Wishlist" style="width:220px; opacity:0.9; margin-bottom:25px;">
            <h3>Your Wishlist is Empty 💔</h3>
            <p>You haven’t saved any items yet.<br>Browse our collection and add your favorites!</p>
            <a href="index.php" class="btn btn-primary px-4 py-2">Explore Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4" id="wishlist-items">
            <?php foreach ($list as $item): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 col-6 wishlist-item" data-id="<?= $item['product_id']; ?>">
                    <div class="card wishlist-card border-0 shadow-sm h-100">
                        <div class="position-relative">
                            <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="card-img-top rounded-top wishlist-img">
                            <span class="price-tag badge bg-primary position-absolute top-0 start-0 m-2 px-2 py-1">$<?= number_format($item['price'],2); ?></span>
                            <button class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-2 remove-wishlist-btn" 
                                    data-product-id="<?= $item['product_id']; ?>" 
                                    title="Remove from Wishlist">
                                <i class="bi bi-x-lg text-danger"></i>
                            </button>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h6 class="card-title text-center fw-semibold mb-3"><?= htmlspecialchars($item['name']); ?></h6>
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <a href="product.php?id=<?= $item['product_id']; ?>" class="btn btn-sm btn-outline-primary w-50">View</a>
                                <button class="btn btn-sm btn-primary w-50 add-cart-btn" data-product-id="<?= $item['product_id']; ?>">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const wishlistContainer = document.querySelector('#wishlist-items');

    function updateCartCount(count){
        const el = document.querySelector('#cartCount');
        if(el) el.textContent = count;
    }

    function updateWishlistCount(count){
        const el = document.querySelector('#wishlistCount');
        if(el) el.textContent = count;
    }

    if(!wishlistContainer) return; // Stop if container does not exist

    wishlistContainer.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.add-cart-btn');
        const removeBtn = e.target.closest('.remove-wishlist-btn');

        // Add to Cart
        if(addBtn){
            const pid = addBtn.dataset.productId;
            fetch('ajax_handler.php', {
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body: new URLSearchParams({action:'add_to_cart', product_id: pid})
            }).then(r=>r.json()).then(data=>{
                if(data.status==='login_required'){
                    window.location='login.php';
                } else if(data.status==='success'){
                    Swal.fire({icon:'success', title:data.message, timer:1500, showConfirmButton:false});
                    updateCartCount(data.cart_count);
                } else {
                    Swal.fire({icon:'error', title:data.message, timer:1500, showConfirmButton:false});
                }
            });
            return; // Stop here
        }

        // Remove from Wishlist with confirmation
if(removeBtn){
    const pid = removeBtn.dataset.productId;
    const itemName = removeBtn.closest('.wishlist-item').querySelector('.card-title').textContent;

    Swal.fire({
        title: `Remove "${itemName}" from wishlist?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed){
            fetch('ajax_handler.php', {
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body: new URLSearchParams({action:'remove_from_wishlist', product_id: pid})
            }).then(r=>r.json()).then(data=>{
                if(data.status==='login_required'){
                    window.location='login.php';
                } else if(data.status==='success'){
                    Swal.fire({icon:'success', title:'Removed from wishlist', timer:1500, showConfirmButton:false});
                    // Remove item from DOM
                    const card = wishlistContainer.querySelector(`.wishlist-item[data-id='${pid}']`);
                    if(card) card.remove();
                    updateWishlistCount(data.wishlist_count);
                    // Show empty message if no items left
                    if(wishlistContainer.querySelectorAll('.wishlist-item').length === 0){
                        wishlistContainer.innerHTML = `
                            <div class="empty-wishlist my-5 text-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/4903/4903482.png" alt="Empty Wishlist" style="width:220px; opacity:0.9; margin-bottom:25px;">
                                <h3>Your Wishlist is Empty 💔</h3>
                                <p>You haven’t saved any items yet.<br>Browse our collection and add your favorites!</p>
                                <a href="index.php" class="btn btn-primary px-4 py-2">Explore Products</a>
                            </div>
                        `;
                    }
                } else {
                    Swal.fire({icon:'error', title:data.message || 'Failed to remove', timer:1500, showConfirmButton:false});
                }
            });
        }
    });

    return; // Stop here
}

    });

});


</script>

<style>
.wishlist-card { transition: all 0.25s ease-in-out; border-radius:1rem; overflow:hidden; }
.wishlist-card:hover { transform: translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.1); }
.wishlist-img { object-fit: cover; height:230px; width:100%; }
.price-tag { font-size:0.9rem; border-radius:0.5rem; }
@media (max-width:768px){ .wishlist-img { height:200px; } }
</style>
