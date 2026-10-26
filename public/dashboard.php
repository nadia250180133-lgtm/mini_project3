```php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';
check_auth();

// Statistik
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_suppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$low_stock_count = $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 5")->fetchColumn();

// Produk stok rendah
$stmt_low = $pdo->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.stock < 5
");
$low_stocks = $stmt_low->fetchAll();

include __DIR__ . '/../views/header.php';
?>

<style>
    .dashboard {
        padding: 25px 0;
        color: #243247;
    }

    .welcome-box {
        background: linear-gradient(135deg, #164e9b, #3985e8);
        color: white;
        padding: 30px;
        border-radius: 18px;
        margin-bottom: 25px;
        box-shadow: 0 8px 22px rgba(30, 90, 170, 0.15);
    }

    .welcome-box h2 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .welcome-box p {
        margin: 0;
        color: #e5efff;
    }

    .stat-card {
        background: white;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 22px;
        height: 100%;
        box-shadow: 0 4px 14px rgba(30, 50, 80, 0.05);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .stat-label {
        color: #718096;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .stat-number {
        font-size: 30px;
        font-weight: 700;
        color: #243247;
        margin: 0;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 22px;
        margin-bottom: 15px;
    }

    .icon-blue { background: #e7f0ff; }
    .icon-green { background: #e2f8ec; }
    .icon-cyan { background: #e0f7fa; }
    .icon-red { background: #ffe8e8; }

    .section-card {
        background: white;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 22px;
        margin-top: 28px;
        box-shadow: 0 4px 14px rgba(30, 50, 80, 0.04);
    }

    .section-title {
        font-size: 19px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .section-subtitle {
        color: #718096;
        font-size: 13px;
        margin-bottom: 20px;
    }

    .table thead th {
        background: #f1f5fb;
        color: #44536a;
        border-bottom: none;
        padding: 13px;
        font-size: 13px;
    }

    .table tbody td {
        padding: 14px 13px;
        vertical-align: middle;
    }

    .stock-badge {
        display: inline-block;
        background: #ffe8e8;
        color: #c53030;
        padding: 5px 11px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 12px;
    }

    .empty-message {
        color: #718096;
        padding: 25px !important;
    }

    @media (max-width: 576px) {
        .welcome-box { padding: 22px; }
        .stat-number { font-size: 25px; }
        .section-card { padding: 15px; }
    }
</style>

<div class="dashboard">

    <div class="welcome-box">
        <h2>Dashboard Inventory</h2>
        <p>Selamat datang, <?= e($_SESSION['username'] ?? 'Pengguna') ?>!
        Kelola dan pantau persediaan produkmu dengan mudah.</p>
    </div>

    <div class="row g-3">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon icon-blue">📦</div>
                <div class="stat-label">Total Produk</div>
                <p class="stat-number"><?= $total_products ?></p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon icon-green">🗂️</div>
                <div class="stat-label">Total Kategori</div>
                <p class="stat-number"><?= $total_categories ?></p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon icon-cyan">🏢</div>
                <div class="stat-label">Total Supplier</div>
                <p class="stat-number"><?= $total_suppliers ?></p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon icon-red">⚠️</div>
                <div class="stat-label">Stok Rendah (&lt; 5)</div>
                <p class="stat-number"><?= $low_stock_count ?></p>
            </div>
        </div>

    </div>

    <div class="section-card">
        <h4 class="section-title">⚠️ Peringatan Stok Rendah</h4>
        <p class="section-subtitle">
            Daftar produk yang memiliki stok kurang dari 5 unit.
        </p>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($low_stocks) > 0): ?>
                        <?php foreach ($low_stocks as $item): ?>
                            <tr>
                                <td><?= e($item['sku']) ?></td>
                                <td><strong><?= e($item['name']) ?></strong></td>
                                <td><?= e($item['category_name']) ?></td>
                                <td>
                                    <span class="stock-badge">
                                        <?= e($item['stock']) ?> unit
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center empty-message">
                                🎉 Tidak ada produk dengan stok rendah.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
```