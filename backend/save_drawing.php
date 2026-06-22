<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Не авторизовано!']);
    exit;
}

$base64Image = $_POST['image'] ?? '';
if (strpos($base64Image, 'data:image/png;base64,') === 0) {
    $imageData = str_replace('data:image/png;base64,', '', $base64Image);
    $decodedData = base64_decode($imageData);
    $fileName = 'custom_' . md5(time() . $_SESSION['user_id']) . '.png';
    
    if (file_put_contents('../uploads/' . $fileName, $decodedData)) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, 1500.00, 'Нове')");
        $stmt->execute([$_SESSION['user_id']]);
        echo json_encode(['status' => 'success', 'order_id' => $pdo->lastInsertId()]);
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'Помилка збереження']);
?>