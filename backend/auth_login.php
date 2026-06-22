<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: ../admin.php');
            } else {
                header('Location: ../catalog.php');
            }
            exit;
        } else {
            echo "<script>alert('Невірні дані!'); window.location.href = '../login.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        die("Помилка: " . $e->getMessage());
    }
}
?>