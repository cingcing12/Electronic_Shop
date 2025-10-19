<?php
require_once 'includes/header.php';

// Delete product
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header('Location: products.php');
    exit;
}

// Fetch categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch products
$products = $pdo->query("SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id=c.id 
    ORDER BY p.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h2>Products</h2>
    <div class="d-flex gap-2 flex-wrap">
        <input type="text" id="searchInput" class="form-control" placeholder="Search product...">
        <select id="categoryFilter" class="form-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <a href="edit_product.php" class="btn btn-gradient"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
    </div>
</div>

<!-- Products Table -->
<div class="card glass-card shadow-lg p-3">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0" id="productsTable">
            <thead>
                <tr style="background: linear-gradient(135deg,#11998e,#38ef7d); color:#fff;">
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Created At</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr class="align-middle">
                    <td><?= $p['id'] ?></td>
                    <td><img src="<?= htmlspecialchars($p['image']) ?>" width="60" height="60" class="rounded shadow-sm"></td>
                    <td class="product-name"><?= htmlspecialchars($p['name']) ?></td>
                    <td class="product-category"><?= htmlspecialchars($p['category_name']) ?></td>
                    <td><span class="fw-bold">$<?= number_format($p['price'],2) ?></span></td>
                    <td><span class="badge text-dark bg-gradient-secondary"><?= date('M d, Y', strtotime($p['created_at'])) ?></span></td>
                    <td class="text-center">
                        <!-- Edit Button -->
                        <button class="btn btn-sm btn-gradient-warning mb-1 btn-edit-product" data-id="<?= $p['id'] ?>" data-bs-toggle="modal" data-bs-target="#editProductModal"><i class="bi bi-pencil-square"></i> Edit</button>
                        <!-- Delete Button -->
                        <a href="products.php?delete=<?= $p['id'] ?>" class="btn btn-sm btn-gradient mb-1" onclick="return confirm('Delete product?')"><i class="bi bi-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($products) == 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No products found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <input type="hidden" id="editProductId" name="id">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editProductName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductCategory" class="form-label">Category</label>
                        <select class="form-select" id="editProductCategory" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="editProductPrice" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductImage" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="editProductImage" name="image" required>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-gradient-warning">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
/* Glass Card + Table Styles */
.glass-card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.glass-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.2); }

.table-responsive { border-radius: 15px; overflow-x:auto; }
.table tbody tr { transition: background 0.3s, transform 0.3s; }
.table tbody tr:hover { background: rgba(255,255,255,0.1); transform: translateX(3px); }

.badge { font-size:0.85rem; padding:0.5em 0.75em; }

.btn-gradient {
    background: linear-gradient(135deg,#ff416c,#ff4b2b);
    border:none; color:#fff; transition: transform 0.2s, box-shadow 0.2s;
}
.btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,75,43,0.4); }

.btn-gradient-warning {
    background: linear-gradient(135deg,#f7971e,#ffd200);
    color:#fff;
}
.btn-gradient-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,209,0,0.4);
}

/* Responsive Table */
@media(max-width:768px){
    .table thead { display:none; }
    .table tbody tr { display:block; margin-bottom:15px; background: rgba(255,255,255,0.05); padding:10px; border-radius:12px; }
    .table tbody td { display:flex; justify-content: space-between; padding:5px 10px; }
    .table tbody td:before { display:none; }
}
</style>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Live Search & Category Filter
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const table = document.getElementById('productsTable').getElementsByTagName('tbody')[0];

// Filter products by search input and category
function filterTable() {
    const searchText = searchInput.value.toLowerCase();
    const categoryText = categoryFilter.value.toLowerCase();

    Array.from(table.rows).forEach(row => {
        const name = row.querySelector('.product-name')?.innerText.toLowerCase() || '';
        const category = row.querySelector('.product-category')?.innerText.toLowerCase() || '';

        if (name.includes(searchText) && (categoryText === '' || category.includes(categoryText))) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', filterTable);
categoryFilter.addEventListener('change', filterTable);

// Fetch product data for editing
document.querySelectorAll('.btn-edit-product').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.getAttribute('data-id');
        
        fetch(`get_product_data.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editProductId').value = data.product.id;
                    document.getElementById('editProductName').value = data.product.name;
                    document.getElementById('editProductCategory').value = data.product.category_id;
                    document.getElementById('editProductPrice').value = data.product.price;
                    document.getElementById('editProductImage').value = data.product.image;
                }
            })
            .catch(error => console.error('Error:', error));
    });
});

// Handle form submission for product edit
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('update_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Product Updated!',
                text: data.message,
                confirmButtonText: 'OK'
            }).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!',
            confirmButtonText: 'OK'
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
