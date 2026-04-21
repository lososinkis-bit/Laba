<?php
// secret.php
require_once 'config.php';

if (!is_auth()) {
    header("Location: login.php");
    exit;
}

$page_title = 'Секретная страница';
require_once 'header.php';
?>

<div style="background: #ffd700; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="text-align: center;">🔒 Секретная страница</h2>
    <p style="text-align: center;">Доступна только авторизованным пользователям</p>
</div>

<?php
// Здесь можно вставить любой контент, например скопировать из design.php
// Но для простоты покажем просто сообщение
?>

<p style="text-align: center; padding: 40px;">Это секретная страница. Здесь могла быть ваша реклама :)</p>

<?php require_once 'footer.php'; ?>