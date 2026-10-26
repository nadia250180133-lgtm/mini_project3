```php
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';

check_role(['admin', 'staff']);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name !== '') {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO categories (name) VALUES (?)"
            );
            $stmt->execute([$name]);

            header('Location: categories.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal menambahkan kategori. Silakan coba nama lain.';
        }
    } else {
        $error = 'Nama kategori tidak boleh kosong.';
    }
}

$categories = $pdo->query(
    "SELECT * FROM categories ORDER BY id DESC"
)->fetchAll();

include __DIR__ . '/../../views/header.php';
?>

<style>
    .category-page {
        padding: 25px 0;
        color: #243247;
    }

    .category-banner {
        background: linear-gradient(135deg, #164e9b, #3985e8);
        color: white;
        padding: 25px 28px;
        border-radius: 18px;
        margin-bottom: 25px;
    }

    .category-banner h2 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .category-banner p {
        margin: 0;
        color: #e5efff;
    }

    .category-card {
        background: white;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 14px rgba(30, 50, 80, .05);
    }

    .category-card h5 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .category-page .form-control {
        min-height: 44px;
        border: 1px solid #dce4ef;
        border-radius: 9px;
    }

    .category-page .form-control:focus {
        border-color: #3985e8;
        box-shadow: 0 0 0 3px rgba(57, 133, 232, .12);
    }

    .btn-add-category {
        min-height: 44px;
        background: #2463b5;
        color: white;
        border: none;
        border-radius: 9px;
        padding: 10px 20px;
        font-weight: 600;
    }

    .btn-add-category:hover {
        background: #164e9b;
        color: white;
    }

    .category-table thead th {
        background: #f1f5fb;
        color: #44536a;
        border: none;
        padding: 14px;
    }

    .category-table tbody td {
        padding: 14px;
        vertical-align: middle;
    }

    .category-id {
        display: inline-block;
        background: #e7f0ff;
        color: #2459a6;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
    }

    .category-total {
        color: #718096;
        font-size: 13px;
    }

    @media (max-width: 576px) {
        .category-banner,
        .category-card {
            padding: 18px;
        }
    }
</style>

<div class="category-page">

    <div class="category-banner">
        <h2>🗂️ Manajemen Kategori</h2>
        <p>Kelola dan kelompokkan produk agar inventaris lebih teratur.</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- Form tambah kategori -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div class="category-card">
        <h5>＋ Tambah Kategori</h5>
        <p class="text-secondary small">
            Masukkan nama kategori baru pada kolom berikut.
        </p>

        <form method="POST" class="row g-3 align-items-end">
            <div class="col-12 col-md-8">
                <label for="name" class="form-label">Nama Kategori</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="Contoh: Elektronik"
                    required
                >
            </div>

            <div class="col-12 col-md-4">
                <button
                    type="submit"
                    class="btn-add-category w-100"
                >
                    + Tambah Kategori
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Daftar kategori -->
    <div class="category-card">
        <h5>📋 Daftar Kategori</h5>

        <p class="category-total mb-3">
            Total <?= count($categories) ?> kategori terdaftar.
        </p>

        <div class="table-responsive">
            <table class="table table-hover category-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>Nama Kategori</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <span class="category-id">
                                        <?= e($cat['id']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= e($cat['name']) ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center text-secondary py-4">
                                Belum ada kategori. Silakan tambah kategori baru.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>
```