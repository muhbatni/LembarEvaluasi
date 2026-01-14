<?php
require_once 'session.php';
include 'koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];
$action = $_GET['action'] ?? '';

if ($action === 'verify') {
    $query = "UPDATE evaluasi SET is_verified = TRUE WHERE id = $id";
} elseif ($action === 'unverify') {
    $query = "UPDATE evaluasi SET is_verified = FALSE WHERE id = $id";
} else {
    header('Location: index.php');
    exit;
}

pg_query($conn, $query);
header('Location: index.php');
exit;
