<?php
// config.php
session_start();

// Параметры БД
$host = 'localhost';
$dbname = 'architectural_bureau';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения к БД");
}

// Проверка авторизации
function is_auth() {
    return isset($_SESSION['user_id']);
}

// Получение данных пользователя
function get_user() {
    global $pdo;
    if (!is_auth()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Проверка пароля по новым требованиям
function validate_password($password) {
    $errors = [];
    
    if (strlen($password) <= 6) {
        $errors[] = "Пароль должен быть длиннее 6 символов";
    }
    
    // Проверка наличия больших латинских букв
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Пароль должен содержать хотя бы одну заглавную латинскую букву";
    }
    
    // Проверка наличия маленьких латинских букв
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Пароль должен содержать хотя бы одну строчную латинскую букву";
    }
    
    // Проверка наличия цифр
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Пароль должен содержать хотя бы одну цифру";
    }
    
    // Проверка наличия спецсимволов (знаки препинания, арифметические действия, пробел, дефис, подчеркивание)
    if (!preg_match('/[!@#$%^&*()\-_=+\[\]{};:\'",.<>?\/`~\\\\| ]/', $password)) {
        $errors[] = "Пароль должен содержать хотя бы один спецсимвол (знаки препинания, +, -, _, пробел и т.д.)";
    }
    
    // Проверка на русские буквы
    if (preg_match('/[а-яА-Я]/u', $password)) {
        $errors[] = "Пароль не должен содержать русские буквы";
    }
    
    return $errors;
}
?>