<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
date_default_timezone_set('Asia/Jakarta');

require_once 'session.php';
include 'koneksi.php';

header('Content-Type: application/json');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$query = "SELECT * FROM user_evaluasi WHERE username = $1";
$result = pg_query_params($conn, $query, [$username]);

if ($result && $row = pg_fetch_assoc($result)) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['admin'] = $row['username'];
        $_SESSION['LAST_ACTIVITY'] = time();

        echo json_encode([
            'status' => 'success',
            'username' => $row['username']
        ]);
        exit;
    }
}

echo json_encode([
    'status' => 'error',
    'message' => 'Username atau password salah'
]);
