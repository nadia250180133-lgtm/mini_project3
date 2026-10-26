
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

check_auth();
check_role(['admin']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'staff';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } elseif (!in_array($role, ['admin', 'staff'], true)) {
        $error = 'Role tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, password, role)
                 VALUES (?, ?, ?)'
            );

            $stmt->execute([
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $role
            ]);

            $success = 'User berhasil ditambahkan.';
        } catch (PDOException $e) {
            $error = 'Gagal menambahkan user. Username mungkin sudah digunakan.';
        }
    }
}

$users = $pdo->query(
    'SELECT id, username, role FROM users ORDER BY id DESC'
)->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/header.php';
?>

<style>
.user-page {
    max-width: 1100px;
    margin: 10px auto 35px;
}

.user-banner {
    background: linear-gradient(120deg, #17365d, #2563a6);
    color: white;
    padding: 28px;
    border-radius: 16px;
    margin-bottom: 24px;
}

.user-banner h2 {
    font-weight: 700;
    margin-bottom: 8px;
}

.user-banner p {
    color: #e5efff;
    margin: 0;
}

.user-card {
    background: #fff;
    border: 1px solid #dce6f2;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(23, 54, 93, .06);
    margin-bottom: 24px;
    overflow: hidden;
}

.user-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5eaf2;
    background: #f8fbff;
}

.user-card-header h5 {
    color: #17365d;
    font-weight: 700;
    margin: 0;
}

.user-card-body {
    padding: 24px;
}

.user-card label {
    color: #243b53;
    font-weight: 600;
    margin-bottom: 8px;
}

.user-card .form-control,
.user-card .form-select {
    padding: 12px 14px;
    border-radius: 9px;
    border-color: #d5e0ee;
}

.user-card .form-control:focus,
.user-card .form-select:focus {
    border-color: #3978c5;
    box-shadow: 0 0 0 .2rem rgba(57, 120, 197, .12);
}

.btn-user {
    background: #245da8;
    color: white;
    border: none;
    border-radius: 9px;
    padding: 11px 20px;
    font-weight: 600;
}

.btn-user:hover {
    background: #194780;
    color: white;
}

.user-table {
    margin: 0;
}

.user-table thead th {
    background: #edf4fc;
    color: #17365d;
    padding: 14px 18px;
    white-space: nowrap;
    border-bottom: 1px solid #dce6f2;
}

.user-table tbody td {
    padding: 14px 18px;
    vertical-align: middle;
}

.user-table tbody tr:hover {
    background: #f5f9ff;
}

.role-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
}

.role-admin {
    background: #dbeafe;
    color: #1d4ed8;
}

.role-staff {
    background: #eaf2f8;
    color: #475569;
}
</style>

<div class="user-page">

    <div class="user-banner">
        <h2>Manajemen User</h2>
        <p>Kelola akun dan hak akses pengguna InventoryApp.</p>
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

    <div class="user-card">
        <div class="user-card-header">
            <h5>Tambah User Baru</h5>
        </div>

        <div class="user-card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username"
                        class="form-control" placeholder="Masukkan username"
                        required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password"
                        class="form-control" minlength="6"
                        placeholder="Minimal 6 karakter" required>
                </div>

                <div class="mb-4">
                    <label for="role" class="form-label">Role Pengguna</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-user">
                    + Tambah User
                </button>
            </form>
        </div>
    </div>

    <div class="user-card">
        <div class="user-card-header">
            <h5>Daftar Pengguna</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user['id']) ?></td>
                            <td class="fw-semibold">
                                <?= e($user['username']) ?>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="role-badge role-admin">Admin</span>
                                <?php else: ?>
                                    <span class="role-badge role-staff">Staff</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-secondary py-4">
                                Belum ada user terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../views/footer.php'; ?>
