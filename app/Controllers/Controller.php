<?php

namespace App\Controllers;

use Core\View;

/**
 * Базовий контролер
 */
abstract class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Рендерити відповідь
     */
    protected function render($template, $data = [], $layout = 'main')
    {
        $this->view->setLayout($layout);
        $this->view->setData($data);
        echo $this->view->render($template);
    }

    /**
     * Рендерити JSON відповідь
     */
    protected function json($data, $statusCode = 200)
    {
        echo $this->view->json($data, $statusCode);
    }

    /**
     * Рендерити відповідь успіху
     */
    protected function jsonSuccess($data = null, $message = 'Success')
    {
        $response = [
            'status' => 'success',
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->json($response);
    }

    /**
     * Рендерити відповідь помилки
     */
    protected function jsonError($message, $statusCode = 400, $data = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Редирект
     */
    protected function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Отримати GET параметр
     */
    protected function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Отримати POST параметр
     */
    protected function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Отримати всі GET параметри
     */
    protected function getAllGet()
    {
        return $_GET;
    }

    /**
     * Отримати всі POST параметри
     */
    protected function getAllPost()
    {
        return $_POST;
    }

    /**
     * Валідувати обов'язкові поля
     */
    protected function validateRequired($data, $requiredFields)
    {
        $errors = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = "Field '{$field}' is required";
            }
        }

        return $errors;
    }

    /**
     * Очистити вхідні дані
     */
    protected function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }

        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Перевірити чи це Ajax запит
     */
    protected function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Встановити заголовки CORS
     */
    protected function setCorsHeaders()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }
}
?>