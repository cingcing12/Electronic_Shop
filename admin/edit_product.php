<?php
// admin/edit_product.php

require_once __DIR__ . '/../init.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Product ID missing.";
    exit;
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $cat_id = $_POST['category_id'];

    $upd = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $upd->execute([$cat_id, $name, $desc, $price, $image, $id]);
    echo "<p>Product updated.</p>";
    // Reload product
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

include __DIR__ . '/../includes/header.php';
?>

<h2>Edit Product</h2>

<form method="post">
  <label>Name: <input name="name" value="<?= htmlspecialchars($product['name']) ?>"></label><br>
  <label>Description:<br>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>
  </label><br>
  <label>Price: <input name="price" type="number" step="0.01" value="<?= $product['price'] ?>"></label><br>
  <label>Image URL: <input name="image" value="<?= htmlspecialchars($product['image']) ?>"></label><br>
  <label>Category ID: <input name="category_id" value="<?= $product['category_id'] ?>"></label><br>
  <button type="submit">Update</button>
</form>

<p><a href="products.php">← Back to Products</a></p>

<?php include __DIR__ . '/../includes/footer.php'; ?>
