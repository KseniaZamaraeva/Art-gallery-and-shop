<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Доступ заборонено!");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT image_url FROM paintings WHERE id = ?");
    $stmt->execute([$id]);
    $painting = $stmt->fetch();

    if ($painting) {
        if (file_exists('../' . $painting['image_url'])) {
            unlink('../' . $painting['image_url']);
        }
        $stmt = $pdo->prepare("DELETE FROM paintings WHERE id = ?");
        $stmt->execute([$id]);
    }
}
header('Location: ../admin.php');
exit;
?>