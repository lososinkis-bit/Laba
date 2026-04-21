<?php
// export.php
require_once 'config.php';

$page_title = 'Экспорт данных';
require_once 'header.php';

// Определяем таблицу для экспорта
$table_name = 'design';
$export_table = 'design_exported';
$format = 'json';

// Обработка экспорта
$export_result = '';
if (isset($_POST['export'])) {
    try {
        // Получаем данные из БД
        $stmt = $pdo->query("SELECT d.*, c.name as category_name 
                              FROM design d 
                              LEFT JOIN categories c ON d.id_brand = c.id 
                              ORDER BY d.id");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($data) > 0) {
            // Создаем временный файл
            $temp_file = tempnam(sys_get_temp_dir(), 'export_');
            
            // Экспорт в JSON
            $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($temp_file, $json_data);
            
            // Отправляем в worker.php через curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost/LR5/worker.php');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'userfile' => new CURLFile($temp_file, 'application/json', $export_table . '.json')
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $worker_response = curl_exec($ch);
            curl_close($ch);
            
            // Удаляем временный файл
            unlink($temp_file);
            
            $export_result = '<div class="success">';
            $export_result .= '<p>✅ Экспорт выполнен успешно!</p>';
            $export_result .= '<p>Формат: <strong>JSON</strong></p>';
            $export_result .= '<p>Файл сохранен на сервере</p>';
            $export_result .= '<p>Ответ от worker.php:</p>';
            $export_result .= '<div style="background:#f5f5f5; padding:10px; margin:10px 0;">' . $worker_response . '</div>';
            $export_result .= '</div>';
        } else {
            $export_result = '<div class="error">Нет данных для экспорта</div>';
        }
    } catch (Exception $e) {
        $export_result = '<div class="error">Ошибка экспорта: ' . $e->getMessage() . '</div>';
    }
}
?>

<style>
    .export-container {
        max-width: 800px;
        margin: 30px auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .info-box {
        background: #e3f2fd;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 25px;
        border-left: 5px solid #2196f3;
    }
    
    .info-box p {
        margin: 8px 0;
        font-size: 16px;
    }
    
    .info-box strong {
        color: #1976d2;
    }
    
    .export-form {
        text-align: center;
        margin: 30px 0;
    }
    
    .btn-export {
        background: #4caf50;
        color: white;
        padding: 15px 40px;
        font-size: 18px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .btn-export:hover {
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

<div class="export-container">
    <h2>📤 Экспорт данных</h2>
    
    <div class="info-box">
        <h3>Информация об экспорте:</h3>
        <p><strong>Формат файла:</strong> JSON</p>
        <p><strong>Имя файла:</strong> design_exported.json</p>
        <p><strong>Куда выгружается:</strong> Локальный сервер (worker.php)</p>
        <p><strong>Таблица:</strong> design</p>
    </div>
    
    <form method="POST" class="export-form">
        <button type="submit" name="export" class="btn-export">Выполнить экспорт</button>
    </form>
    
    <?php if ($export_result): ?>
        <?= $export_result ?>
    <?php endif; ?>
    
    <p style="text-align: center; margin-top: 20px;">
        <a href="design.php">← На главную</a>
    </p>
</div>

<?php require_once 'footer.php'; ?>