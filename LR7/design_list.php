<?php require_once 'config.php'; 
$designs = $designTable->getAllWithCategories();

if (isset($_GET['delete'])) {
    echo '<div style="max-width:400px; margin:50px auto; background:white; padding:30px; border-radius:8px; text-align:center;">';
    echo '<h3>Подтверждение удаления</h3>';
    echo '<p>Вы уверены?</p>';
    echo '<a href="design_delete.php?id='.$_GET['delete'].'" style="background:#dc3545; color:white; padding:10px 20px; text-decoration:none; margin-right:10px;">Да</a>';
    echo '<a href="design_list.php" style="background:#6c757d; color:white; padding:10px 20px; text-decoration:none;">Нет</a>';
    echo '</div>';
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Проекты</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { text-align: center; }
        .add-btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; float: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #007bff; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .cat { background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; display: inline-block; }
        
        /* Кнопки рядом без наложения */
        .actions {
            display: flex;
            gap: 8px;
        }
        .edit { 
            background: #ffc107; 
            color: black; 
            padding: 5px 12px; 
            text-decoration: none; 
            border-radius: 3px; 
            font-size: 14px;
            display: inline-block;
        }
        .delete { 
            background: #dc3545; 
            color: white; 
            padding: 5px 12px; 
            text-decoration: none; 
            border-radius: 3px; 
            font-size: 14px;
            display: inline-block;
        }
        .edit:hover { background: #e0a800; }
        .delete:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Архитектурное бюро</h1>
        <a href="design_form.php" class="add-btn">+ Добавить</a>
        
        <?php if (isset($_GET['success'])) echo '<div class="success">Проект успешно добавлен</div>'; ?>
        <?php if (isset($_GET['updated'])) echo '<div class="success">Проект успешно обновлен</div>'; ?>
        <?php if (isset($_GET['deleted'])) echo '<div class="success">Проект успешно удален</div>'; ?>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Категория</th>
                <th>Описание</th>
                <th>Стоимость</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($designs as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><b><?= htmlspecialchars($item['name']) ?></b></td>
                <td><span class="cat"><?= htmlspecialchars($item['category_name'] ?? '') ?></span></td>
                <td><?= htmlspecialchars(mb_substr($item['description'] ?? '-', 0, 50)) ?>...</td>
                <td><?= number_format($item['cost']) ?> руб.</td>
                <td class="actions">
                    <a href="design_edit.php?id=<?= $item['id'] ?>" class="edit">Редактировать</a>
                    <a href="?delete=<?= $item['id'] ?>" class="delete">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>