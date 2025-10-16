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
    <a class="navbar-brand fw-bold" href="index.php">Electronic_Shop</a>


<div>
  <!-- Mobile Search Icon -->
<button class="btn btn-outline-light d-lg-none ms-2" type="button" data-bs-toggle="modal" data-bs-target="#mobileSearchModal">
  <i class="fas fa-search"></i>
</button>

    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
</div>



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

        <!-- Responsive Search Input -->
<div class="position-relative ms-auto me-3 d-lg-block d-none" style="max-width:350px; width:100%;">
  <input id="navbarSearch" type="text" class="form-control rounded-pill ps-4 pe-4" placeholder="Search products..." autocomplete="off">
  <div id="searchResults" class="position-absolute w-100 bg-white shadow rounded overflow-auto" style="max-height:300px; display:none; z-index:1050;"></div>
</div>
  </div>
</nav>

<!-- Mobile Search Modal -->
<div class="modal fade" id="mobileSearchModal" tabindex="-1" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="mobileSearchModalLabel">Search Products</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input id="mobileNavbarSearch" type="text" class="form-control rounded-pill mb-3" placeholder="Search products..." autocomplete="off">
        <div id="mobileSearchResults" class="overflow-auto" style="max-height:300px;"></div>
      </div>
    </div>
  </div>
</div>

<style>
#mobileSearchResults div {
  padding: 0.5rem;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;
  border-bottom: 1px solid #e0e0e0;
}
#mobileSearchResults div:hover {
  background: linear-gradient(90deg, #ff6f61, #ff9472);
  color: #fff;
}
#mobileSearchResults img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 5px;
  margin-right: 10px;
}
</style>


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

<style>
/* Search Input Styling */
#navbarSearch {
  padding: 0.5rem 1rem;
  border: 2px solid #ffebcd;
  transition: all 0.3s ease;
}

#navbarSearch:focus {
  outline: none;
  box-shadow: 0 0 12px rgba(255,111,97,0.5);
  border-color: #ff6f61;
}

/* Dropdown Results */
#searchResults div {
  padding: 0.5rem;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

#searchResults div:hover {
  background: linear-gradient(90deg, #ff6f61, #ff9472);
  color: #fff;
}

#searchResults img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 5px;
  margin-right: 10px;
}

/* Responsive adjustments */
@media(max-width:767px){
  #navbarSearch {
    max-width:100%;
    margin-bottom:10px;
  }
  #searchResults {
    max-height:200px;
  }
}
</style>



  <!-- Page content starts here -->
  <!-- <div class="container"> -->

  <script>
const searchInput = document.getElementById('navbarSearch');
const resultsBox = document.getElementById('searchResults');

let timeout = null;

searchInput.addEventListener('input', function() {
  clearTimeout(timeout);
  const query = this.value.trim();

  if(query.length < 2){
    resultsBox.style.display = 'none';
    return;
  }

  timeout = setTimeout(() => {
    fetch(`search_ajax.php?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        resultsBox.innerHTML = '';
        if(data.length > 0){
          data.forEach(product => {
            const div = document.createElement('div');
            div.classList.add('d-flex','align-items-center','p-2','border-bottom','hover-bg-light');
            div.style.cursor = 'pointer';
            div.innerHTML = `
              <img src="${product.image}" alt="${product.name}" style="width:50px;height:50px;object-fit:cover;border-radius:5px;margin-right:10px;">
              <span>${product.name}</span>
            `;
            div.addEventListener('click', () => {
              window.location.href = `product.php?id=${product.id}`;
            });
            resultsBox.appendChild(div);
          });
        } else {
          resultsBox.innerHTML = '<div class="p-2 text-muted">No products found</div>';
        }
        resultsBox.style.display = 'block';
      });
  }, 300);
});

// Hide results when clicking outside
document.addEventListener('click', (e)=>{
  if(!resultsBox.contains(e.target) && e.target !== searchInput){
    resultsBox.style.display = 'none';
  }
});
</script>


<script>
const mobileSearchInput = document.getElementById('mobileNavbarSearch');
const mobileResultsBox = document.getElementById('mobileSearchResults');

let mobileTimeout = null;

mobileSearchInput.addEventListener('input', function() {
  clearTimeout(mobileTimeout);
  const query = this.value.trim();

  if(query.length < 2){
    mobileResultsBox.innerHTML = '';
    return;
  }

  mobileTimeout = setTimeout(() => {
    fetch(`search_ajax.php?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        mobileResultsBox.innerHTML = '';
        if(data.length > 0){
          data.forEach(product => {
            const div = document.createElement('div');
            div.innerHTML = `
              <img src="${product.image}" alt="${product.name}">
              <span>${product.name}</span>
            `;
            div.addEventListener('click', () => {
              window.location.href = `product.php?id=${product.id}`;
            });
            mobileResultsBox.appendChild(div);
          });
        } else {
          mobileResultsBox.innerHTML = '<div class="text-muted p-2">No products found</div>';
        }
      });
  }, 300);
});
</script>
