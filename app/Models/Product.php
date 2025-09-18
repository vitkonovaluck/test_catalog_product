<?php

namespace App\Models;

/**
 * Модель товарів
 */
class Product extends Model
{
    protected $table = 'products';

    /**
     * Отримати товари з інформацією про категорії
     */
    public function getAllWithCategory($categoryId = null, $sortBy = 'price', $sortOrder = 'ASC')
    {
        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
        ";

        $params = [];

        // Фільтрація за категорією
        if ($categoryId !== null && $categoryId > 0) {
            $query .= " WHERE p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        // Сортування
        $allowedSorts = ['price', 'name', 'date_added', 'id'];
        $allowedOrders = ['ASC', 'DESC'];

        if (in_array($sortBy, $allowedSorts) && in_array($sortOrder, $allowedOrders)) {
            $query .= " ORDER BY p.{$sortBy} {$sortOrder}";
        } else {
            $query .= " ORDER BY p.price ASC";
        }

        return $this->db->select($query, $params);
    }

    /**
     * Отримати товар за ID з інформацією про категорію
     */
    public function findWithCategory($id)
    {
        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ";

        return $this->db->selectOne($query, ['id' => $id]);
    }

    /**
     * Отримати товари за категорією
     */
    public function getByCategory($categoryId, $sortBy = 'price', $sortOrder = 'ASC')
    {
        return $this->getAllWithCategory($categoryId, $sortBy, $sortOrder);
    }

    /**
     * Пошук товарів за назвою
     */
    public function search($searchTerm, $categoryId = null)
    {
        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE :search
        ";

        $params = ['search' => "%{$searchTerm}%"];

        if ($categoryId !== null && $categoryId > 0) {
            $query .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $query .= " ORDER BY p.name ASC";

        return $this->db->select($query, $params);
    }

    /**
     * Отримати товари в ціновому діапазоні
     */
    public function getByPriceRange($minPrice, $maxPrice, $categoryId = null)
    {
        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
            WHERE p.price BETWEEN :min_price AND :max_price
        ";

        $params = [
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ];

        if ($categoryId !== null && $categoryId > 0) {
            $query .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $query .= " ORDER BY p.price ASC";

        return $this->db->select($query, $params);
    }

    /**
     * Отримати найновіші товари
     */
    public function getLatest($limit = 10, $categoryId = null)
    {
        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
        ";

        $params = ['limit' => $limit];

        if ($categoryId !== null && $categoryId > 0) {
            $query .= " WHERE p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $query .= " ORDER BY p.date_added DESC LIMIT :limit";

        return $this->db->select($query, $params);
    }

    /**
     * Отримати найдешевші товари
     */
    public function getCheapest($limit = 10, $categoryId = null)
    {
        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
        ";

        $params = ['limit' => $limit];

        if ($categoryId !== null && $categoryId > 0) {
            $query .= " WHERE p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $query .= " ORDER BY p.price ASC LIMIT :limit";

        return $this->db->select($query, $params);
    }

    /**
     * Парсити параметри сортування
     */
    public function parseSortParams($sort)
    {
        switch ($sort) {
            case 'price_asc':
                return ['price', 'ASC'];
            case 'name_asc':
                return ['name', 'ASC'];
            case 'date_desc':
                return ['date_added', 'DESC'];
            default:
                return ['price', 'ASC'];
        }
    }

    /**
     * Отримати статистику товарів
     */
    public function getStats()
    {
        $query = "
            SELECT 
                COUNT(*) as total_products,
                AVG(price) as avg_price,
                MIN(price) as min_price,
                MAX(price) as max_price,
                COUNT(DISTINCT category_id) as total_categories
            FROM {$this->table}
        ";

        return $this->db->selectOne($query);
    }

    /**
     * Перевірити чи існує товар з такою назвою в категорії
     */
    public function existsByNameInCategory($name, $categoryId, $excludeId = null)
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name AND category_id = :category_id";
        $params = [
            'name' => $name,
            'category_id' => $categoryId
        ];

        if ($excludeId) {
            $query .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->selectOne($query, $params);
        return $result['count'] > 0;
    }

    public function getAllWithCategoryPaginated($categoryId = null, $sortBy = 'price', $sortOrder = 'ASC', $page = 1, $limit = 12)
    {
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
            WHERE 1 = 1
        ";

        $params = [];

        // Фільтрація за категорією
        if ($categoryId !== null && $categoryId > 0) {
            $query .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        // Сортування
        $allowedSorts = ['price', 'name', 'date_added', 'id'];
        $allowedOrders = ['ASC', 'DESC'];

        if (in_array($sortBy, $allowedSorts) && in_array($sortOrder, $allowedOrders)) {
            $query .= " ORDER BY p.{$sortBy} {$sortOrder}";
        } else {
            $query .= " ORDER BY p.price ASC";
        }

        // Пагінація
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->select($query, $params);
    }

    /**
     * Підрахувати загальну кількість товарів
     */
    public function getTotalCount($categoryId = null, $searchTerm = null)
    {
        $query = "
            SELECT COUNT(*) as total
            FROM {$this->table} p
            WHERE p.is_active = 1
        ";

        $params = [];

        // Фільтрація за категорією
        if ($categoryId !== null && $categoryId > 0) {
            $query .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        // Пошук
        if ($searchTerm) {
            $query .= " AND p.name LIKE :search";
            $params['search'] = "%{$searchTerm}%";
        }

        $result = $this->db->selectOne($query, $params);
        return (int) $result['total'];
    }

    /**
     * Пошук товарів з пагінацією
     */
    public function searchPaginated($searchTerm, $categoryId = null, $page = 1, $limit = 12)
    {
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.date_added,
                p.category_id,
                c.name as category_name
            FROM {$this->table} p
            JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.name LIKE :search
        ";

        $params = ['search' => "%{$searchTerm}%"];

        if ($categoryId !== null && $categoryId > 0) {
            $query .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $query .= " ORDER BY p.name ASC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->select($query, $params);
    }

    /**
     * Отримати інформацію про пагінацію
     */
    public function getPaginationInfo($totalItems, $currentPage, $itemsPerPage)
    {
        $totalPages = ceil($totalItems / $itemsPerPage);
        $currentPage = max(1, min($currentPage, $totalPages));

        return [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'items_per_page' => $itemsPerPage,
            'has_prev' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
            'next_page' => $currentPage < $totalPages ? $currentPage + 1 : null,
            'start_item' => ($currentPage - 1) * $itemsPerPage + 1,
            'end_item' => min($currentPage * $itemsPerPage, $totalItems)
        ];
    }
}