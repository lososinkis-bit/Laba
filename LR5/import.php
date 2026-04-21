<?php
// import.php
require_once 'config.php';

$page_title = 'Импорт данных';
require_once 'header.php';

// Определяем параметры импорта
$import_table = 'design_imported';
$format = 'json';

// Обработка импорта
$import_result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_file'])) {
    $file = $_FILES['import_file'];
    
    try {
        // Проверка на ошибки загрузки
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Ошибка при загрузке файла: ' . $file['error']);
        }
        
        // Проверка расширения файла
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file_ext !== 'json') {
            throw new Exception('Неверный тип файла. Разрешены только JSON файлы');
        }
        
        // Проверка MIME типа
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = ['application/json', 'text/plain'];
        if (!in_array($mime_type, $allowed_mimes)) {
            throw new Exception('Неверный MIME тип файла. Ожидается JSON');
        }
        
        // Чтение файла
        $handle = fopen($file['tmp_name'], 'r');
        $content = '';
        while (!feof($handle)) {
            $content .= fread($handle, 8192);
        }
        fclose($handle);
        
        // Парсинг JSON
        $data = json_decode($content, true);
        if ($data === null) {
            throw new Exception('Ошибка парсинга JSON: ' . json_last_error_msg());
        }
        
        if (!is_array($data) || count($data) == 0) {
            throw new Exception('Файл не содержит данных');
        }
        
        // Создание новой таблицы для импорта
        $pdo->exec("DROP TABLE IF EXISTS $import_table");
        
        // Определяем структуру таблицы
        $first_row = $data[0];
        $columns = array_keys($first_row);
        
        $create_sql = "CREATE TABLE $import_table (";
        $create_sql .= "id INT AUTO_INCREMENT PRIMARY KEY";
        
        foreach ($columns as $col) {
            if ($col != 'id') {
                $sample = $first_row[$col];
                if (is_numeric($sample)) {
                    $type = 'INT';
                } elseif (is_string($sample) && strlen($sample) > 255) {
                    $type = 'TEXT';
                } else {
                    $type = 'VARCHAR(255)';
                }
                $create_sql .= ", `$col` $type";
            }
        }
        $create_sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($create_sql);
        
        // Подготовка вставки
        $insert_cols = array_filter($columns, function($c) { return $c != 'id'; });
        $placeholders = implode(', ', array_fill(0, count($insert_cols), '?'));
        $insert_sql = "INSERT INTO $import_table (" . implode(', ', array_map(function($c) {
            return "`$c`";
        }, $insert_cols)) . ") VALUES ($placeholders)";
        
        $stmt = $pdo->prepare($insert_sql);
        $insert_count = 0;
        
        // Вставка данных
        foreach ($data as $row) {
            $values = [];
            foreach ($insert_cols as $col) {
                $values[] = $row[$col] ?? null;
            }
            
            try {
                $stmt->execute($values);
                $insert_count++;
            } catch (PDOException $e) {
                // Пропускаем проблемные записи
                continue;
            }
        }
        
        $import_result = '<div class="success">';
        $import_result .= '<p>✅ Импорт выполнен успешно!</p>';
        $import_result .= '<p><strong>Источник:</strong> Загруженный пользователем файл</p>';
        $import_result .= '<p><strong>Формат:</strong> ' . $format . '</p>';
        $import_result .= '<p><strong>Создана таблица:</strong> ' . $import_table . '</p>';
        $import_result .= '<p><strong>Записей импортировано:</strong> ' . $insert_count . '</p>';
        $import_result .= '</div>';
        
    } catch (Exception $e) {
        $import_result = '<div class="error">Ошибка импорта: ' . $e->getMessage() . '</div>';
    }
}
?>

<style>
    .import-container {
        max-width: 800px;
        margin: 30px auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .info-box {
        background: #e8f5e9;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 25px;
        border-left: 5px solid #4caf50;
    }
    
    .info-box p {
        margin: 8px 0;
        font-size: 16px;
    }
    
    .info-box strong {
        color: #2e7d32;
    }
    
    .import-form {
        background: #f5f5f5;
        padding: 25px;
        border-radius: 5px;
        margin: 20px 0;
    }
    
    .file-input {
        width: 100%;
        padding: 15px;
        background: white;
        border: 2px dashed #4caf50;
        border-radius: 5px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }
    
    .file-input input {
        width: 100%;
        padding: 10px;
    }
    
    .btn-import {
        background: #4caf50;
        color: white;
        padding: 12px 30px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }
    
    .btn-import:hover {
        background: #45a049;
    }
    
    .success {
        background: #d4edda;
        color: #155724;
        padding: 20px;
        border-radius: 5px;
        margin: 20px 0;
        border-left: 5px solid #28a745;
    }
    
    .error {
        background: #f8d7da;
        color: #721c24;
        padding: 20px;
        border-radius: 5px;
        margin: 20px 0;
        border-left: 5px solid #dc3545;
    }
</style>

<div class="import-container">
    <h2>📥 Импорт данных</h2>
    
    <div class="info-box">
        <h3>Информация об импорте:</h3>
        <p><strong>Формат файла:</strong> JSON</p>
        <p><strong>Откуда загружается:</strong> Загруженный пользователем файл</p>
        <p><strong>Таблица для импорта:</strong> <?= $import_table ?></p>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="import-form">
        <div class="file-input">
            <label for="import_file"><strong>Выберите JSON файл для импорта:</strong></label><br><br>
            <input type="file" name="import_file" id="import_file" accept=".json" required>
        </div>
        
        <button type="submit" class="btn-import">Выполнить импорт</button>
    </form>
    
    <?php if ($import_result): ?>
        <?= $import_result ?>
    <?php endif; ?>
    
    <p style="text-align: center; margin-top: 20px;">
        <a href="design.php">← На главную</a>
    </p>
</div>

<?php require_once 'footer.php'; ?>