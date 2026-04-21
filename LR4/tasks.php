<?php
// tasks.php
require_once 'tasks_logic.php';

$inputText = '';
$resultTask1 = '';
$resultTask2 = '';
$resultTask3 = '';
$resultTask4 = '';
$tocHtml = '';
$fullTextWithAnchors = '';

// Обработка POST запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['html_code'])) {
    $inputText = $_POST['html_code'];
} 
// Обработка GET preset
elseif (isset($_GET['preset'])) {
    $presetData = getPresetText((int)$_GET['preset']);
    $inputText = $presetData['content'];
}

// Если есть текст - обрабатываем все задачи
if (!empty($inputText)) {
    // Задача 1: Прямая речь
    $resultTask1 = extractDirectSpeech($inputText);
    
    // Задача 2: Запятые и многоточия
    $resultTask2 = processPunctuation($inputText);
    
    // Задача 3: Оглавление
    $tocData = generateTOC($inputText);
    $tocHtml = $tocData[0];
    $fullTextWithAnchors = $tocData[1];
    $resultTask3 = $tocHtml . '<div class="full-text"><h3>Полный текст с якорями:</h3>' . $fullTextWithAnchors . '</div>';
    
    // Задача 4: Фильтр запретных слов
    $resultTask4 = filterForbiddenWords($inputText);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>4 задания по обработке текста</title>
    <link rel="stylesheet" href="tasks.css">
</head>
<body>
    <div class="container">
        <h1>4 задания по обработке текста</h1>
        
        <div class="presets">
            <strong>Выберите пример:</strong><br><br>
            <a href="?preset=1">Киноринхи</a>
            <a href="?preset=2">Статья о театре</a>
            <a href="?preset=3">Винни-Пух</a>
            <a href="tasks.php">Очистить</a>
        </div>
        
        <form method="POST">
            <h3>Вставьте HTML-текст:</h3>
            <textarea name="html_code" rows="10"><?php echo htmlspecialchars($inputText); ?></textarea><br>
            <button type="submit">Обработать все задания</button>
        </form>
        
        <?php if (!empty($inputText)): ?>
            <hr>
            
            <div class="stats">
                <strong>Статистика:</strong>
                Текст получен, обрабатываются все 4 задания...
            </div>
            
            <?php if (!empty($resultTask1)): ?>
                <div class="result-block">
                    <h2>Задание 1: Прямая речь (абзацы с длинного тире)</h2>
                    <?php echo $resultTask1; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($resultTask2)): ?>
                <div class="result-block">
                    <h2>Задание 2: Запятые перед "а", "но" и замена ... на …</h2>
                    <?php echo $resultTask2; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($resultTask3)): ?>
                <div class="result-block">
                    <h2>Задание 3: Оглавление по заголовкам h1-h3</h2>
                    <?php echo $resultTask3; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($resultTask4)): ?>
                <div class="result-block">
                    <h2>Задание 4: Фильтр запретных слов (пух, рот, делать, ехать, около, для)</h2>
                    <?php echo $resultTask4; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <p>Нет текста для обработки</p>
                <p>Выберите пример или вставьте свой HTML-текст</p>
                <p>и нажмите "Обработать"</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>