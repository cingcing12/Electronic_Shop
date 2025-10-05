<?php
require_once __DIR__ . '/../init.php';

// Simple admin check (customize as needed)
if (!is_logged_in() || !is_admin()) {
    // Redirect non-admin users to login or home page
    redirect('login.php');
}

// Fetch some stats
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
  <h1 class="mb-4">Admin Dashboard</h1>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card text-white bg-primary shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Users</h5>
          <p class="card-text display-4"><?= $userCount ?></p>
          <a href="users.php" class="btn btn-light btn-sm">Manage Users</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-white bg-success shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Products</h5>
          <p class="card-text display-4"><?= $productCount ?></p>
          <a href="products.php" class="btn btn-light btn-sm">Manage Products</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-white bg-warning shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Orders</h5>
          <p class="card-text display-4"><?= $orderCount ?></p>
          <a href="orders.php" class="btn btn-light btn-sm">Manage Orders</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
