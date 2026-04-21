<?php
// header.php
require_once 'config.php';
$user = get_user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?? 'Архитектурное бюро' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f0f0f0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .header {
            background: #333;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        
        .nav { display: flex; gap: 20px; }
        .nav a { color: white; text-decoration: none; }
        .nav a:hover { text-decoration: underline; }
        
        .auth-block {
            background: #444;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
        }
        .auth-block a { color: #ffd700; text-decoration: none; margin: 0 5px; }
        .auth-block a:hover { text-decoration: underline; }
        .logout-btn { background: #c0392b; padding: 3px 8px; border-radius: 3px; color: white !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; align-items: center; gap: 30px;">
                <h1>🏗️ Архитектурное бюро</h1>
                <div class="nav">
                    <a href="design.php">Главная</a>
                    <?php if (is_auth()): ?>
                        <a href="secret.php">Секретная</a>
                        <a href="export.php">Экспорт</a>
                        <a href="import.php">Импорт</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="auth-block">
                <?php if (is_auth()): ?>
                    <span>Вы вошли как <?= htmlspecialchars($user['email']) ?></span>
                    <a href="logout.php" class="logout-btn">Выйти</a>
                <?php else: ?>
                    <span>Вы не авторизованы.</span>
                    <a href="login.php">Войти</a>
                    <a href="register.php">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>