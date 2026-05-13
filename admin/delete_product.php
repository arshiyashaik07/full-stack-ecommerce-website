<?php
session_start();
include '../includes/db.php';

// Optional: check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: manage_products.php");
    exit();
} else {
    header("Location: manage_products.php");
    exit();
}
?>