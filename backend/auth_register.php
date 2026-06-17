<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        die("Будь ласка, заповніть усі поля!");
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            die("Користувач з таким Email вже існує!");
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')");
        $stmt->execute([$name, $email, $passwordHash]);

        echo "<script>alert('Реєстрація успішна!'); window.location.href = '../login.php';</script>";
        exit;
    } catch (PDOException $e) {
        die("Помилка: " . $e->getMessage());
    }
}
?>