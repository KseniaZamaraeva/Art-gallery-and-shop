<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Доступ заборонено!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $dest_path = '../uploads/' . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $db_image_url = 'uploads/' . $newFileName;
            $stmt = $pdo->prepare("INSERT INTO paintings (title, description, price, image_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $price, $db_image_url]);
            header('Location: ../admin.php');
            exit;
        }
    }
}
?>