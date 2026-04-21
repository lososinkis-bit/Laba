<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Проекты архитектурного бюро</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        
        /* Кнопка регистрации */
        .register-button-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .register-btn {
            background: #28a745;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        .register-btn:hover {
            background: #218838;
        }
        
        /* Форма фильтрации */
        .filter-form {
            background: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        .filter-group {
            flex: 1 1 200px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .filter-group input, .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .filter-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        
        /* Таблица */
        .table-wrapper {
            overflow-x: auto;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th {
            background: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            position: sticky;
            top: 0;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
            word-wrap: break-word;
        }
        tr:hover {
            background: #f5f5f5;
        }
        
        th:nth-child(1), td:nth-child(1) { width: 5%; }
        th:nth-child(2), td:nth-child(2) { width: 15%; }
        th:nth-child(3), td:nth-child(3) { width: 20%; }
        th:nth-child(4), td:nth-child(4) { width: 45%; }
        th:nth-child(5), td:nth-child(5) { width: 15%; }
        
        .category-badge {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            display: inline-block;
            white-space: normal;
            word-wrap: break-word;
        }
        .description-cell {
            max-width: 100%;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.5;
        }
        .no-data {
            text-align: center;
            color: #999;
            padding: 40px;
        }
        .stats {
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .active-filters {
            background: #fff3cd;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .filter-tag {
            background: #007bff;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            margin-right: 5px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏗️ Архитектурное бюро</h1>
        
        <!-- Кнопка регистрации -->
        <div class="register-button-container">
            <a href="register.php" class="register-btn">Зарегистрироваться</a>
        </div>
        
        <?php
        // Подключение к БД
        $conn = new mysqli("localhost", "root", "", "architectural_bureau");
        
        if ($conn->connect_error) {
            die("<p style='color:red;'>Ошибка подключения к БД: " . $conn->connect_error . "</p>");
        }
        
        $conn->set_charset("utf8");
        
        // Получаем список категорий из БД
        $categories = [];
        $cat_result = $conn->query("SELECT id, name FROM categories ORDER BY name");
        if ($cat_result) {
            while ($cat = $cat_result->fetch_assoc()) {
                $categories[$cat['id']] = $cat['name'];
            }
        }
        
        // Получаем значения фильтров из GET-запроса
        $filter_name = isset($_GET['filter_name']) ? trim($_GET['filter_name']) : '';
        $filter_desc = isset($_GET['filter_desc']) ? trim($_GET['filter_desc']) : '';
        $filter_category = isset($_GET['filter_category']) ? (int)$_GET['filter_category'] : 0;
        $filter_cost = isset($_GET['filter_cost']) ? trim($_GET['filter_cost']) : '';
        
        // Формируем WHERE часть запроса
        $where_conditions = [];
        $params = [];
        $types = "";
        
        if (!empty($filter_name)) {
            $where_conditions[] = "d.name LIKE ?";
            $params[] = "%$filter_name%";
            $types .= "s";
        }
        
        if (!empty($filter_desc)) {
            $where_conditions[] = "d.description LIKE ?";
            $params[] = "%$filter_desc%";
            $types .= "s";
        }
        
        if ($filter_category > 0) {
            $where_conditions[] = "d.id_brand = ?";
            $params[] = $filter_category;
            $types .= "i";
        }
        
        if (!empty($filter_cost)) {
            $where_conditions[] = "d.cost = ?";
            $params[] = $filter_cost;
            $types .= "i";
        }
        
        // Собираем полный WHERE
        $where_sql = "";
        if (count($where_conditions) > 0) {
            $where_sql = "WHERE " . implode(" AND ", $where_conditions);
        }
        
        // Основной запрос с JOIN для получения названия категории
        $sql = "SELECT d.*, c.name as category_name 
                FROM design d 
                LEFT JOIN categories c ON d.id_brand = c.id 
                $where_sql 
                ORDER BY d.id DESC";
        
        // Выполняем запрос
        if (!empty($params)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }
        
        // Получаем общее количество записей
        $total_all_result = $conn->query("SELECT COUNT(*) as cnt FROM design");
        $total_all = $total_all_result->fetch_assoc()['cnt'];
        
        $found_count = $result->num_rows;
        ?>
        
        <!-- Статистика -->
        <div class="stats">
            <strong>Найдено:</strong> <?= $found_count ?> из <?= $total_all ?> проектов
        </div>
        
        <!-- Форма фильтрации -->
        <div class="filter-form">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Название (содержит)</label>
                        <input type="text" name="filter_name" value="<?= htmlspecialchars($filter_name) ?>" 
                               placeholder="например: house">
                    </div>
                    <div class="filter-group">
                        <label>Описание (содержит)</label>
                        <input type="text" name="filter_desc" value="<?= htmlspecialchars($filter_desc) ?>" 
                               placeholder="например: частный">
                    </div>
                    <div class="filter-group">
                        <label>Категория (точно)</label>
                        <select name="filter_category">
                            <option value="0">Все категории</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?= $id ?>" <?= $filter_category == $id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Стоимость (точно)</label>
                        <input type="number" name="filter_cost" value="<?= htmlspecialchars($filter_cost) ?>" 
                               placeholder="например: 100000">
                    </div>
                </div>
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Применить фильтр</button>
                    <a href="?" class="btn btn-secondary">Сбросить фильтр</a>
                </div>
            </form>
        </div>
        
        <!-- Активные фильтры -->
        <?php if (!empty($filter_name) || !empty($filter_desc) || $filter_category > 0 || !empty($filter_cost)): ?>
        <div class="active-filters">
            <strong>Активные фильтры:</strong>
            <?php if (!empty($filter_name)): ?>
                <span class="filter-tag">Название содержит "<?= htmlspecialchars($filter_name) ?>"</span>
            <?php endif; ?>
            <?php if (!empty($filter_desc)): ?>
                <span class="filter-tag">Описание содержит "<?= htmlspecialchars($filter_desc) ?>"</span>
            <?php endif; ?>
            <?php if ($filter_category > 0 && isset($categories[$filter_category])): ?>
                <span class="filter-tag">Категория: <?= htmlspecialchars($categories[$filter_category]) ?></span>
            <?php endif; ?>
            <?php if (!empty($filter_cost)): ?>
                <span class="filter-tag">Стоимость = <?= number_format($filter_cost, 0, '', ' ') ?> ₽</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Таблица с результатами -->
        <?php if ($found_count > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Описание</th>
                            <th>Стоимость (₽)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                                <td>
                                    <?php if (!empty($row['category_name'])): ?>
                                        <span class="category-badge">
                                            <?= htmlspecialchars($row['category_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #999;">без категории</span>
                                    <?php endif; ?>
                                </td>
                                <td class="description-cell">
                                    <?php 
                                    if (!empty($row['description']) && $row['description'] != 'NULL') {
                                        echo nl2br(htmlspecialchars($row['description']));
                                    } else {
                                        echo '<span style="color: #999;">-</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= number_format($row['cost'], 0, '', ' ') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>😕 По вашему запросу ничего не найдено</p>
                <p><small>Попробуйте изменить параметры фильтрации</small></p>
            </div>
        <?php endif; ?>
        
        <?php 
        if (isset($stmt)) $stmt->close();
        $conn->close(); 
        ?>
    </div>
</body>
</html>