<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/mini_project3/public/dashboard.php">InventoryApp</a>
        <div class="collapse navbar-collapse">
<ul class="navbar-nav me-auto">
    <li class="nav-item"><a class="nav-link" href="/mini_project3/public/dashboard.php">Dashboard</a></li>
    <li class="nav-item"><a class="nav-link" href="/mini_project3/public/products/index.php">Produk</a></li>
    <li class="nav-item"><a class="nav-link" href="/mini_project3/public/products/categories.php">Kategori</a></li>
    <li class="nav-item"><a class="nav-link" href="/mini_project3/public/stock/in.php">Barang Masuk</a></li>
    <li class="nav-item"><a class="nav-link" href="/mini_project3/public/stock/out.php">Barang Keluar</a></li>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <li class="nav-item"><a class="nav-link text-warning" href="/mini_project3/public/users.php">Manajemen User</a></li>
    <?php endif; ?>
</ul>
<a href="/mini_project3/public/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>
<?php endif; ?>
<div class="container">