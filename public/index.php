<?php

// Запустити вивід буферизації
ob_start();

// Встановити звітність про помилки для розробки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Встановити часовий пояс
date_default_timezone_set('Europe/Kyiv');

// Встановити кодування
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Autoloader для класів (простий варіант)
spl_autoload_register(function ($className) {
    // Конвертувати namespace в шлях до файлу
    $file = __DIR__ . '/../' . str_replace(['\\', 'App/', 'Core/'], ['/', 'app/', 'core/'], $className) . '.php';

    if (file_exists($file)) {
        require_once $file;
        return true;
    }

    return false;
});

// Підключити основні класи
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Router.php';

use Core\Router;

try {
    // Створити роутер
    $router = new Router();

    // ====================================================
    // ОСНОВНІ МАРШРУТИ
    // ====================================================

    // Головна сторінка каталогу
    $router->get('/', 'CatalogController@index');

    // Сторінка каталогу з номером сторінки
    $router->get('/page/{page}', function($page) {
        $_GET['page'] = $page;
        $controller = new App\Controllers\CatalogController();
        return $controller->index();
    });

    // Категорія з пагінацією
    $router->get('/category/{id}/page/{page}', function($id, $page) {
        $_GET['category'] = $id;
        $_GET['page'] = $page;
        $controller = new App\Controllers\CatalogController();
        return $controller->index();
    });

    // Пошук
    $router->get('/search', 'CatalogController@search');

    // Пошук з пагінацією
    $router->get('/search/page/{page}', function($page) {
        $_GET['page'] = $page;
        $controller = new App\Controllers\CatalogController();
        return $controller->search();
    });

    // Сторінка товару
    $router->get('/product/{id}', 'CatalogController@product');

    // Категорія (редирект)
    $router->get('/category/{id}', 'CatalogController@category');

    // ====================================================
    // API МАРШРУТИ
    // ====================================================

    $router->get('/api/categories', 'ApiController@categories');
    $router->get('/api/products', 'ApiController@products');
    $router->get('/api/products/page/{page}', 'ApiController@page');
    $router->get('/api/product', 'ApiController@product');
    $router->get('/api/search', 'ApiController@search');
    $router->get('/api/quick-search', 'ApiController@quickSearch');
    $router->get('/api/stats', 'ApiController@stats');
    $router->get('/api/price-range', 'ApiController@priceRange');

    // ====================================================
    // ДОДАТКОВІ SEO МАРШРУТИ
    // ====================================================

    // Красиві URL для категорій з назвами
    $router->get('/electronics', function() {
        $_GET['category'] = 1;
        $controller = new App\Controllers\CatalogController();
        return $controller->index();
    });

    $router->get('/electronics/page/{page}', function($page) {
        $_GET['category'] = 1;
        $_GET['page'] = $page;
        $controller = new App\Controllers\CatalogController();
        return $controller->index();
    });

    $router->get('/clothing', function() {
        $_GET['category'] = 2;
        $controller = new App\Controllers\CatalogController();
        return $controller->index();
    });

    $router->get('/clothing/page/{page}', function($page) {
        $_GET['category'] = 2;
        $_GET['page'] = $page;
        $controller = new App\Controllers\CatalogController();
        return $controller->index();
    });


    // Обробити запит
    $router->dispatch();

} catch (Exception $e) {
    // Обробка помилок
    http_response_code(500);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/api/') === false) {
        // Показати помилку для звичайних сторінок
        ?>
        <!DOCTYPE html>
        <html lang="uk">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Помилка - Каталог товарів</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-danger">
                        <h4>❌ Виникла помилка</h4>
                        <p><strong>Повідомлення:</strong> <?= htmlspecialchars($e->getMessage()) ?></p>
                        <?php if (ini_get('display_errors')): ?>
                            <hr>
                            <p><strong>Файл:</strong> <?= htmlspecialchars($e->getFile()) ?></p>
                            <p><strong>Рядок:</strong> <?= $e->getLine() ?></p>
                            <details>
                                <summary>Stack Trace</summary>
                                <pre><?= htmlspecialchars($e->getTraceAsString()) ?></pre>
                            </details>
                        <?php endif; ?>
                    </div>

                    <div class="text-center">
                        <a href="/" class="btn btn-primary">🏠 На головну</a>
                    </div>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?php
    } else {
        // JSON помилка для API
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ], JSON_UNESCAPED_UNICODE);
    }
}

// Закінчити буферизацію та вивести контент
ob_end_flush();
?>