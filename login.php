<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="auth-wrapper">
        <div class="auth-tabs">
            <div class="auth-tab active" onclick="tab('login')">Вхід</div>
            <div class="auth-tab" onclick="tab('reg')">Реєстрація</div>
        </div>
        <form id="form-login" class="auth-form active" action="backend/auth_login.php" method="POST">
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Пароль</label><input type="password" name="password" required></div>
            <button class="btn">Увійти</button>
        </form>
        <form id="form-reg" class="auth-form" action="backend/auth_register.php" method="POST">
            <div class="form-group"><label>Ім'я</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Пароль</label><input type="password" name="password" required></div>
            <button class="btn">Зареєструватись</button>
        </form>
    </div>
</div>
<script>
function tab(t) {
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    document.querySelectorAll('.auth-tab').forEach(b => b.classList.remove('active'));
    if(t==='login') { document.getElementById('form-login').classList.add('active'); document.querySelectorAll('.auth-tab')[0].classList.add('active'); }
    else { document.getElementById('form-reg').classList.add('active'); document.querySelectorAll('.auth-tab')[1].classList.add('active'); }
}
</script>
<footer style="margin-top: 50px; padding: 20px 0; text-align: center; border-top: 1px solid #e1e8ed; color: #7f8c8d; font-size: 0.95rem; width: 100%;">
    <p>&copy; 2026 Ксенія Замараєва. Всі права захищено.</p>
</footer>
</body>
</html>