<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Творча Майстерня — Розмальовка</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .game-layout {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            margin-top: 20px;
        }
        
        /* Контейнер для полотна із сучасною м'якою тінню та закругленням */
        .canvas-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 20px;
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 840px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        canvas {
            width: 100%;
            height: auto;
            max-width: 800px;
            background: #ffffff;
            cursor: crosshair;
            border-radius: 16px;
            transition: transform 0.3s ease;
        }

        /* Оновлена преміальна палітра */
        .palette-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            width: 100%;
            max-width: 840px;
            text-align: center;
        }

        .palette-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .game-palette {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Круглі 3D кнопки кольорів */
        .color-number-btn {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            cursor: pointer;
            border: 4px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            position: relative;
        }

        .color-number-btn:hover {
            transform: scale(1.12) translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .color-number-btn.active {
            transform: scale(1.15) translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 12px 20px rgba(99, 102, 241, 0.35);
        }

        /* Інформаційне табло стану гри */
        .status-banner {
            background: #eef2ff;
            border-left: 4px solid var(--primary);
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            color: #312e81;
            width: 100%;
            max-width: 840px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-sm);
        }

        /* Панель інструментів та дій */
        .action-bar {
            width: 100%;
            max-width: 840px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .action-bar .btn {
            flex: 1;
            min-width: 160px;
            gap: 8px;
        }

        @media (max-width: 600px) {
            .canvas-card, .palette-card {
                padding: 10px;
                border-radius: 16px;
            }
            .color-number-btn {
                width: 44px;
                height: 44px;
                font-size: 1rem;
            }
            .action-bar {
                flex-direction: column;
            }
            .action-bar .btn {
                width: 100%;
            }
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
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="cabinet.php">🛒 Кошик</a>
            <a href="backend/logout.php" style="color: var(--accent);">Вихід</a>
        <?php else: ?>
            <a href="login.php">Вхід</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1>🎨 Інтерактивний Арт-Простір</h1>
        <p style="color: var(--text-muted); margin-top: 4px;">Розфарбовуйте замкнені області за номерами одним кліком мишки</p>
    </div>

    <div class="game-layout">
        <div class="status-banner" id="game-message">
            🚀 Оберіть номер кольору на палітрі нижче та почніть творити!
        </div>

        <div class="canvas-card">
            <canvas id="canvas" width="800" height="550"></canvas>
        </div>

        <div class="palette-card">
            <div class="palette-title">Доступна палітра художника</div>
            <div class="game-palette" id="game-palette"></div>
        </div>

        <div class="action-bar">
            <label class="btn" style="background-color: #f1f5f9; color: var(--text-main); border: 1px solid var(--border-color); text-align: center; cursor: pointer;">
                📁 Свій контур
                <input type="file" id="user-contour-input" accept="image/*" style="display: none;">
            </label>
            <button class="btn" id="next-contour" style="background-color: #e0e7ff; color: var(--primary);">🎲 Інший малюнок</button>
            <button class="btn" id="clear" style="background-color: #f8fafc; color: var(--text-muted); border: 1px solid var(--border-color);">🧼 Скинути колір</button>
            <button class="btn" id="save" style="background: linear-gradient(135deg, var(--primary) 0%, #a855f7 100%); color: white;">🚀 Зберегти роботу</button>
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
const userContourInput = document.getElementById('user-contour-input');

let currentLevel = 0;
let selectedNumber = 1;

const gameColors = {
    1: '#ff4757', 2: '#2ed573', 3: '#1e90ff', 4: '#ffa502', 5: '#9b59b6',
    6: '#ff6b81', 7: '#48dbfb', 8: '#ff9f43', 9: '#1dd1a1', 10: '#34495e'
};

const complexContours = [
    'https://cdn-icons-png.flaticon.com/512/2560/2560403.png',
    'https://cdn-icons-png.flaticon.com/512/2995/2995475.png',
    'https://cdn-icons-png.flaticon.com/512/9373/9373142.png',
    'https://cdn-icons-png.flaticon.com/512/4148/4148705.png',
    'https://cdn-icons-png.flaticon.com/512/2205/2205044.png',
    'https://cdn-icons-png.flaticon.com/512/3069/3069276.png',
    'https://cdn-icons-png.flaticon.com/512/10317/10317079.png',
    'https://cdn-icons-png.flaticon.com/512/8663/8663243.png',
    'https://cdn-icons-png.flaticon.com/512/9908/9908332.png',
    'https://cdn-icons-png.flaticon.com/512/1150/1150626.png',
    'https://cdn-icons-png.flaticon.com/512/2306/2306161.png',
    'https://cdn-icons-png.flaticon.com/512/3122/3122421.png',
    'https://cdn-icons-png.flaticon.com/512/3024/3024527.png',
    'https://cdn-icons-png.flaticon.com/512/3660/3660144.png',
    'https://cdn-icons-png.flaticon.com/512/1904/1904425.png',
    'https://cdn-icons-png.flaticon.com/512/1040/1040407.png',
    'https://cdn-icons-png.flaticon.com/512/3069/3069223.png',
    'https://cdn-icons-png.flaticon.com/512/2554/2554030.png',
    'https://cdn-icons-png.flaticon.com/512/1044/1044391.png',
    'https://cdn-icons-png.flaticon.com/512/4800/4800455.png'
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
            showMessage(`🖌️ Активний інструмент: Колір №${selectedNumber}. Клікніть на зону рисунка!`);
        });
        paletteContainer.appendChild(btn);
    });
}

function showMessage(text) {
    gameMessage.innerHTML = text;
}

function loadLevel() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const img = new Image();
    img.crossOrigin = "anonymous";
    img.src = complexContours[currentLevel];
    
    img.onload = function() {
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        const size = 530;
        const x = (canvas.width - size) / 2;
        const y = (canvas.height - size) / 2;
        ctx.drawImage(img, x, y, size, size);
        showMessage(`✨ Шаблон №${currentLevel + 1} успішно завантажено. Приємної творчості!`);
    };
}

canvas.addEventListener('click', function(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    const x = Math.floor((e.clientX - rect.left) * scaleX);
    const y = Math.floor((e.clientY - rect.top) * scaleY);

    const pixel = ctx.getImageData(x, y, 1, 1).data;
    const r = pixel[0], g = pixel[1], b = pixel[2];

    if (r < 60 && g < 60 && b < 60) {
        showMessage("🛡️ Ой, це лінія контуру! Натисніть всередині білої області малюнка.");
        return;
    }

    const targetColor = hexToRgb(gameColors[selectedNumber]);
    if (r === targetColor.r && g === targetColor.g && b === targetColor.b) return;

    floodFill(x, y, targetColor);
    showMessage(`⚡ Успішно зафарбовано зону кольором №${selectedNumber}!`);
});

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return { r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16) };
}

function floodFill(startX, startY, fillColor) {
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    const width = canvas.width;
    const height = canvas.height;

    const startIdx = (startY * width + startX) * 4;
    const startR = data[startIdx], startG = data[startIdx + 1], startB = data[startIdx + 2];

    const queue = [[startX, startY]];
    const tolerance = 45;

    while (queue.length > 0) {
        const [x, y] = queue.pop();
        const idx = (y * width + x) * 4;

        if (Math.abs(data[idx] - startR) <= tolerance &&
            Math.abs(data[idx + 1] - startG) <= tolerance &&
            Math.abs(data[idx + 2] - startB) <= tolerance) {
            
            if (data[idx] === fillColor.r && data[idx + 1] === fillColor.g && data[idx + 2] === fillColor.b) continue;

            data[idx] = fillColor.r; data[idx + 1] = fillColor.g; data[idx + 2] = fillColor.b;

            if (x > 0) queue.push([x - 1, y]);
            if (x < width - 1) queue.push([x + 1, y]);
            if (y > 0) queue.push([x, y - 1]);
            if (y < height - 1) queue.push([x, y + 1]);
        }
    }
    ctx.putImageData(imageData, 0, 0);
}

userContourInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        const img = new Image();
        img.src = event.target.result;
        img.onload = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            const maxCanvasSize = 530;
            let imgWidth = img.width, imgHeight = img.height;
            if (imgWidth > imgHeight) {
                if (imgWidth > maxCanvasSize) { imgHeight *= maxCanvasSize / imgWidth; imgWidth = maxCanvasSize; }
            } else {
                if (imgHeight > maxCanvasSize) { imgWidth *= maxCanvasSize / imgHeight; imgHeight = maxCanvasSize; }
            }
            const x = (canvas.width - imgWidth) / 2, y = (canvas.height - imgHeight) / 2;
            ctx.drawImage(img, x, y, imgWidth, imgHeight);
            showMessage(`📸 Ваш власний контур успішно інтегровано на полотно!`);
        };
    };
    reader.readAsDataURL(file);
});

nextBtn.addEventListener('click', () => { currentLevel = (currentLevel + 1) % complexContours.length; loadLevel(); });
clearBtn.addEventListener('click', loadLevel);

window.addEventListener('DOMContentLoaded', () => { generatePalette(); loadLevel(); });

saveBtn.addEventListener('click', () => {
    if (<?= isset($_SESSION['user_id']) ? 'false' : 'true' ?>) { alert('Будь ласка, авторизуйтесь перед збереженням!'); window.location.href = 'login.php'; return; }
    saveBtn.disabled = true;
    fetch('backend/save_drawing.php', { 
        method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
        body: 'image=' + encodeURIComponent(canvas.toDataURL('image/png')) 
    }).then(r => r.json()).then(d => {
        saveBtn.disabled = false;
        if(d.status === 'success') alert('Шедевр збережено! Номер замовлення в системі: #' + d.order_id);
    });
});
</script>
</body>
</html>