<?php
require_once 'init.php'; // PDO connection
include 'includes/header.php';

if (!isset($_GET['category_id'])) {
    die("Category not specified.");
}

$category_id = (int)$_GET['category_id'];

// Get category name
$cat_sql = "SELECT name FROM categories WHERE id = :id";
$cat_stmt = $pdo->prepare($cat_sql);
$cat_stmt->execute(['id' => $category_id]);
$category_row = $cat_stmt->fetch(PDO::FETCH_ASSOC);

if (!$category_row) {
    die("Category not found.");
}

$category = $category_row['name'];

// Get products in this category
$sql = "SELECT * FROM products WHERE category_id = :id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $category_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <h2 class="mb-5 fw-bold text-center text-gradient-primary display-5"><?php echo htmlspecialchars($category); ?> Products</h2>

    <?php if (count($products) > 0): ?>
        <div class="row g-4">
            <?php foreach($products as $row): ?>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-lg product-card rounded-4 overflow-hidden">
                        <div class="position-relative overflow-hidden product-img-wrapper">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($row['name']); ?>">

                            <!-- Overlay Buttons -->
                            <div class="overlay d-flex flex-column justify-content-center align-items-center gap-2">
                                <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning fw-bold w-75 shadow-sm">View</a>
                                <button class="btn btn-success fw-bold w-75 shadow-sm add-cart" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>">Add to Cart</button>
                                <button class="btn btn-danger fw-bold w-75 shadow-sm add-wishlist" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>">Wishlist</button>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title fw-semibold"><?php echo htmlspecialchars($row['name']); ?></h6>
                            <p class="text-muted fw-bold mb-1">$<?php echo number_format($row['price'], 2); ?></p>

                            <!-- Optional Star Rating -->
                            <div class="d-flex justify-content-center gap-1">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <i class="far fa-star text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-secondary fs-5">No products found in this category.</p>
    <?php endif; ?>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cartButtons = document.querySelectorAll('.add-cart');
    const wishlistButtons = document.querySelectorAll('.add-wishlist');

    cartButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const productName = btn.dataset.name;
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart',
                text: productName + ' has been added to your cart!',
                showConfirmButton: false,
                timer: 1500,
                backdrop: true
            });
        });
    });

    wishlistButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const productName = btn.dataset.name;
            Swal.fire({
                icon: 'success',
                title: 'Added to Wishlist',
                text: productName + ' has been added to your wishlist!',
                showConfirmButton: false,
                timer: 1500,
                backdrop: true
            });
        });
    });
});
</script>

<style>
/* Gradient Heading */
.text-gradient-primary {
    background: linear-gradient(90deg, #ff6f61, #ff9472);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Product Card */
.product-card {
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.product-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
}

/* Image Zoom */
.product-img-wrapper {
    position: relative;
    overflow: hidden;
}
.product-img {
    transition: transform 0.5s ease;
}
.product-card:hover .product-img {
    transform: scale(1.12);
}

/* Overlay Buttons */
.overlay {
    position: absolute;
    inset: 0;
    background-color: rgba(0,0,0,0.65);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 0.6rem;
}
.product-card:hover .overlay {
    opacity: 1;
}

/* Button Effects */
.overlay .btn {
    border-radius: 50px;
    padding: 0.5rem 1.2rem;
    font-weight: 600;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.overlay .btn:hover {
    transform: scale(1.08);
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
}

/* Star Rating */
.card-body .fa-star, .card-body .fa-star-half-alt, .card-body .far.fa-star {
    font-size: 0.9rem;
}
</style>

<?php
include 'includes/footer.php';
?>
