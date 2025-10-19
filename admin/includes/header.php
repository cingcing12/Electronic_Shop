<?php
// admin_header.php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!is_admin_logged_in()) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Electronic_Shop Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { font-family:'Poppins', sans-serif; margin:0; background:#f5f6fa; }

/* Sidebar */
.sidebar {
    width: 220px;
    position: fixed;
    height: 100%;
    background: #1e1e2f;
    color: #fff;
    transition: transform 0.3s ease;
    z-index:1030;
}
.sidebar .nav-link { color:#fff; margin-bottom:5px; transition:0.3s; }
.sidebar .nav-link.active, .sidebar .nav-link:hover { background:#007bff; border-radius:6px; }

/* Content */
.content { margin-left:220px; padding:30px; transition: margin-left 0.3s ease; }

/* Sidebar Toggle Button */
#sidebarToggleBtn {
    display:none;
    position:fixed;
    top:15px;
    right:15px;
    z-index:1041;
    background:#007bff;
    color:#fff;
    border:none;
    padding:10px 12px;
    border-radius:6px;
}

/* Overlay */
#sidebarOverlay {
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:1029;
    transition: opacity 0.3s ease;
}

/* Responsive: Hide sidebar on small devices */
@media(max-width:991px){
    .sidebar { transform: translateX(-100%); }
    .sidebar.show { transform: translateX(0); }
    .content { margin-left:0; }
    #sidebarToggleBtn { display:block; }
    #sidebarOverlay.show { display:block; }
}
</style>
</head>
<body>

<!-- Sidebar Toggle Button -->
<button id="sidebarToggleBtn"><i class="bi bi-list"></i></button>

<!-- Overlay -->
<div id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column p-3 px-2" id="sidebar">
    <h4 class="text-center text-white mb-4 fw-bold">Electronic_Shop</h4>
    <ul class="nav flex-column mb-auto">
        <li><a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li><a href="users.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='users.php'?'active':'' ?>"><i class="bi bi-people me-2"></i> Users</a></li>
        <li><a href="products.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='products.php'?'active':'' ?>"><i class="bi bi-box-seam me-2"></i> Products</a></li>
        <li><a href="orders.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='orders.php'?'active':'' ?>"><i class="bi bi-receipt me-2"></i> Orders</a></li>
        <li><a href="edit_product.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='edit_product.php'?'active':'' ?>"><i class="bi bi-pencil-square me-2"></i> Add Product</a></li>
        <li class="mt-4"><a href="../logout.php" class="btn btn-danger w-100"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
   
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Elements
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebarToggleBtn');
const overlay = document.getElementById('sidebarOverlay');

// Toggle sidebar
toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
});

// Hide sidebar when clicking overlay
overlay.addEventListener('click', () => {
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
});
</script>