<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

/**
 * API контролер для Ajax запитів
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

        // Встановити CORS заголовки
        $this->setCorsHeaders();

        // Встановити Content-Type для JSON
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Отримати категорії з кількістю товарів
     */
    public function categories()
    {
        try {
            $categories = $this->categoryModel->getAllWithProductCount();

            $this->jsonSuccess($categories);

        } catch (\Exception $e) {
            $this->jsonError('Помилка завантаження категорій: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Отримати товари з фільтрацією та сортуванням
     */
    public function products()
    {
        try {
            // Отримати параметри
            $categoryId = $this->get('category');
            $sort = $this->get('sort', 'price_asc');
            $search = $this->get('search');
            $limit = $this->get('limit', 50);
            $offset = $this->get('offset', 0);

            // Валідація параметрів
            if ($categoryId !== null) {
                $categoryId = (int) $categoryId;
                if ($categoryId <= 0) {
                    $categoryId = null;
                }
            }

            $limit = max(1, min(100, (int) $limit)); // Обмежити від 1 до 100
            $offset = max(0, (int) $offset);

            // Парсити параметри сортування
            [$sortField, $sortOrder] = $this->productModel->parseSortParams($sort);

            // Отримати товари
            if ($search && strlen(trim($search)) >= 2) {
                // Пошук товарів
                $products = $this->productModel->search(trim($search), $categoryId);
            } else {
                // Звичайна фільтрація
                $products = $this->productModel->getAllWithCategory($categoryId, $sortField, $sortOrder);
            }

            // Застосувати пагінацію якщо потрібно
            if ($limit < 100) {
                $totalCount = count($products);
                $products = array_slice($products, $offset, $limit);

                $responseData = [
                    'products' => $products,
                    'total' => $totalCount,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $totalCount
                ];
            } else {
                $responseData = [
                    'products' => $products,
                    'total' => count($products)
                ];
            }

            // Додати параметри запиту до відповіді
            $responseData['params'] = [
                'category' => $categoryId,
                'sort' => $sort,
                'search' => $search
            ];

            $this->jsonSuccess($responseData);

        } catch (\Exception $e) {
            $this->jsonError('Помилка завантаження товарів: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Отримати конкретний товар за ID
     */
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

            // Отримати товар з інформацією про категорію
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

    /**
     * Отримати статистику
     */
    public function stats()
    {
        try {
            $stats = $this->productModel->getStats();
            $categoriesCount = $this->categoryModel->count();

            $responseData = array_merge($stats, [
                'categories_count' => $categoriesCount
            ]);

            $this->jsonSuccess($responseData);

        } catch (\Exception $e) {
            $this->jsonError('Помилка завантаження статистики: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Пошук товарів (окремий endpoint)
     */
    public function search()
    {
        try {
            $searchTerm = $this->get('q', '');
            $categoryId = $this->get('category');

            if (strlen(trim($searchTerm)) < 2) {
                $this->jsonError('Пошуковий запит повинен містити мінімум 2 символи', 400);
                return;
            }

            // Валідувати categoryId
            if ($categoryId !== null) {
                $categoryId = (int) $categoryId;
                if ($categoryId <= 0) {
                    $categoryId = null;
                }
            }

            $products = $this->productModel->search(trim($searchTerm), $categoryId);

            $responseData = [
                'products' => $products,
                'total' => count($products),
                'search_term' => trim($searchTerm),
                'category' => $categoryId
            ];

            $this->jsonSuccess($responseData);

        } catch (\Exception $e) {
            $this->jsonError('Помилка пошуку: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Отримати товари за ціновим діапазоном
     */
    public function priceRange()
    {
        try {
            $minPrice = $this->get('min', 0);
            $maxPrice = $this->get('max');
            $categoryId = $this->get('category');

            if ($maxPrice === null) {
                $this->jsonError('Максимальна ціна є обов\'язковою', 400);
                return;
            }

            $minPrice = max(0, (float) $minPrice);
            $maxPrice = max($minPrice, (float) $maxPrice);

            // Валідувати categoryId
            if ($categoryId !== null) {
                $categoryId = (int) $categoryId;
                if ($categoryId <= 0) {
                    $categoryId = null;
                }
            }

            $products = $this->productModel->getByPriceRange($minPrice, $maxPrice, $categoryId);

            $responseData = [
                'products' => $products,
                'total' => count($products),
                'price_range' => [
                    'min' => $minPrice,
                    'max' => $maxPrice
                ],
                'category' => $categoryId
            ];

            $this->jsonSuccess($responseData);

        } catch (\Exception $e) {
            $this->jsonError('Помилка фільтрації за ціною: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Обробити невідомі методи
     */
    public function notFound()
    {
        $this->jsonError('API endpoint не знайдено', 404);
    }
}
?>