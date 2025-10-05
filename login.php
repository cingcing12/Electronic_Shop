<?php
require_once __DIR__ . '/init.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!$username || !$password) {
        $errors[] = "All fields required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $redirect = $_SESSION['after_login_redirect'] ?? 'index.php';
            unset($_SESSION['after_login_redirect']);
            redirect($redirect);
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h3 class="card-title text-center mb-4">Login</h3>

          <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
          <?php endforeach; ?>

          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>

            <p class="mt-3 mb-0 text-center">
              Don't have an account? <a href="register.php">Register here</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
