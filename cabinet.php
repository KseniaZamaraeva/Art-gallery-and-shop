<?php
session_start();

// 1. Перевірка авторизації: якщо користувач не увійшов, відправляємо на вхід
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 2. Підключення до бази (залиш свій шлях до db.php)
require_once 'config/db.php'; 

$current_user_id = $_SESSION['user_id'];

// 3. Запит замовлень поточного користувача
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$current_user_id]);
$my_orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Мій Кабінет — ArtGallery</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Стилі для кабінету користувача */
        .cabinet-wrapper {
            background: var(--surface-color, #ffffff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: var(--radius-lg, 24px);
            box-shadow: var(--shadow-md, 0 10px 25px -5px rgba(0, 0, 0, 0.05));
            overflow: hidden;
            margin-top: 30px;
        }
        .order-row {
            display: grid;
            grid-template-columns: 80px 1fr 120px 140px;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color, #e2e8f0);
            gap: 20px;
        }
        .order-row:last-child {
            border-bottom: none;
        }
        .order-row:hover {
            background-color: #f8fafc;
        }
        .order-id {
            font-weight: 700;
            color: var(--text-main, #0f172a);
        }
        .order-preview img {
            max-width: 70px;
            border-radius: var(--radius-md, 12px);
            border: 1px solid var(--border-color, #e2e8f0);
            object-fit: cover;
            display: block;
        }
        .order-price {
            font-weight: 600;
            color: var(--text-main, #0f172a);
            font-size: 1.05rem;
        }
        
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
        }
        .status-new { background-color: #fef3c7; color: #d97706; }
        .status-process { background-color: #e0e7ff; color: #4f46e5; }
        .status-done { background-color: #dcfce7; color: #15803d; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted, #64748b);
        }

        /* ДЕМО ОПЛАТА: Блок з кнопкою */
        .checkout-actions {
            display: flex;
            justify-content: flex-end;
            padding: 24px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color, #e2e8f0);
        }

        .gpay-button-ui {
            background-color: #000000;
            color: #ffffff;
            min-width: 240px;
            height: 48px;
            border: none;
            border-radius: 24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.2s, transform 0.1s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .gpay-button-ui:hover {
            background-color: #202124;
            transform: translateY(-1px);
        }

        /* ДЕМО ОПЛАТА: Модальне вікно */
        .payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .payment-modal.active {
            display: flex;
        }

        .payment-card {
            background: #ffffff;
            border-radius: 28px;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .success-icon {
            width: 72px;
            height: 72px;
            background: #e6f4ea;
            color: #137333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px auto;
        }
    </style>
</head>
<body>

<header>
    <h2>ArtGallery Pro</h2>
    <nav>
        <a href="index.php">Про автора</a>
        <a href="catalog.php">Каталог</a>
        <a href="editor.php">Розмальовка</a>
        <a href="cabinet.php" class="active">🛒 Кошик</a>
        <a href="backend/logout.php" style="color: var(--accent, #ff4757);">Вихід</a>
    </nav>
</header>

<div class="container">
    <div style="margin-bottom: 25px;">
        <h1>🛒 Мої замовлення та роботи</h1>
        <p style="color: var(--text-muted, #64748b);">Вітаємо! Тут ви можете відстежувати статус вашої творчості.</p>
    </div>

    <div class="cabinet-wrapper">
        <?php if (!empty($my_orders)): ?>
            <?php foreach($my_orders as $order): ?>
                <?php 
                    $status_class = 'status-new';
                    if ($order['status'] === 'В обробці') $status_class = 'status-process';
                    if ($order['status'] === 'Виконано') $status_class = 'status-done';
                ?>
                <div class="order-row">
                    <div class="order-id">#<?= $order['id'] ?></div>
                    <div class="order-preview">
                        <?php if (!empty($order['image_path'])): ?>
                            <img src="<?= htmlspecialchars($order['image_path']) ?>" alt="Робота">
                        <?php else: ?>
                            <span style="color: var(--text-muted, #64748b); font-size: 0.9rem; font-style: italic;">Товар із каталогу</span>
                        <?php endif; ?>
                    </div>
                    <div class="order-price"><?= $order['total_price'] ?> ₴</div>
                    <div>
                        <span class="badge <?= $status_class ?>">
                            <?= htmlspecialchars($order['status'] ?? 'Нове') ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="checkout-actions">
                <button type="button" class="gpay-button-ui" onclick="openGPayModal()">
                    <svg width="41" height="17" viewBox="0 0 41 17" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top: 2px;">
                        <path d="M18.3 1.6H14.1V14.8H18.3C20.2 14.8 21.8 14.2 23 13C24.2 11.8 24.8 10.2 24.8 8.2C24.8 6.2 24.2 4.6 23 3.4C21.8 2.2 20.2 1.6 18.3 1.6ZM18.3 12.2H16.4V4.2H18.3C19.4 4.2 20.3 4.5 21 5.2C21.7 5.9 22.1 6.9 22.1 8.2C22.1 9.5 21.7 10.5 21 11.2C20.3 11.9 19.4 12.2 18.3 12.2Z" fill="currentColor"/>
                        <path d="M30.4 6.1C28.9 6.1 27.7 6.6 26.9 7.6V6.4H24.6V17.3H26.9V13.1C27.7 14.1 28.9 14.6 30.4 14.6C31.7 14.6 32.8 14.1 33.7 13C34.6 11.9 35.1 10.3 35.1 8.3C35.1 6.3 34.6 4.8 33.7 3.7C32.8 2.6 31.7 6.1 30.4 6.1ZM30.1 12.4C29.2 12.4 28.5 12.1 27.9 11.4C27.3 10.7 27 9.7 27 8.3C27 7 27.3 6 27.9 5.3C28.5 4.6 29.2 4.3 30.1 4.3C31 4.3 31.7 4.6 32.3 5.3C32.9 6 33.2 7.1 33.2 8.3C33.2 9.6 32.9 10.7 32.3 11.4C31.7 12.1 31 12.4 30.1 12.4Z" fill="currentColor"/>
                        <path d="M4.3 8.4V4.9H8.2V2.7H4.3V0H2.1V13.2C2.1 15.3 3.3 16.4 5.7 16.4C6.6 16.4 7.4 16.2 8.1 15.9V13.8C7.6 14 7.1 14.1 6.5 14.1C5 14.1 4.3 13.1 4.3 11.2V8.4Z" fill="currentColor"/>
                        <path d="M10.1 14.8H12.3V1.6H10.1V14.8Z" fill="currentColor"/>
                        <path d="M39.6 6.4H37.3L36.1 10.4L34.9 6.4H32.6L34.9 13.1L32.6 17.5H34.9L40 6.4H39.6Z" fill="currentColor"/>
                    </svg>
                    Швидка оплата з GPay
                </button>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 3rem; margin-bottom: 10px;">🎨</div>
                <h3>Ваш кошик порожній</h3>
                <p>Збережіть розфарбовану роботу, щоб надіслати замовлення.</p>
                <a href="editor.php" class="btn" style="background: var(--primary, #6366f1); color: white; margin-top: 20px; text-decoration: none; display: inline-block; padding: 10px 20px; border-radius: 12px;">Перейти до розмальовки</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="payment-modal" id="demo-gpay-modal">
    <div class="payment-card">
        <div class="success-icon">✓</div>
        <h3 style="font-size: 1.4rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Симуляція оплати</h3>
        <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; margin-bottom: 24px;">Демо-платіж через <strong>Google Pay</strong> успішно імітовано! На реальному сервері тут відбувається шлюз транзакції.</p>
        <button type="button" class="btn" style="background: #0f172a; color: #ffffff; width: 100%; border-radius: 12px; padding: 12px; cursor: pointer; border: none; font-weight: 600;" onclick="closeGPayModal()">
            Зрозуміло
        </button>
    </div>
</div>

<script>
function openGPayModal() {
    document.getElementById('demo-gpay-modal').classList.add('active');
}

function closeGPayModal() {
    document.getElementById('demo-gpay-modal').classList.remove('active');
}
</script>



</body>
</html>