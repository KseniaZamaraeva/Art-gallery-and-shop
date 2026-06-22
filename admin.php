<?php
session_start();
require_once 'config/db.php';
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { header('Location: login.php'); exit; }
$paintings = $pdo->query("SELECT * FROM paintings ORDER BY id DESC")->fetchAll();
$orders = $pdo->query("SELECT o.*, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмінка</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="sidebar-brand">Адмін Панель</div>
            <ul class="sidebar-menu">
                <li><a onclick="tab('p')" class="active" id="m-p">🖼️ Картини</a></li>
                <li><a onclick="tab('o')" id="m-o">📦 Замовлення</a></li>
            </ul>
        </div>
        
        <ul class="sidebar-menu" style="margin-bottom: 20px; border-top: 1px solid #34495e;">
            <li>
                <a href="backend/logout.php" style="color: #ff4757; font-weight: bold; transition: background 0.3s;">
                    🚪 Вихід з панелі
                </a>
            </li>
        </ul>
    </aside>
    <main class="admin-content">
        <section id="s-p" class="admin-section active">
            <div class="admin-form">
                <form action="backend/add_painting.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group"><label>Назва</label><input type="text" name="title" required></div>
                    <div class="form-group"><label>Опис</label><input type="text" name="description"></div>
                    <div class="form-group"><label>Ціна</label><input type="number" name="price" required></div>
                    <div class="form-group"><label>Фото</label><input type="file" name="image" required></div>
                    <button class="btn">Додати</button>
                </form>
            </div>
            <table class="admin-table">
                <?php foreach($paintings as $pt): ?>
                    <tr>
                        <td><img src="<?= $pt['image_url'] ?>" width="50"></td>
                        <td><?= htmlspecialchars($pt['title']) ?></td>
                        <td><a href="backend/delete_painting.php?id=<?= $pt['id'] ?>" class="btn" style="background:#e74c3c">Видалити</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
        <section id="s-o" class="admin-section">
            <table class="admin-table">
                <?php foreach($orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['email']) ?></td>
                        <td><?= $o['total_price'] ?> ₴</td>
                        <td>
                            <form action="backend/update_order.php" method="POST">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <select name="status" onchange="this.form.submit()" class="select-status">
                                    <option <?= $o['status']=='Нове'?'selected':'' ?>>Нове</option>
                                    <option <?= $o['status']=='В обробці'?'selected':'' ?>>В обробці</option>
                                    <option <?= $o['status']=='Виконано'?'selected':'' ?>>Виконано</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </main>
</div>
<script>
function tab(t) {
    document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
    if(t==='p') { document.getElementById('s-p').classList.add('active'); document.getElementById('m-p').classList.add('active'); }
    else { document.getElementById('s-o').classList.add('active'); document.getElementById('m-o').classList.add('active'); }
}
</script>
<footer style="margin-top: 50px; padding: 20px 0; text-align: center; border-top: 1px solid #e1e8ed; color: #7f8c8d; font-size: 0.95rem; width: 100%;">
    <p>&copy; 2026 Ксенія Замараєва. Всі права захищено.</p>
</footer>
</body>
</html>