<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Розмальовка Областями (Happy Color)</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .canvas-container {
            position: relative;
            width: 800px;
            height: 550px;
            margin: 0 auto;
        }
        canvas {
            border: 3px solid #2c3e50;
            background: #ffffff;
            cursor: pointer;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .game-palette {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
            padding: 15px;
            background: #f1f2f6;
            border-radius: 12px;
        }
        .color-number-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            text-shadow: 0px 1px 2px rgba(0,0,0,0.8);
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .color-number-btn:hover {
            transform: scale(1.15);
        }
        .color-number-btn.active {
            border-color: #2c3e50;
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .game-status {
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
            min-height: 35px;
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
        <?php if (isset($_SESSION['user_name'])): ?>
            <span style="color: #ff4757; font-weight: bold; margin-left: 20px;">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="backend/logout.php">Вихід</a>
        <?php else: ?>
            <a href="login.php">Вхід</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 5px;">🎨 Заливка областей за номерами</h1>
    <p style="text-align: center; color: #666; margin-bottom: 15px;">Клікніть всередині будь-якої білої області, і вона повністю зафарбується обраним кольором!</p>

    <div class="game-status" id="game-message">Оберіть колір з палітри та натисніть на область малюнка!</div>

    <div class="editor-container" style="flex-direction: column; align-items: center;">
        
        <div class="canvas-container">
            <canvas id="canvas" width="800" height="550"></canvas>
        </div>

        <div class="game-palette" id="game-palette" style="width: 800px;"></div>

        <div style="width: 800px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 10px;">
                <button class="btn" id="next-contour" style="background-color: #3498db;">🎲 Змінити малюнок</button>
                <button class="btn" id="clear" style="background-color: #7f8c8d;">🧼 Очистити все</button>
            </div>
            <button class="btn" id="save" style="background-color: #2ecc71; padding: 12px 35px; font-size: 1.1rem;">🚀 Надіслати художнику</button>
        </div>
    </div>
</div>

<script>
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
const clearBtn = document.getElementById('clear');
const saveBtn = document.getElementById('save');
const nextBtn = document.getElementById('next-contour');
const paletteContainer = document.getElementById('game-palette');
const gameMessage = document.getElementById('game-message');

let currentLevel = 0;
let selectedNumber = 1;

// Набір кольорів для розмальовування
const gameColors = {
    1: '#ff4757',  // Червоний
    2: '#2ed573',  // Зелений
    3: '#1e90ff',  // Синій
    4: '#ffa502',  // Жовтий
    5: '#9b59b6',  // Фіолетовий
    6: '#ff6b81',  // Рожевий
    7: '#48dbfb',  // Блакитний
    8: '#ff9f43',  // Помаранчевий
    9: '#1dd1a1',  // Бірюзовий
    10: '#34495e'  // Графітовий
};

// Колекція з 20 деталізованих шаблонів (Пейзажі, Архітектура, Антистрес)
const complexContours = [
    // --- МАЛЬОВНИЧІ ПЕЙЗАЖІ ---
    'https://cdn-icons-png.flaticon.com/512/2560/2560403.png', // 1. Гірський пейзаж з річкою та лісом
    'https://cdn-icons-png.flaticon.com/512/2995/2995475.png', // 2. Морський пейзаж з маяком та кораблем
    'https://cdn-icons-png.flaticon.com/512/9373/9373142.png', // 3. Тропічний острів, океан та пальми
    'https://cdn-icons-png.flaticon.com/512/4148/4148705.png', // 4. Сільський будиночок біля озера
    'https://cdn-icons-png.flaticon.com/512/2205/2205044.png', // 5. Казковий замок на тлі великого місяця
    'https://cdn-icons-png.flaticon.com/512/3069/3069276.png', // 6. Повітряна куля над горами та хмарами
    'https://cdn-icons-png.flaticon.com/512/10317/10317079.png', // 7. Захід сонця у пустелі з кактусами та дюнами

    // --- СКЛАДНА АРХІТЕКТУРА ТА МІСТА ---
    'https://cdn-icons-png.flaticon.com/512/8663/8663243.png', // 8. Середньовічний форт з вежами та вікнами
    'https://cdn-icons-png.flaticon.com/512/9908/9908332.png', // 9. Чарівний палац з гострими шпилями
    'https://cdn-icons-png.flaticon.com/512/1150/1150626.png', // 10. Східний пагода-храм у саду
    'https://cdn-icons-png.flaticon.com/512/2306/2306161.png', // 11. Вітряк на полі з квітами

    // --- ПРИРОДА ТА СКЛАДНА ФАУНА (АНТИСТРЕС) ---
    'https://cdn-icons-png.flaticon.com/512/3122/3122421.png', // 12. Складна кругова геометрична мандала
    'https://cdn-icons-png.flaticon.com/512/3024/3024527.png', // 13. Мудра сова з візерунчастим пір'ям
    'https://cdn-icons-png.flaticon.com/512/3660/3660144.png', // 14. Складний деталізований дракон
    'https://cdn-icons-png.flaticon.com/512/1904/1904425.png', // 15. Акваріум / Підводний світ з коралами та рибками
    'https://cdn-icons-png.flaticon.com/512/1040/1040407.png', // 16. Лісова галявина з грибами та равликом
    'https://cdn-icons-png.flaticon.com/512/3069/3069223.png', // 17. Метелик з мереживними візерунками на крилах

    // --- КОСМОС ТА СЮРРЕАЛІЗМ ---
    'https://cdn-icons-png.flaticon.com/512/2554/2554030.png', // 18. Космічна ракета серед планет і зірок
    'https://cdn-icons-png.flaticon.com/512/1044/1044391.png', // 19. Інопланетна тарілка (НЛО) над нічним лісом
    'https://cdn-icons-png.flaticon.com/512/4800/4800455.png'  // 20. Скриня зі скарбами на піщаному дні
];

function generatePalette() {
    paletteContainer.innerHTML = '';
    Object.keys(gameColors).forEach((num) => {
        const btn = document.createElement('div');
        btn.classList.add('color-number-btn');
        btn.style.backgroundColor = gameColors[num];
        btn.innerText = num;
        
        if(parseInt(num) === selectedNumber) btn.classList.add('active');
        
        btn.addEventListener('click', () => {
            document.querySelectorAll('.color-number-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedNumber = parseInt(num);
            showMessage(`Обрано колір №${selectedNumber}. Клініть на область для заливки!`, '#2c3e50');
        });
        paletteContainer.appendChild(btn);
    });
}

function showMessage(text, color) {
    gameMessage.innerText = text;
    gameMessage.style.color = color;
}

function loadLevel() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    const img = new Image();
    img.crossOrigin = "anonymous";
    img.src = complexContours[currentLevel];
    
    img.onload = function() {
        // Заливаємо фон чисто білим перед малюванням контуру
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
       const size = 530; // Збільшили розмір контуру для полегшення кліків по дрібних деталях
const x = (canvas.width - size) / 2;
const y = (canvas.height - size) / 2;
ctx.drawImage(img, x, y, size, size);
        
        showMessage(`Малюнок завантажено. Готово до заливки областями!`, '#2c3e50');
    };
}

// Подія кліку по Canvas
canvas.addEventListener('click', function(e) {
    const rect = canvas.getBoundingClientRect();
    const x = Math.floor(e.clientX - rect.left);
    const y = Math.floor(e.clientY - rect.top);

    // Отримуємо RGB колір пікселя, на який клікнули
    const pixel = ctx.getImageData(x, y, 1, 1).data;
    const r = pixel[0], g = pixel[1], b = pixel[2];

    // Якщо клікнули на чорну лінію контуру — ігноруємо
    if (r < 60 && g < 60 && b < 60) {
        showMessage("🛡️ Це лінія контуру! Клікніть всередині білої області.", "#7f8c8d");
        return;
    }

    // Запускаємо ідеальну заливку області
    const targetColor = hexToRgb(gameColors[selectedNumber]);
    
    // Перевірка: якщо область вже зафарбована цим кольором — нічого не робимо
    if (r === targetColor.r && g === targetColor.g && b === targetColor.b) return;

    floodFill(x, y, targetColor);
    showMessage(`✨ Область зафарбована кольором №${selectedNumber}!`, '#2ecc71');
});

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return { r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16) };
}

// --- КЛАСИЧНИЙ АЛГОРИТМ FLOOD FILL (ЗАЛИВКА ЗАМКНЕНОЇ ОБЛАСТІ) ---
function floodFill(startX, startY, fillColor) {
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    const width = canvas.width;
    const height = canvas.height;

    const startIdx = (startY * width + startX) * 4;
    const startR = data[startIdx];
    const startG = data[startIdx + 1];
    const startB = data[startIdx + 2];

    const queue = [[startX, startY]];

    // Толерантність (похибка), щоб фарба гарно заходила на згладжені краї ліній
    const tolerance = 45;

    while (queue.length > 0) {
        const [x, y] = queue.pop();
        const idx = (y * width + x) * 4;

        // Перевіряємо, чи поточний піксель схожий на початковий колір заливки (білий)
        if (Math.abs(data[idx] - startR) <= tolerance &&
            Math.abs(data[idx + 1] - startG) <= tolerance &&
            Math.abs(data[idx + 2] - startB) <= tolerance) {
            
            // Якщо піксель уже зафарбований потрібним кольором — пропускаємо, щоб уникнути нескінченного циклу
            if (data[idx] === fillColor.r && data[idx + 1] === fillColor.g && data[idx + 2] === fillColor.b) {
                continue;
            }

            // Заливаємо новим кольором
            data[idx] = fillColor.r;
            data[idx + 1] = fillColor.g;
            data[idx + 2] = fillColor.b;

            // Додаємо сусідні пікселі в чергу для сканування кордонів
            if (x > 0) queue.push([x - 1, y]);
            if (x < width - 1) queue.push([x + 1, y]);
            if (y > 0) queue.push([x, y - 1]);
            if (y < height - 1) queue.push([x, y + 1]);
        }
    }
    // Оновлюємо полотно
    ctx.putImageData(imageData, 0, 0);
}

nextBtn.addEventListener('click', () => {
    currentLevel = (currentLevel + 1) % complexContours.length;
    loadLevel();
});

clearBtn.addEventListener('click', loadLevel);

window.addEventListener('DOMContentLoaded', () => {
    generatePalette();
    loadLevel();
});

// Надіслати на сервер
saveBtn.addEventListener('click', () => {
    if (<?= isset($_SESSION['user_id']) ? 'false' : 'true' ?>) {
        alert('Авторизуйтесь перед надсиланням!');
        window.location.href = 'login.php';
        return;
    }
    saveBtn.disabled = true;
    fetch('backend/save_drawing.php', { 
        method: 'POST', 
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
        body: 'image=' + encodeURIComponent(canvas.toDataURL('image/png')) 
    })
    .then(r => r.json()).then(d => {
        saveBtn.disabled = false;
        if(d.status === 'success') alert('Ваш малюнок надіслано! Номер замовлення: #' + d.order_id);
    });
});
</script>
<footer style="margin-top: 50px; padding: 20px 0; text-align: center; border-top: 1px solid #e1e8ed; color: #7f8c8d; font-size: 0.95rem; width: 100%;">
    <p>&copy; 2026 Ксенія Замараєва. Всі права захищено.</p>
</footer>
</body>
</html>