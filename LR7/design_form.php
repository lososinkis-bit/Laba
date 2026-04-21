<?php
require_once 'config.php';
session_start();

$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);

$pdo = Database::getInstance()->getConnection();
$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Добавить проект</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 100px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #218838; }
        .cancel { background: #6c757d; padding: 8px 15px; color: white; text-decoration: none; border-radius: 4px; }
        .red { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Добавить проект</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="design_create.php" enctype="multipart/form-data">
            <div class="form-group">
                <label>Название <span class="red">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Категория <span class="red">*</span></label>
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
                <label>Стоимость (руб.) <span class="red">*</span></label>
                <input type="number" name="cost" value="<?= htmlspecialchars($old['cost'] ?? '') ?>" required min="1">
            </div>
            
            <div>
                <button type="submit" class="btn">Сохранить</button>
                <a href="design_list.php" class="cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>