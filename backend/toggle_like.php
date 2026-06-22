<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Авторизуйтесь!']);
    exit;
}

$userId = $_SESSION['user_id'];
$paintingId = intval($_POST['painting_id'] ?? 0);

$stmt = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND painting_id = ?");
$stmt->execute([$userId, $paintingId]);

if ($stmt->fetch()) {
    $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND painting_id = ?");
    $stmt->execute([$userId, $paintingId]);
    $action = 'unliked';
} else {
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, painting_id) VALUES (?, ?)");
    $stmt->execute([$userId, $paintingId]);
    $action = 'liked';
}

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM likes WHERE painting_id = ?");
$stmt->execute([$paintingId]);
echo json_encode(['status' => 'success', 'action' => $action, 'new_likes' => $stmt->fetch()['total']]);
?>