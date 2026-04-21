<?php
$host = 'localhost';
$dbname = 'architectural_bureau';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
} catch (PDOException $e) {
    die("Ошибка подключения к БД");
}

$stmt = $pdo->query("SELECT d.*, c.name as brand_name FROM design d LEFT JOIN categories c ON d.id_brand = c.id");
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Товары</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #eee; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        img { max-width: 50px; max-height: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Наши товары</h1>
        <table>
            <tr>
                <th>Изображение</th>
                <th>Наименование</th>
                <th>Бренд</th>
                <th>Описание</th>
                <th>Стоимость</th>
            </tr>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <?php if (!empty($item['img_path']) && $item['img_path'] != 'no_img.png'): ?>
                        <img src="uploads/<?= htmlspecialchars($item['img_path']) ?>">
                    <?php else: ?>
                        Нет фото
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['brand_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['description'] ?? 'Описание отсутствует.') ?></td>
                <td><?= number_format($item['cost']) ?> руб.</td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>