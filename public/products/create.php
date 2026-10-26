<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validation.php';
require_once __DIR__ . '/../../helpers/escape.php';
check_role(['admin']); // Staff tidak boleh menambah master produk

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku']);
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Validasi aturan bisnis
    $err_price = validate_positive_number($price, 'Harga');
    $err_stock = validate_positive_number($stock, 'Stok');

    if ($err_price || $err_stock) {
        $error = $err_price ?? $err_stock;
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (sku, name, category_id, price, stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$sku, $name, $category_id, $price, $stock]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            // Tangkap duplikasi SKU (Unique constraint violation)
            if ($e->getCode() == 23000) {
                $error = "Gagal: SKU '$sku' sudah terdaftar (SKU harus unik).";
            } else {
                $error = "Terjadi kesalahan database: " . $e->getMessage();
            }
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
include __DIR__ . '/../../views/header.php';
?>
<h2>Tambah Produk</h2>
<hr>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<form method="POST" class="bg-white p-4 border rounded">
    <div class="mb-3">
        <label>SKU (Unik)</label>
        <input type="text" name="sku" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Kategori</label>
        <select name="category_id" class="form-control" required>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Harga (Tidak boleh negatif)</label>
        <input type="number" step="0.01" name="price" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Stok Awal</label>
        <input type="number" name="stock" class="form-control" value="0" required>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Produk</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
<?php include __DIR__ . '/../../views/footer.php'; ?>