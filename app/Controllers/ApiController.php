<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

/**
 * Оновлений API контролер з пагінацією
 */
class ApiController extends Controller
{
    private $categoryModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
        $this->productModel = new Product();

        $this->setCorsHeaders();
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Отримати товари з пагінацією через API
     */
    public function products()
    {
        try {
            // Отримати параметри
            $categoryId = $this->get('category');
            $sort = $this->get('sort', 'price_asc');
            $page = max(1, (int) $this->get('page', 1));
            $limit = max(1, min(50, (int) $this->get('limit', 12))); // Обмежити до 50
            $search = $this->get('search');

            // Валідація categoryId
            if ($categoryId !== null) {
                $categoryId = (int) $categoryId;
                if ($categoryId <= 0) {
                    $categoryId = null;
                }
            }

            // Парсити параметри сортування
            [$sortField, $sortOrder] = $this->productModel->parseSortParams($sort);

            // Отримати товари з пагінацією
            if ($search && strlen(trim($search)) >= 2) {
                // Пошук з пагінацією
                $products = $this->productModel->searchPaginated(trim($search), $categoryId, $page, $limit);
                $totalCount = $this->productModel->getTotalCount($categoryId, trim($search));
            } else {
                // Звичайна фільтрація з пагінацією
                $products = $this->productModel->getAllWithCategoryPaginated(
                    $categoryId,
                    $sortField,
                    $sortOrder,
                    $page,
                    $limit
                );
                $totalCount = $this->productModel->getTotalCount($categoryId);
            }

            // Отримати інформацію про пагінацію
            $pagination = $this->productModel->getPaginationInfo($totalCount, $page, $limit);

            // Підготувати відповідь
            $responseData = [
                'products' => $products,
                'pagination' => $pagination,
                'params' => [
                    'category' => $categoryId,
                    'sort' => $sort,
                    'search' => $search,
                    'page' => $page,
                    'limit' => $limit
                ]
            ];

            $this->jsonSuccess($responseData);

        } catch (\Exception $e) {
            $this->jsonError('Помилка завантаження товарів: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Отримати конкретну сторінку товарів (окремий endpoint)
     */
    public function page()
    {
        $this->products(); // Використовуємо той же метод
    }

    /**
     * Швидкий пошук з автодоповненням (без пагінації)
     */
    public function quickSearch()
    {
        try {
            $searchTerm = $this->get('q', '');
            $limit = min(10, (int) $this->get('limit', 5)); // Максимум 10 для автодоповнення

            if (strlen(trim($searchTerm)) < 2) {
                $this->jsonSuccess([]);
                return;
            }

            // Швидкий пошук без пагінації
            $query = "
                SELECT 
                    p.id,
                    p.name,
                    p.price,
                    c.name as category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.name LIKE :search
                ORDER BY p.name ASC
                LIMIT :limit
            ";

            $results = $this->productModel->query($query, [
                'search' => "%{$searchTerm}%",
                'limit' => $limit
            ]);

            $this->jsonSuccess($results);

        } catch (\Exception $e) {
            $this->jsonError('Помилка пошуку: ' . $e->getMessage(), 500);
        }
    }

    // Інші методи залишаються без змін...
    public function categories()
    {
        try {
            $categories = $this->categoryModel->getAllWithProductCount();
            $this->jsonSuccess($categories);
        } catch (\Exception $e) {
            $this->jsonError('Помилка завантаження категорій: ' . $e->getMessage(), 500);
        }
    }

    public function product()
    {
        try {
            $productId = $this->get('id');

            if (!$productId || !is_numeric($productId)) {
                $this->jsonError('Невірний ID товару', 400);
                return;
            }

            $productId = (int) $productId;

            if ($productId <= 0) {
                $this->jsonError('Невірний ID товару', 400);
                return;
            }

            $product = $this->productModel->findWithCategory($productId);

            if (!$product) {
                $this->jsonError('Товар не знайдено', 404);
                return;
            }

            $this->jsonSuccess($product);

        } catch (\Exception $e) {
            $this->jsonError('Помилка завантаження товару: ' . $e->getMessage(), 500);
        }
    }

    public function notFound()
    {
        $this->jsonError('API endpoint не знайдено', 404);
    }
}
?>