<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

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
     * Головна сторінка каталогу з пагінацією
     */
    public function index()
    {
        try {
            // Отримати параметри з URL
            $categoryId = $this->get('category');
            $sort = $this->get('sort', 'price_asc');
            $page = max(1, (int) $this->get('page', 1));
            $itemsPerPage = 12; // Кількість товарів на сторінці

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

            // Отримати товари з пагінацією
            $products = $this->productModel->getAllWithCategoryPaginated(
                $categoryId,
                $sortField,
                $sortOrder,
                $page,
                $itemsPerPage
            );

            // Отримати загальну кількість товарів
            $totalItems = $this->productModel->getTotalCount($categoryId);

            // Отримати інформацію про пагінацію
            $pagination = $this->productModel->getPaginationInfo($totalItems, $page, $itemsPerPage);

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
                'currentPage' => $page,
                'pagination' => $pagination,
                'totalProducts' => $totalProducts,
                'title' => 'Каталог товарів'
            ]);

        } catch (\Exception $e) {
            $this->render('catalog/index', [
                'categories' => [],
                'products' => [],
                'currentCategory' => null,
                'currentCategoryId' => null,
                'currentSort' => 'price_asc',
                'currentPage' => 1,
                'pagination' => null,
                'totalProducts' => 0,
                'error' => 'Помилка завантаження даних',
                'title' => 'Каталог товарів'
            ]);
        }
    }

    /**
     * Пошук товарів з пагінацією
     */
    public function search()
    {
        try {
            $searchTerm = $this->get('q', '');
            $categoryId = $this->get('category');
            $page = max(1, (int) $this->get('page', 1));
            $itemsPerPage = 12;

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

            // Пошук товарів з пагінацією
            $products = $this->productModel->searchPaginated($searchTerm, $categoryId, $page, $itemsPerPage);

            // Отримати загальну кількість результатів пошуку
            $totalItems = $this->productModel->getTotalCount($categoryId, $searchTerm);

            // Отримати інформацію про пагінацію
            $pagination = $this->productModel->getPaginationInfo($totalItems, $page, $itemsPerPage);

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
                'currentPage' => $page,
                'pagination' => $pagination,
                'totalProducts' => array_sum(array_column($categories, 'product_count')),
                'searchTerm' => $searchTerm,
                'title' => 'Пошук: ' . htmlspecialchars($searchTerm)
            ]);

        } catch (\Exception $e) {
            $this->redirect('/');
        }
    }
}
?>