<?php
// design_add.php
require_once 'config.php';

// Обработка POST запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $designTable->validate($_POST);
    
    if (empty($errors)) {
        try {
            $designTable->insert($_POST);
            header('Location: design.php?success=1');
            exit;
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
    
    // Сохраняем ошибки и старые данные в сессию
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old'] = $_POST;
    header('Location: design_add.php');
    exit;
}

// Получаем категории
$pdo = Database::getInstance()->getConnection();
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_KEY_PAIR);

$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Добавление проекта</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 100px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .error ul { margin: 5px 0 0 20px; }
        .btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #218838; }
        .btn-secondary { background: #6c757d; text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Добавление проекта</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Ошибки:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Название *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Категория *</label>
                <select name="id_brand" required>
                    <option value="">-- Выберите --</option>
                    <?php foreach ($categories as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($old['id_brand'] ?? '') == $id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Изображение</label>
                <input type="file" name="img_path" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Описание</label>
                <textarea name="description"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Стоимость *</label>
                <input type="number" name="cost" value="<?= htmlspecialchars($old['cost'] ?? '') ?>" required min="1">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Сохранить</button>
                <a href="design.php" class="btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>