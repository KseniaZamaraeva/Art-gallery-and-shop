<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Про автора — Ксенія Замараєва</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Сучасні стилі для сторінки портфоліо автора */
        .hero-section {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 16px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .hero-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: #2c3e50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            border: 4px solid #fff;
        }
        .hero-info h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .hero-info h3 {
            color: #ff4757;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .section-title {
            text-align: center;
            margin: 40px 0 20px;
            color: #2c3e50;
            font-size: 1.8rem;
            position: relative;
        }
        /* Інтерактивні картки технологій */
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .skill-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #e1e8ed;
        }
        .skill-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
            border-color: #ff4757;
        }
        .skill-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        /* Блок інтерактивної статистики */
        .stats-container {
            display: flex;
            justify-content: space-around;
            background: #2c3e50;
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 40px;
            text-align: center;
        }
        .stat-item h2 {
            font-size: 2.2rem;
            color: #2ecc71;
            margin-bottom: 5px;
        }
        /* Віджет статусу розробника */
        .status-widget {
            background: #fff;
            border: 2px dashed #ff4757;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin: 30px auto;
            max-width: 600px;
        }
        .status-btn {
            background-color: #ff4757;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
            margin-top: 15px;
        }
        .status-btn:hover {
            background-color: #ff6b81;
        }
    </style>
</head>
<body>

<header>
    <h2>ArtGallery Portfolio</h2>
    <nav>
        <a href="index.php" class="active">Про автора</a>
        <a href="catalog.php">Каталог</a>
        <a href="editor.php">Гра-Розмальовка</a>
        <?php if (isset($_SESSION['user_name'])): ?>
            <span style="color: #ff4757; font-weight: bold; margin-left: 20px;">Привіт, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
            <a href="backend/logout.php">Вихід</a>
        <?php else: ?>
            <a href="login.php">Вхід / Реєстрація</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    
    <section class="hero-section">
        <div class="hero-avatar">👩‍💻</div>
        <div class="hero-info">
            <h1>Ксенія Замараєва</h1>
            <h3>Full-Stack Web Developer & UI/UX Designer</h3>
            <p style="color: #57606f; line-height: 1.6;">
                Вітаю у моєму творчому просторі! Я займаюся проектуванням, розробкою та автоматизацією сучасних веб-орієнтованих інформаційних систем. Даний веб-ресурс поєднує в собі технологічну адмін-панель керування замовленнями, каталог побутової техніки, та інтерактивну ігрову зону «Happy Color» для взаємодії з клієнтами.
            </p>
        </div>
    </section>

    <h2 class="section-title">📊 Мій проєкт у цифрах</h2>
    <section class="stats-container">
        <div class="stat-item">
            <h2 id="stat-code">0</h2>
            <p>РяДКІВ КОДУ PHP/JS</p>
        </div>
        <div class="stat-item">
            <h2 id="stat-coffee">0</h2>
            <p>ЧАШОК КАВИ ☕</p>
        </div>
        <div class="stat-item">
            <h2 id="stat-levels">20</h2>
            <p>ІГРОВИХ РІВНІВ</p>
        </div>
    </section>

    <h2 class="section-title">🛠️ Технологічний стек проєкту</h2>
    <section class="skills-grid">
        <div class="skill-card">
            <div class="skill-icon">🐘</div>
            <h3>PHP 8.x</h3>
            <p style="font-size: 0.9rem; color: #7f8c8d; margin-top: 5px;">Бекенд, сесії, архітектура та логіка CRM.</p>
        </div>
        <div class="skill-card">
            <div class="skill-icon">🗄️</div>
            <h3>MySQL / БД</h3>
            <p style="font-size: 0.9rem; color: #7f8c8d; margin-top: 5px;">Реляційні бази даних, збереження замовлень.</p>
        </div>
        <div class="skill-card">
            <div class="skill-icon">🎨</div>
            <h3>UI/UX & Canvas</h3>
            <p style="font-size: 0.9rem; color: #7f8c8d; margin-top: 5px;">Алгоритми Flood Fill, маніпуляція пікселями.</p>
        </div>
        <div class="skill-card">
            <div class="skill-icon">⚡</div>
            <h3>JavaScript</h3>
            <p style="font-size: 0.9rem; color: #7f8c8d; margin-top: 5px;">Асинхронні запити Fetch API, динаміка.</p>
        </div>
    </section>

    <section class="status-widget">
        <h3>💡 Мій поточний розробницький статус:</h3>
        <p id="status-text" style="font-size: 1.1rem; font-style: italic; color: #2c3e50; margin-top: 10px; font-weight: bold;">
            "Код працює? Не чіпай!"
        </p>
        <button class="status-btn" onclick="generateStatus()">🎲 Змінити статус</button>
    </section>



<script>
// --- ІНТЕРАКТИВНИЙ ЕФЕКТ: АНІМАЦІЯ ЛІЧИЛЬНИКІВ СТАТИСТИКИ ---
function animateValue(id, start, end, duration) {
    let obj = document.getElementById(id);
    let range = end - start;
    let minTimer = 50;
    let stepTime = Math.abs(Math.floor(duration / range));
    stepTime = Math.max(stepTime, minTimer);
    let startTime = new Date().getTime();
    let endTime = startTime + duration;
    let timer;
    
    function run() {
        let now = new Date().getTime();
        let remaining = Math.max((endTime - now) / duration, 0);
        let value = Math.round(end - (remaining * range));
        obj.innerHTML = value + (id === 'stat-code' ? '+' : '');
        if (value == end) {
            clearInterval(timer);
        }
    }
    timer = setInterval(run, stepTime);
}

// Запускаємо лічильники при завантаженні сторінки
window.addEventListener('DOMContentLoaded', () => {
    animateValue("stat-code", 0, 1500, 2000); // Анімуємо до 1500 рядків коду
    animateValue("stat-coffee", 0, 42, 1500);  // Анімуємо до 42 чашок кави
});

// --- ІНТЕРАКТИВНИЙ ГЕНЕРАТОР СТАТУСІВ ПРОГРАМІСТА ---
const statuses = [
    "«Код працює? Не чіпай!» 💻",
    "«Пишу код, який зрозуміє навіть phpMyAdmin» 🗄️",
    "«Кава перетворюється на якісні SQL-запити» ☕",
    "«Мій улюблений алгоритм — Flood Fill, він робить світ яскравішим» 🎨",
    "«Успішно пройдено дебаг 14-ї сторінки перед захистом!» 🚀",
    "«Сесії працюють, адмінка захищена, можна й на риболовлю!» 🎣"
];

function generateStatus() {
    const textElement = document.getElementById('status-text');
    let newStatus;
    
    // Слідкуємо, щоб статус не повторювався два рази поспіль
    do {
        newStatus = statuses[Math.floor(Math.random() * statuses.length)];
    } while (textElement.innerText === newStatus);
    
    textElement.innerText = newStatus;
}
</script>
<footer style="margin-top: 50px; padding: 20px 0; text-align: center; border-top: 1px solid #e1e8ed; color: #7f8c8d; font-size: 0.95rem; width: 100%;">
    <p>&copy; 2026 Ксенія Замараєва. Всі права захищено.</p>
</footer>
</body>
</html>