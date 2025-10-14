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

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<!-- FontAwesome for Rating Stars -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">





  <style>
    /* Product Card Styling */
.product-card {
    border-radius: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}
.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

/* Image hover overlay */
.card-img-wrapper {
    position: relative;
}
.product-img {
    transition: transform 0.3s ease;
}
.product-card:hover .product-img {
    transform: scale(1.1);
}
.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 1rem 1rem 0 0;
}
.product-card:hover .overlay {
    opacity: 1;
}
.overlay .btn {
    background: linear-gradient(90deg, #ff6f61, #ff9472);
    border: none;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.overlay .btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* Gradient Heading */
.text-gradient-primary {
    background: linear-gradient(90deg, #ff6f61, #ff9472);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Product Card */
.product-card {
    border-radius: 1.2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
}

/* Image Zoom on Hover */
.product-img {
    transition: transform 0.4s ease;
}
.product-card:hover .product-img {
    transform: scale(1.1);
}

/* Overlay Buttons */
.overlay {
    position: absolute;
    inset: 0;
    background-color: rgba(0,0,0,0.55);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    border-radius: 1.2rem 1.2rem 0 0;
}
.product-card:hover .overlay {
    opacity: 1;
}

.overlay .btn {
    border-radius: 50px;
    padding: 0.5rem 1.2rem;
    font-weight: 600;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.overlay .btn:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

/* Card body */
.card-body h6 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}
.card-body p {
    font-size: 0.95rem;
    color: #555;
}

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

    .category-card {
  background: #fff;
  border: 1px solid #e0e0e0;
  transition: all 0.3s ease;
  cursor: pointer;
}
.category-card:hover {
  transform: translateY(-8px) scale(1.05);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  border-color: #ff6f61;
}
.icon-wrapper {
  width: 60px;
  height: 60px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: linear-gradient(135deg, #ff6f61, #ff9472);
  color: #fff;
  font-size: 1.5rem;
  transition: transform 0.3s ease;
}
.category-card:hover .icon-wrapper {
  transform: scale(1.2);
}
.category-card h6 {
  margin-top: 10px;
  font-size: 1rem;
  transition: color 0.3s ease;
}
.category-card:hover h6 {
  color: #ff6f61;
}

  </style>
</head>

<body>

  <!-- Modern Navbar with Offcanvas -->
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">My Shop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column justify-content-start">
        <ul class="navbar-nav flex-grow-1 pe-3 align-items-lg-center">
          <li class="nav-item my-1">
            <a class="nav-link d-flex align-items-center px-3 py-2 rounded hover-bg-light" href="index.php">
              <i class="fas fa-home me-2"></i> Home
            </a>
          </li>
          <li class="nav-item my-1">
            <a class="nav-link d-flex align-items-center px-3 py-2 rounded hover-bg-light" href="cart.php">
              <i class="fas fa-shopping-cart me-2"></i> Cart
            </a>
          </li>
          <li class="nav-item my-1">
            <a class="nav-link d-flex align-items-center px-3 py-2 rounded hover-bg-light" href="wishlist.php">
              <i class="fas fa-heart me-2"></i> Wishlist
            </a>
          </li>

          <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
            <li class="nav-item my-1">
              <a class="nav-link d-flex align-items-center px-3 py-2 rounded hover-bg-light" href="account.php">
                <i class="fas fa-user me-2"></i> Account
              </a>
            </li>
            <li class="nav-item my-1">
              <a class="nav-link d-flex align-items-center px-3 py-2 rounded hover-bg-light" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout 
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item my-1 ms-lg-2">
              <a class="btn btn-outline-warning w-100 my-1 rounded-pill" href="login.php">Login</a>
            </li>
            <li class="nav-item my-1 ms-lg-2">
              <a class="btn btn-warning w-100 my-1 rounded-pill text-dark fw-bold" href="register.php">Register</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</nav>

<style>
/* Gradient Navbar */
.navbar.bg-gradient {
  background: linear-gradient(90deg, #0b0425ff, #3414a8ff) !important;
}

/* Hover background for links */
.hover-bg-light:hover {
  background-color: rgba(255, 255, 255, 0.1);
  transition: 0.3s;
}

/* Offcanvas link styles */
.offcanvas-body .nav-link {
  font-size: 1.1rem;
  font-weight: 500;
  color: #fff;
  transition: all 0.3s ease;
}

.offcanvas-body .nav-link:hover {
  color: #ffebcd;
  transform: translateX(5px);
}

/* Buttons */
.btn-outline-warning {
  border: 2px solid #ffebcd;
  color: #ffebcd;
  transition: 0.3s;
}
.btn-outline-warning:hover {
  background-color: #ffebcd;
  color: #000;
}

.btn-warning {
  background: #ffebcd;
  border: none;
}
.btn-warning:hover {
  background: #ffdca3;
}

/* Navbar brand */
.navbar-brand {
  font-size: 1.6rem;
  letter-spacing: 1px;
  color: #fff;
  transition: 0.3s;
}
.navbar-brand:hover {
  color: #ffdca3;
}

/* Add subtle shadow to offcanvas */
.offcanvas {
  box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
</style>


  <!-- Page content starts here -->
  <div class="container">