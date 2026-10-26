
<?php
require_once '../../config/database.php';
require_once '../../helpers/auth.php';
require_once '../../helpers/escape.php';

check_role(['admin']);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $category_id = $_POST['category_id'] ?? '';

    if (
        $name === '' ||
        $sku === '' ||
        !is_numeric($price) ||
        $price < 0 ||
        filter_var($stock, FILTER_VALIDATE_INT) === false ||
        $stock < 0
    ) {
        $error = 'Periksa kembali nama, SKU, harga, dan stok.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE products
                 SET name = ?, sku = ?, price = ?, stock = ?, category_id = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $name,
                $sku,
                $price,
                $stock,
                $category_id !== '' ? $category_id : null,
                $id
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal menyimpan. Pastikan SKU tidak digunakan produk lain.';
        }
    }
}

$categories = $pdo->query(
    "SELECT id, name FROM categories ORDER BY name"
)->fetchAll(PDO::FETCH_ASSOC);

include '../../views/header.php';
?>

<style>
.edit-wrapper {
    max-width: 760px;
    margin: 30px auto;
    padding: 0 18px;
}
.edit-card {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,.07);
}
.edit-heading {
    color: #1e3a5f;
    font-size: 27px;
    font-weight: 700;
}
.edit-description {
    color: #718096;
    margin-bottom: 25px;
}
.edit-form label {
    display: block;
    font-weight: 600;
    margin: 16px 0 7px;
    color: #334155;
}
.edit-form input,
.edit-form select {
    width: 100%;
    box-sizing: border-box;
    padding: 12px 14px;
    border: 1px solid #dbe3ee;
    border-radius: 9px;
    font-size: 15px;
    background: #f8fafc;
}
.edit-form input:focus,
.edit-form select:focus {
    outline: none;
    border-color: #3978c5;
    background: white;
}
.edit-actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}
.btn-save, .btn-cancel {
    padding: 11px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
}
.btn-save {
    border: none;
    background: #245da8;
    color: white;
}
.btn-save:hover {
    background: #194780;
}
.btn-cancel {
    background: #eef2f7;
    color: #334155;
}
.edit-error {
    padding: 12px;
    border-radius: 8px;
    background: #fff0f0;
    color: #b42318;
    margin-bottom: 15px;
}
</style>

<div class="edit-wrapper">
    <div class="edit-card">
        <h2 class="edit-heading">Edit Produk</h2>
        <p class="edit-description">
            Perbarui informasi produk inventory kamu.
        </p>

        <?php if ($error !== ''): ?>
            <div class="edit-error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="edit-form">
            <label>Nama Produk</label>
            <input type="text" name="name"
                value="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required>

            <label>SKU / Kode Produk</label>
            <input type="text" name="sku"
                value="<?= htmlspecialchars($product['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required>

            <label>Harga Produk (Rp)</label>
            <input type="number" name="price" min="0" step="0.01"
                value="<?= htmlspecialchars((string)$product['price'], ENT_QUOTES, 'UTF-8') ?>"
                required>

            <label>Jumlah Stok</label>
            <input type="number" name="stock" min="0"
                value="<?= htmlspecialchars((string)$product['stock'], ENT_QUOTES, 'UTF-8') ?>"
                required>

            <label>Kategori Produk</label>
            <select name="category_id">
                <option value="">Tanpa kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>"
                        <?= (string)$category['id'] === (string)($product['category_id'] ?? '') ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="edit-actions">
                <button type="submit" class="btn-save">
                    Simpan Perubahan
                </button>
                <a href="index.php" class="btn-cancel">Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php include '../../views/footer.php'; ?>
