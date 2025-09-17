<?php

namespace App\Models;

use Core\Database;

/**
 * Базова модель
 */
abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Отримати всі записи
     */
    public function all($orderBy = null, $direction = 'ASC')
    {
        $query = "SELECT * FROM {$this->table}";

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy} {$direction}";
        }

        return $this->db->select($query);
    }

    /**
     * Знайти запис за ID
     */
    public function find($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->selectOne($query, ['id' => $id]);
    }

    /**
     * Знайти записи за умовою
     */
    public function where($column, $operator, $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        return $this->db->select($query, ['value' => $value]);
    }

    /**
     * Створити новий запис
     */
    public function create($data)
    {
        $columns = array_keys($data);
        $placeholders = ':' . implode(', :', $columns);

        $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})";

        $this->db->execute($query, $data);
        return $this->db->lastInsertId();
    }

    /**
     * Оновити запис
     */
    public function update($id, $data)
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE {$this->primaryKey} = :id";

        $data['id'] = $id;
        return $this->db->execute($query, $data);
    }

    /**
     * Видалити запис
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    /**
     * Підрахувати кількість записів
     */
    public function count($where = null, $params = [])
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";

        if ($where) {
            $query .= " WHERE {$where}";
        }

        $result = $this->db->selectOne($query, $params);
        return (int) $result['count'];
    }

    /**
     * Виконати кастомний запит
     */
    public function query($query, $params = [])
    {
        return $this->db->select($query, $params);
    }

    /**
     * Виконати кастомний запит для одного рядка
     */
    public function queryOne($query, $params = [])
    {
        return $this->db->selectOne($query, $params);
    }
}
?>