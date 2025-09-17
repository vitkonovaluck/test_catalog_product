<?php

namespace App\Models;

/**
 * Модель категорій
 */
class Category extends Model
{
    protected $table = 'categories';

    /**
     * Отримати всі категорії з кількістю товарів
     */
    public function getAllWithProductCount()
    {
        $query = "
            SELECT 
                c.id, 
                c.name,
                COUNT(p.id) as product_count
            FROM {$this->table} c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id, c.name
            ORDER BY c.name
        ";

        return $this->db->select($query);
    }

    /**
     * Отримати категорію за ID з кількістю товарів
     */
    public function findWithProductCount($id)
    {
        $query = "
            SELECT 
                c.id, 
                c.name,
                COUNT(p.id) as product_count
            FROM {$this->table} c
            LEFT JOIN products p ON c.id = p.category_id
            WHERE c.id = :id
            GROUP BY c.id, c.name
        ";

        return $this->db->selectOne($query, ['id' => $id]);
    }

    /**
     * Перевірити чи існує категорія з такою назвою
     */
    public function existsByName($name, $excludeId = null)
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name";
        $params = ['name' => $name];

        if ($excludeId) {
            $query .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->selectOne($query, $params);
        return $result['count'] > 0;
    }

    /**
     * Отримати топ категорії за кількістю товарів
     */
    public function getTopByProductCount($limit = 5)
    {
        $query = "
            SELECT 
                c.id, 
                c.name,
                COUNT(p.id) as product_count
            FROM {$this->table} c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id, c.name
            HAVING product_count > 0
            ORDER BY product_count DESC
            LIMIT :limit
        ";

        return $this->db->select($query, ['limit' => $limit]);
    }

    /**
     * Отримати категорії без товарів
     */
    public function getEmptyCategories()
    {
        $query = "
            SELECT c.*
            FROM {$this->table} c
            LEFT JOIN products p ON c.id = p.category_id
            WHERE p.id IS NULL
            ORDER BY c.name
        ";

        return $this->db->select($query);
    }
}
?>