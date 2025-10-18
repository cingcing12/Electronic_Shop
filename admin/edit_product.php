<?php
require_once 'includes/header.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$id = $_GET['id'] ?? null;
$product = null;

if($id){
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_POST['image'];

    if($id){
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category_id=?, image=? WHERE id=?");
        $stmt->execute([$name,$desc,$price,$category,$image,$id]);
    }else{
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image) VALUES (?,?,?,?,?)");
        $stmt->execute([$name,$desc,$price,$category,$image]);
    }
    header('Location: products.php');
    exit;
}
?>

<div class="card shadow-lg p-4 glass-card" style="max-width:700px; margin:auto;">
    <h2 class="mb-4"><?= $id ? 'Edit' : 'Add' ?> Product</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control" step="0.01" value="<?= $product['price'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($product['category_id']??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input type="text" name="image" id="imageInput" class="form-control" value="<?= htmlspecialchars($product['image'] ?? '') ?>" required>
        </div>
        <div class="mb-3 text-center">
            <img id="imagePreview" src="<?= htmlspecialchars($product['image'] ?? '') ?>" alt="Image Preview" class="img-fluid rounded shadow-sm" style="max-height:200px;">
        </div>
        <button class="btn btn-success w-100"><?= $id ? 'Update' : 'Add' ?> Product</button>
    </form>
</div>

<style>
.glass-card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}
</style>

<script>
// Live image preview
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');

imageInput.addEventListener('input', () => {
    imagePreview.src = imageInput.value || '';
});
</script>

<?php include 'includes/footer.php'; ?>
