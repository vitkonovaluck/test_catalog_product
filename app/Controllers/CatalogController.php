<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

/**
 * Контролер каталогу товарів
 */
class CatalogController extends Controller
{
    private $categoryModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
        $this->productModel = new Product();
    }

    /**
     * Головна сторінка каталогу
     */
    public function index()
    {
        try {
            // Отримати параметри з URL
            $categoryId = $this->get('category');
            $sort = $this->get('sort', 'price_asc');

            // Валідувати categoryId
            if ($categoryId !== null) {
                $categoryId = (int) $categoryId;
                if ($categoryId <= 0) {
                    $categoryId = null;
                }
            }

            // Отримати категорії з кількістю товарів
            $categories = $this->categoryModel->getAllWithProductCount();

            // Парсити параметри сортування
            [$sortField, $sortOrder] = $this->productModel->parseSortParams($sort);

            // Отримати товари
            $products = $this->productModel->getAllWithCategory($categoryId, $sortField, $sortOrder);

            // Отримати поточну категорію якщо вибрана
            $currentCategory = null;
            if ($categoryId) {
                $currentCategory = $this->categoryModel->find($categoryId);
            }

            // Підрахувати загальну кількість товарів
            $totalProducts = array_sum(array_column($categories, 'product_count'));

            // Передати дані до шаблону
            $this->render('catalog/index', [
                'categories' => $categories,
                'products' => $products,
                'currentCategory' => $currentCategory,
                'currentCategoryId' => $categoryId,
                'currentSort' => $sort,
                'totalProducts' => $totalProducts,
                'title' => 'Каталог товарів'
            ]);

        } catch (\Exception $e) {
            // В продакшені тут був би логувальник
            $this->render('catalog/index', [
                'categories' => [],
                'products' => [],
                'currentCategory' => null,
                'currentCategoryId' => null,
                'currentSort' => 'price_asc',
                'totalProducts' => 0,
                'error' => 'Помилка завантаження даних',
                'title' => 'Каталог товарів'
            ]);
        }
    }

    /**
     * Пошук товарів
     */
    public function search()
    {
        try {
            $searchTerm = $this->get('q', '');
            $categoryId = $this->get('category');

            if (strlen(trim($searchTerm)) < 2) {
                $this->redirect('/');
                return;
            }

            // Валідувати categoryId
            if ($categoryId !== null) {
                $categoryId = (int) $categoryId;
                if ($categoryId <= 0) {
                    $categoryId = null;
                }
            }

            // Отримати категорії
            $categories = $this->categoryModel->getAllWithProductCount();

            // Пошук товарів
            $products = $this->productModel->search($searchTerm, $categoryId);

            // Отримати поточну категорію якщо вибрана
            $currentCategory = null;
            if ($categoryId) {
                $currentCategory = $this->categoryModel->find($categoryId);
            }

            $this->render('catalog/index', [
                'categories' => $categories,
                'products' => $products,
                'currentCategory' => $currentCategory,
                'currentCategoryId' => $categoryId,
                'currentSort' => 'name_asc',
                'totalProducts' => array_sum(array_column($categories, 'product_count')),
                'searchTerm' => $searchTerm,
                'title' => 'Пошук: ' . htmlspecialchars($searchTerm)
            ]);

        } catch (\Exception $e) {
            $this->redirect('/');
        }
    }

    /**
     * Деталі товару
     */
    public function product($id)
    {
        try {
            $productId = (int) $id;

            if ($productId <= 0) {
                $this->redirect('/');
                return;
            }

            // Отримати товар з інформацією про категорію
            $product = $this->productModel->findWithCategory($productId);

            if (!$product) {
                $this->redirect('/');
                return;
            }

            // Отримати схожі товари з тієї ж категорії
            $relatedProducts = $this->productModel->getByCategory(
                $product['category_id'],
                'price',
                'ASC'
            );

            // Видалити поточний товар зі списку схожих
            $relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
                return $p['id'] !== $productId;
            });

            // Обмежити кількість схожих товарів
            $relatedProducts = array_slice($relatedProducts, 0, 4);

            $this->render('catalog/product', [
                'product' => $product,
                'relatedProducts' => $relatedProducts,
                'title' => $product['name']
            ]);

        } catch (\Exception $e) {
            $this->redirect('/');
        }
    }

    /**
     * Сторінка категорії
     */
    public function category($id)
    {
        try {
            $categoryId = (int) $id;

            if ($categoryId <= 0) {
                $this->redirect('/');
                return;
            }

            // Перевірити чи існує категорія
            $category = $this->categoryModel->find($categoryId);

            if (!$category) {
                $this->redirect('/');
                return;
            }

            // Редирект на головну сторінку з параметром категорії
            $this->redirect("/?category={$categoryId}");

        } catch (\Exception $e) {
            $this->redirect('/');
        }
    }
}
?>