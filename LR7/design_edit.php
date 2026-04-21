<?php
require_once 'config.php';
session_start();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: design_list.php'); exit; }

$record = $designTable->getById($id);
if (!$record) { header('Location: design_list.php'); exit; }

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
    <title>Редактировать проект</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 100px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .btn { background: #ffc107; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #e0a800; }
        .cancel { background: #6c757d; padding: 8px 15px; color: white; text-decoration: none; border-radius: 4px; }
        .red { color: red; }
        .current-img { margin: 10px 0; background: #f5f5f5; padding: 10px; }
        .current-img img { max-width: 200px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Редактировать проект</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="design_update.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $record['id'] ?>">
            
            <div class="form-group">
                <label>Название <span class="red">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? $record['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Категория <span class="red">*</span></label>
                <select name="id_brand" required>
                    <option value="">-- Выберите --</option>
                    <?php foreach ($categories as $cid => $cname): ?>
                        <option value="<?= $cid ?>" <?= ($old['id_brand'] ?? $record['id_brand']) == $cid ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cname) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="current-img">
                <?php if (!empty($record['img_path']) && $record['img_path'] != 'no_img.png'): ?>
                    <img src="uploads/<?= $record['img_path'] ?>"><br>
                    <small>Текущее изображение</small>
                <?php else: ?>
                    <p>Нет изображения</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Новое изображение (оставьте пустым, чтобы не менять)</label>
                <input type="file" name="img_path" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Описание</label>
                <textarea name="description"><?= htmlspecialchars($old['description'] ?? $record['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Стоимость (руб.) <span class="red">*</span></label>
                <input type="number" name="cost" value="<?= htmlspecialchars($old['cost'] ?? $record['cost']) ?>" required min="1">
            </div>
            
            <div>
                <button type="submit" class="btn">Сохранить</button>
                <a href="design_list.php" class="cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>