<?php
// register.php
$page_title = 'Регистрация';
require_once 'header.php';

if (is_auth()) {
    $user = get_user();
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; text-align: center;'>";
    echo "Вы уже авторизованы как " . htmlspecialchars($user['email']);
    echo " <a href='logout.php' style='color: #155724; font-weight: bold;'>Выйти</a>";
    echo "</div>";
    require_once 'footer.php';
    exit;
}

$errors = [];
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $birth_date = $_POST['birth_date'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $gender = $_POST['gender'] ?? 'other';
    $interests = trim($_POST['interests'] ?? '');
    $vk_profile = trim($_POST['vk_profile'] ?? '');
    $blood_type = $_POST['blood_type'] ?? '';
    $rh_factor = $_POST['rh_factor'] ?? '';
    
    // Валидация
    if (empty($username)) $errors[] = "Введите имя пользователя";
    if (strlen($username) < 3) $errors[] = "Имя минимум 3 символа";
    
    if (empty($email)) $errors[] = "Введите email";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Некорректный email";
    
    if (empty($password)) $errors[] = "Введите пароль";
    else {
        $pass_errors = validate_password($password);
        $errors = array_merge($errors, $pass_errors);
    }
    
    if ($password !== $password_confirm) $errors[] = "Пароли не совпадают";
    
    // Проверка email
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Email уже зарегистрирован";
    }
    
    // Сохранение
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, full_name, birth_date, address, gender, interests, vk_profile, blood_type, rh_factor) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$username, $email, $password_hash, $full_name, $birth_date, $address, $gender, $interests, $vk_profile, $blood_type, $rh_factor])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header("Location: design.php");
            exit;
        } else {
            $errors[] = "Ошибка при регистрации";
        }
    }
}
?>

<style>
    .register-form {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; }
    input, select, textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    textarea { height: 80px; }
    .error-box {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .error-box ul { margin-left: 20px; }
    .hint { font-size: 12px; color: #666; margin-top: 5px; }
    .btn {
        background: #007bff;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 4px;
        width: 100%;
        cursor: pointer;
        font-size: 16px;
    }
    .btn:hover { background: #0056b3; }
    .links { text-align: center; margin-top: 20px; }
    .row2 { display: flex; gap: 15px; }
    .row2 > div { flex: 1; }
    
    /* Стили для кнопки */
    .big-button {
        background: #28a745;
        color: white;
        padding: 15px 30px;
        font-size: 18px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
        font-weight: bold;
        transition: background 0.3s;
    }
    .big-button:hover {
        background: #218838;
    }
    
    .header-small {
        text-align: center;
        color: #666;
        margin-bottom: 10px;
        font-size: 14px;
    }
</style>

<div class="register-form">
    <h2 style="text-align: center; margin-bottom: 10px;">Регистрация нового пользователя</h2>
    <div class="header-small">Заполните все обязательные поля (*)</div>
    
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <strong>Ошибки:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Имя пользователя *</label>
            <input type="text" name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email (логин) *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
        </div>
        
        <div class="row2">
            <div class="form-group">
                <label>Пароль *</label>
                <input type="password" name="password" required>
                <div class="hint">
                    Длиннее 6 символов, заглавные и строчные латинские буквы,<br>
                    цифры, спецсимволы (.,!?+-_ и др.). Без русских букв.
                </div>
            </div>
            <div class="form-group">
                <label>Подтверждение *</label>
                <input type="password" name="password_confirm" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>ФИО</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($old['full_name'] ?? '') ?>">
        </div>
        
        <div class="row2">
            <div class="form-group">
                <label>Дата рождения</label>
                <input type="date" name="birth_date" value="<?= htmlspecialchars($old['birth_date'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Пол</label>
                <select name="gender">
                    <option value="male" <?= ($old['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Мужской</option>
                    <option value="female" <?= ($old['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Женский</option>
                    <option value="other" <?= ($old['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Другой</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Адрес</label>
            <textarea name="address"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Интересы</label>
            <textarea name="interests"><?= htmlspecialchars($old['interests'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Ссылка на профиль ВК</label>
            <input type="url" name="vk_profile" value="<?= htmlspecialchars($old['vk_profile'] ?? '') ?>">
        </div>
        
        <div class="row2">
            <div class="form-group">
                <label>Группа крови</label>
                <select name="blood_type">
                    <option value="">-- выберите --</option>
                    <option value="A" <?= ($old['blood_type'] ?? '') == 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= ($old['blood_type'] ?? '') == 'B' ? 'selected' : '' ?>>B</option>
                    <option value="AB" <?= ($old['blood_type'] ?? '') == 'AB' ? 'selected' : '' ?>>AB</option>
                    <option value="O" <?= ($old['blood_type'] ?? '') == 'O' ? 'selected' : '' ?>>O</option>
                </select>
            </div>
            <div class="form-group">
                <label>Резус-фактор</label>
                <select name="rh_factor">
                    <option value="">-- выберите --</option>
                    <option value="+" <?= ($old['rh_factor'] ?? '') == '+' ? 'selected' : '' ?>>Положительный</option>
                    <option value="-" <?= ($old['rh_factor'] ?? '') == '-' ? 'selected' : '' ?>>Отрицательный</option>
                </select>
            </div>
        </div>
        
        <!-- Кнопка регистрации -->
        <button type="submit" class="big-button">Зарегистрироваться</button>
    </form>
    
    <div class="links">
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
        <p><a href="design.php">← Вернуться на главную</a></p>
    </div>
</div>

<?php require_once 'footer.php'; ?>