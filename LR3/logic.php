<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$database = "architectural_bureau";

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $database);

// Проверяем подключение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8");
?>