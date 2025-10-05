<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>My Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- FontAwesome for Rating Stars -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">


  <style>
    body {
      padding-top: 70px;
      background-color: #f9f9f9;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }

    .nav-link {
      font-size: 1.1rem;
    }

    footer {
      margin-top: 50px;
      padding: 20px 0;
      text-align: center;
      background-color: #f1f1f1;
    }

    .card:hover {
      transform: translateY(-3px);
      transition: 0.2s ease-in-out;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .card-title {
      font-size: 1rem;
      font-weight: 600;
    }
  </style>
</head>

<body>

  <!-- Navbar with Offcanvas -->
  <nav class="navbar navbar-dark bg-dark navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="index.php">My Shop</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
            <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>

            <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
              <li class="nav-item"><a class="nav-link" href="account.php">Account</a></li>
              <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- Page content starts here -->
  <div class="container">