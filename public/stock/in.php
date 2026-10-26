
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/escape.php';

check_auth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $reference_note = trim($_POST['reference_note'] ?? '');

    if (!$product_id || !$quantity || $quantity <= 0) {
        $error = 'Pilih produk dan masukkan jumlah lebih dari 0.';
    } else {
        try {
            $pdo->beginTransaction();

            $check = $pdo->prepare("SELECT id FROM products WHERE id = ?");
            $check->execute([$product_id]);

            if (!$check->fetch()) {
                throw new Exception('Produk tidak ditemukan.');
            }

            $stmt_tx = $pdo->prepare(
                "INSERT INTO stock_transactions
                (product_id, user_id, type, quantity, reference_note)
                VALUES (?, ?, 'IN', ?, ?)"
            );
            $stmt_tx->execute([
                $product_id,
                $_SESSION['user_id'],
                $quantity,
                $reference_note
            ]);

            $stmt_update = $pdo->prepare(
                "UPDATE products SET stock = stock + ? WHERE id = ?"
            );
            $stmt_update->execute([$quantity, $product_id]);

            $pdo->commit();
            $success = 'Barang masuk berhasil dicatat dan stok diperbarui.';
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Transaksi gagal. Silakan periksa data dan coba lagi.';
        }
    }
}

$products = $pdo->query(
    "SELECT id, sku, name, stock FROM products ORDER BY name ASC"
)->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../views/header.php';
?>

<style>

.stock-page {
    max-width: 950px;
    margin: 10px auto 35px;
}

.stock-banner {
    background: linear-gradient(120deg, #17365d, #2563a6);
    color: white;
    padding: 28px;
    border-radius: 16px;
    margin-bottom: 24px;
}

.stock-banner h2 {
    font-weight: 700;
    margin-bottom: 8px;
}

.stock-banner p {
    margin: 0;
    color: #e5efff;
}

.stock-card {
    background: #ffffff;
    border: 1px solid #dce6f2;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 5px 20px rgba(23, 54, 93, 0.07);
}

.stock-card label {
    font-weight: 600;
    color: #243b53;
    margin-bottom: 8px;
}

.stock-card .form-control,
.stock-card .form-select {
    padding: 12px 14px;
    border-radius: 9px;
    border: 1px solid #d5e0ee;
    background-color: #ffffff;
}

.stock-card .form-control:focus,
.stock-card .form-select:focus {
    border-color: #3978c5;
    box-shadow: 0 0 0 0.2rem rgba(57, 120, 197, 0.12);
}

.stock-submit {
    background: #245da8;
    border: none;
    color: white;
    padding: 12px 20px;
    border-radius: 9px;
    font-weight: 600;
}

.stock-submit:hover {
    background: #194780;
    color: white;
}

.stock-hint {
    color: #718096;
    font-size: 13px;
    margin-top: 7px;
}

</style>

<div class="stock-page">
    <div class="stock-banner">
        <h2>↓ Barang Masuk</h2>
        <p>Catat penambahan stok produk dengan mudah dan rapi.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger rounded-3">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success rounded-3">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <div class="stock-card">
        <h5 class="fw-bold mb-1">Formulir Barang Masuk</h5>
        <p class="text-secondary mb-4">
            Isi informasi di bawah ini sesuai transaksi.
        </p>

        <form method="POST">
            <div class="mb-4">
                <label for="product_id" class="form-label">Pilih Produk</label>
                <select name="product_id" id="product_id"
                    class="form-select" required>
                    <option value="">-- Pilih produk --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= (int)$p['id'] ?>">
                            <?= e($p['sku']) ?> - <?= e($p['name']) ?>
                            (Stok saat ini: <?= (int)$p['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="stock-hint">
                    Pilih produk yang akan ditambahkan stoknya.
                </div>
            </div>

            <div class="mb-4">
                <label for="quantity" class="form-label">Jumlah Barang Masuk</label>
                <input type="number" name="quantity" id="quantity"
                    class="form-control" min="1"
                    placeholder="Contoh: 10" required>
            </div>

            <div class="mb-4">
                <label for="reference_note" class="form-label">
                    Catatan / Supplier / Referensi
                </label>
                <input type="text" name="reference_note" id="reference_note"
                    class="form-control"
                    placeholder="Contoh: Pembelian dari supplier A">
            </div>

            <button type="submit" class="btn stock-submit">
                Simpan Barang Masuk
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../views/footer.php'; ?>
