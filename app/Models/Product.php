<?php

namespace App\Models;

/**
 * Оновлена модель товарів з підтримкою пагінації
 */
class Product extends Model
{
    protected $table = 'products';

    /**
     * Отримати товари з пагінацією
     */
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
            WHERE p.is_active = 1
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
?>