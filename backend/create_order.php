<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Увійдіть в акаунт!'); window.location.href = '../login.php';</script>";
    exit;
}

$paintingId = intval($_GET['painting_id'] ?? 0);
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT price FROM paintings WHERE id = ?");
$stmt->execute([$paintingId]);
$painting = $stmt->fetch();

if ($painting) {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Нове')");
    $stmt->execute([$userId, $painting['price']]);
    echo "<script>alert('Замовлення створено!'); window.location.href = '../catalog.php';</script>";
}
?>