<?php
require_once __DIR__ . '/../init.php';
// In a real system, check that admin is logged in (with a role)

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $cat = $_POST['category_id'];
    $ins = $pdo->prepare("INSERT INTO products (category_id, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
    $ins->execute([$cat, $name, $desc, $price, $image]);
    redirect('products.php');
}

// Fetch list
$stmt = $pdo->query("SELECT p.*, c.name AS catname FROM products p LEFT JOIN categories c ON p.category_id = c.id");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<h2>Manage Products</h2>

<h3>Add Product</h3>
<form method="post">
  <label>Name: <input name="name"></label><br>
  <label>Description: <textarea name="description"></textarea></label><br>
  <label>Price: <input name="price" type="number" step="0.01"></label><br>
  <label>Image URL: <input name="image"></label><br>
  <label>Category ID: <input name="category_id"></label><br>
  <button name="add">Add</button>
</form>

<h3>Existing Products</h3>
<table border="1">
  <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th></tr>
  <?php foreach ($products as $p): ?>
    <tr>
      <td><?php echo $p['id']; ?></td>
      <td><?php echo htmlspecialchars($p['name']); ?></td>
      <td><?php echo htmlspecialchars($p['catname']); ?></td>
      <td><?php echo number_format($p['price'],2); ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
