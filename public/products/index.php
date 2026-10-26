```php
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';
check_auth();

$limit = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$category_filter = $_GET['category_id'] ?? '';

$allowed_sort_cols = ['name', 'sku', 'price', 'stock'];
$sort = $_GET['sort'] ?? 'name';
$order = strtoupper($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

if (!in_array($sort, $allowed_sort_cols, true)) {
    $sort = 'name';
}

$query_params = [];
$where_clauses = ["1=1"];

if ($search !== '') {
    $where_clauses[] = "(p.name LIKE ? OR p.sku LIKE ?)";
    $query_params[] = "%$search%";
    $query_params[] = "%$search%";
}

if ($category_filter !== '') {
    $where_clauses[] = "p.category_id = ?";
    $query_params[] = $category_filter;
}

$where_sql = implode(" AND ", $where_clauses);

$count_stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM products p WHERE $where_sql"
);
$count_stmt->execute($query_params);
$total_records = $count_stmt->fetchColumn();
$total_pages = (int)ceil($total_records / $limit);

$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE $where_sql
        ORDER BY p.$sort $order
        LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($query_params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

include __DIR__ . '/../../views/header.php';
?>

<style>
    .product-page {
        padding: 25px 0;
        color: #243247;
    }

    .page-banner {
        background: linear-gradient(135deg, #164e9b, #3985e8);
        color: white;
        padding: 25px 28px;
        border-radius: 18px;
        margin-bottom: 25px;
    }

    .page-banner h2 {
        font-weight: 700;
        margin-bottom: 7px;
    }

    .page-banner p {
        margin: 0;
        color: #e5efff;
    }

    .product-panel {
        background: white;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 22px;
        box-shadow: 0 4px 14px rgba(30, 50, 80, .05);
    }

    .product-panel h5 {
        font-weight: 700;
        margin-bottom: 18px;
    }

    .product-page .form-control {
        min-height: 42px;
        border-color: #dce4ef;
        border-radius: 9px;
    }

    .product-page .form-control:focus {
        border-color: #3985e8;
        box-shadow: 0 0 0 3px rgba(57, 133, 232, .12);
    }

    .btn-blue {
        background: #2463b5;
        border-color: #2463b5;
        color: white;
    }

    .btn-blue:hover {
        background: #164e9b;
        color: white;
    }

    .product-table {
        margin-bottom: 0;
    }

    .product-table thead th {
        background: #f1f5fb;
        color: #44536a;
        border-bottom: none;
        padding: 14px;
        font-size: 13px;
        white-space: nowrap;
    }

    .product-table tbody td {
        padding: 14px;
        vertical-align: middle;
    }

    .sku-badge {
        display: inline-block;
        background: #e7f0ff;
        color: #2459a6;
        padding: 5px 9px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-label {
        display: inline-block;
        background: #e2f8ec;
        color: #18794e;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .stock-low {
        background: #ffe8e8;
        color: #c53030;
    }

    .product-count {
        color: #718096;
        font-size: 13px;
    }

    .product-page .pagination {
        margin-bottom: 0;
    }

    .product-page .page-link {
        color: #2463b5;
        border-color: #e1e8f0;
        border-radius: 7px;
        margin-right: 5px;
    }

    .product-page .page-item.active .page-link {
        background: #2463b5;
        border-color: #2463b5;
        color: white;
    }

    @media (max-width: 576px) {
        .page-banner, .product-panel {
            padding: 18px;
        }
    }
</style>

<div class="product-page">

    <div class="page-banner">
        <h2>📦 Daftar Produk</h2>
        <p>Kelola data produk, harga, kategori, dan stok barang.</p>
    </div>

    <div class="product-panel">
        <h5>🔎 Cari dan Filter</h5>

        <form method="GET" class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari nama atau SKU..."
                    value="<?= e($search) ?>"
                >
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <select name="category_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option
                            value="<?= e($cat['id']) ?>"
                            <?= $category_filter == $cat['id'] ? 'selected' : '' ?>
                        >
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <select name="sort" class="form-control">
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nama</option>
                    <option value="sku" <?= $sort === 'sku' ? 'selected' : '' ?>>SKU</option>
                    <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>Harga</option>
                    <option value="stock" <?= $sort === 'stock' ? 'selected' : '' ?>>Stok</option>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <select name="order" class="form-control">
                    <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Naik (ASC)</option>
                    <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Turun (DESC)</option>
                </select>
            </div>

            <div class="col-12 col-lg-2">
                <button type="submit" class="btn btn-blue w-100">
                    Cari / Filter
                </button>
            </div>
        </form>
    </div>

    <div class="product-panel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="mb-1">Daftar Data Produk</h5>
                <span class="product-count">
                    Total <?= $total_records ?> produk ditemukan
                </span>
            </div>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="create.php" class="btn btn-blue">
                    + Tambah Produk
                </a>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-hover product-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <span class="sku-badge">
                                        <?= e($p['sku']) ?>
                                    </span>
                                </td>

                                <td><strong><?= e($p['name']) ?></strong></td>

                                <td><?= e($p['category_name']) ?></td>

                                <td>
                                    Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                </td>

                                <td>
                                    <span class="stock-label <?= $p['stock'] < 5 ? 'stock-low' : '' ?>">
                                        <?= e($p['stock']) ?> unit
                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    <a
                                        href="edit.php?id=<?= e($p['id']) ?>"
                                        class="btn btn-sm btn-warning"
                                    >Edit</a>

                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a
                                            href="delete.php?id=<?= e($p['id']) ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                        >Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">
                                Tidak ada produk ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination flex-wrap">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                            <a
                                class="page-link"
                                href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category_id=<?= urlencode($category_filter) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?>"
                            ><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>
```