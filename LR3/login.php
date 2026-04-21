<?php
// login.php
$page_title = 'Вход';
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

$error = '';
$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Заполните все поля";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $error = "Пользователь не найден";
        } elseif (!password_verify($password, $user['password_hash'])) {
            $error = "Неверный пароль";
        } else {
            $_SESSION['user_id'] = $user['id'];
            header("Location: design.php");
            exit;
        }
    }
}
?>

<style>
    .login-form {
        max-width: 400px;
        margin: 50px auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .form-group { margin-bottom: 20px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; }
    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .big-button {
        background: #007bff;
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
        background: #0056b3;
    }
    .links { text-align: center; margin-top: 20px; }
    .links a { color: #007bff; text-decoration: none; }
    .links a:hover { text-decoration: underline; }
</style>

<div class="login-form">
    <h2 style="text-align: center; margin-bottom: 30px;">Вход на сайт</h2>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" class="big-button">Войти</button>
    </form>
    
    <div class="links">
        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
        <p><a href="design.php">← На главную</a></p>
    </div>
</div>

<?php require_once 'footer.php'; ?>