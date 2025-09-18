<?php

// Підключаємо конфіг

try {
    $config = require __DIR__ . '/../config/database.php';

// Формуємо DSN
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";

// Створюємо об'єкт PDO
    try {
        $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);

    } catch (PDOException $e) {
        die("Помилка підключення до БД: " . $e->getMessage());
    }

    $start = microtime(true);

    // Отримуємо всі категорії
    $stmt = $pdo->query("SELECT categories_id, parent_id FROM categories_");
    $categories = $stmt->fetchAll();

    // Індексуємо по ID
    $items = [];
    foreach ($categories as $cat) {
        $id = (int)$cat['categories_id'];
        $parent = (int)$cat['parent_id'];
        $items[$id] = ['id' => $id, 'parent_id' => $parent, 'children' => []];
    }

    // Формуємо дерево
    $tree = [];
    foreach ($items as $id => &$item) {
        if ($item['parent_id'] === 0) {
            $tree[$id] = &$item;
        } else {
            $parentId = $item['parent_id'];
            if (isset($items[$parentId])) {
                $items[$parentId]['children'][$id] = &$item;
            }
        }
    }
    unset($item); // знімаємо посилання

    // Приводимо до потрібного формату
    function buildArrayTree($nodes) {
        $result = [];
        foreach ($nodes as $id => $node) {
            if (!empty($node['children'])) {
                $result[$id] = buildArrayTree($node['children']);
            } else {
                $result[$id] = $id;
            }
        }
        return $result;
    }

    $finalTree = ['result'=>buildArrayTree($tree)];

    // Перевіряємо час
    $elapsed = microtime(true) - $start;
    if ($elapsed > 2) {
        echo " Попередження: виконання зайняло більше 2 секунд: {$elapsed}s<br>";
    }

    // Вивід

    echo '<pre>';
    print_r($finalTree);
    echo '</pre>';

} catch (Exception $e) {
    echo "❌ Помилка: " . $e->getMessage();
}

