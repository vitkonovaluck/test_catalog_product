<?php

namespace Core;

use PDO;
use PDOException;
use Exception;

/**
 * Клас для роботи з базою даних
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $config = require_once __DIR__ . '/../config/database.php';

        try {
            $this->pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Singleton pattern для отримання єдиного екземпляру
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Отримати PDO з'єднання
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Виконати SELECT запит
     */
    public function select($query, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Виконати SELECT запит для одного рядка
     */
    public function selectOne($query, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Виконати INSERT, UPDATE, DELETE
     */
    public function execute($query, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Отримати ID останнього вставленого запису
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Почати транзакцію
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Підтвердити транзакцію
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Відкатити транзакцію
     */
    public function rollback()
    {
        return $this->pdo->rollback();
    }

    /**
     * Заборонити клонування
     */
    private function __clone() {}

    /**
     * Заборонити десеріалізацію
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>