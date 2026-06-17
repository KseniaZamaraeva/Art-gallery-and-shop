<?php
session_start();
require_once 'config/db.php';
$paintings = $pdo->query("SELECT p.*, COUNT(l.painting_id) as likes_count FROM paintings p LEFT JOIN likes l ON p.id = l.painting_id GROUP BY p.id ORDER BY p.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Каталог</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h2>ArtGallery</h2>
    <nav><a href="index.php">Про автора</a><a href="catalog.php">Каталог</a><a href="editor.php">Малювати</a>
    <?= isset($_SESSION['user_name']) ? '<span>'.$_SESSION['user_name'].'</span><a href="backend/logout.php">Вихід</a>' : '<a href="login.php">Вхід</a>' ?></nav>
</header>
<div class="container">
    <div class="gallery">
        <?php foreach ($paintings as $p): ?>
            <div class="paint-card">
                <img src="<?= $p['image_url'] ?>">
                <div class="paint-info">
                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <p><?= htmlspecialchars($p['description']) ?></p>
                    <div class="paint-meta">
                        <span><?= $p['price'] ?> ₴</span>
                        <div>
                            <button class="like-btn" onclick="like(<?= $p['id'] ?>, this)">❤️ <span><?= $p['likes_count'] ?></span></button>
                            <a href="backend/create_order.php?painting_id=<?= $p['id'] ?>" class="btn">Купити</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
function like(id, btn) {
    if(<?= isset($_SESSION['user_id']) ? 'false' : 'true' ?>) return alert('Увійдіть!');
    fetch('backend/toggle_like.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'painting_id='+id })
    .then(r => r.json()).then(d => { if(d.status==='success') { btn.querySelector('span').innerText = d.new_likes; btn.classList.toggle('liked', d.action==='liked'); } });
}
</script>
<footer style="margin-top: 50px; padding: 20px 0; text-align: center; border-top: 1px solid #e1e8ed; color: #7f8c8d; font-size: 0.95rem; width: 100%;">
    <p>&copy; 2026 Ксенія Замараєва. Всі права захищено.</p>
</footer>
</body>
</html>