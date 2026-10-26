<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_auth() {
    if (!isset($_SESSION['user_id'])) {
    header('Location: /mini_project3/public/login.php');
        exit;
    }
}

function check_role($allowed_roles) {
    check_auth();
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // Akses Terlarang (Forbidden)
        http_response_code(403);
        include __DIR__ . '/../views/header.php';
        echo "<div class='container mt-5'><div class='alert alert-danger'><h3>Akses Terlarang (403)</h3><p>Anda tidak memiliki hak akses ke halaman ini.</p></div></div>";
        include __DIR__ . '/../views/footer.php';
        exit;
    }
}