<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
check_role(['admin']);

$id = $_GET['id'] ?? null;
if ($id) {
    // Periksa apakah produk memiliki histori transaksi
    $check_tx = $pdo->prepare("SELECT COUNT(*) FROM stock_transactions WHERE product_id = ?");
    $check_tx->execute([$id]);
    $has_history = $check_tx->fetchColumn() > 0;

    if ($has_history) {
        include __DIR__ . '/../../views/header.php';
        echo "<div class='container mt-5'><div class='alert alert-danger'><h3>Penolakan Penghapusan</h3><p>Produk ini memiliki histori transaksi stok dan tidak boleh dihapus sembarangan!</p><a href='index.php' class='btn btn-secondary'>Kembali</a></div></div>";
        include __DIR__ . '/../../views/footer.php';
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: index.php');
exit;