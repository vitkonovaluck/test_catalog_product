<?php

namespace Core;

/**
 * Клас для роботи з шаблонами (View)
 */
class View
{
    private $data = [];
    private $layout = 'main';

    /**
     * Встановити змінні для шаблону
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Встановити масив змінних
     */
    public function setData($data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Встановити layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Рендерити шаблон
     */
    public function render($template)
    {
        // Витягнути змінні в локальну область видимості
        extract($this->data);

        // Почати буферизацію виводу
        ob_start();

        // Підключити файл шаблону
        $templatePath = $this->getTemplatePath($template);
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            throw new \Exception("Template not found: {$template}");
        }

        // Отримати контент шаблону
        $content = ob_get_clean();

        // Якщо потрібен layout
        if ($this->layout) {
            $layoutPath = $this->getLayoutPath($this->layout);
            if (file_exists($layoutPath)) {
                ob_start();
                include $layoutPath;
                return ob_get_clean();
            } else {
                throw new \Exception("Layout not found: {$this->layout}");
            }
        }

        return $content;
    }

    /**
     * Рендерити частковий шаблон
     */
    public function partial($template, $data = [])
    {
        // Злити дані з поточними
        $currentData = $this->data;
        $this->data = array_merge($this->data, $data);

        // Витягнути змінні
        extract($this->data);

        // Рендерити частковий шаблон
        $templatePath = $this->getPartialPath($template);
        if (file_exists($templatePath)) {
            ob_start();
            include $templatePath;
            $result = ob_get_clean();
        } else {
            throw new \Exception("Partial template not found: {$template}");
        }

        // Відновити попередні дані
        $this->data = $currentData;

        return $result;
    }

    /**
     * Рендерити JSON відповідь
     */
    public function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Отримати шлях до шаблону
     */
    private function getTemplatePath($template)
    {
        return __DIR__ . "/../app/Views/{$template}.php";
    }

    /**
     * Отримати шлях до layout
     */
    private function getLayoutPath($layout)
    {
        return __DIR__ . "/../app/Views/layouts/{$layout}.php";
    }

    /**
     * Отримати шлях до часткового шаблону
     */
    private function getPartialPath($template)
    {
        return __DIR__ . "/../app/Views/partials/{$template}.php";
    }

    /**
     * Екранувати HTML
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Форматувати ціну
     */
    public function formatPrice($price)
    {
        return number_format($price, 2, ',', ' ') . ' грн';
    }

    /**
     * Форматувати дату
     */
    public function formatDate($date)
    {
        return date('d.m.Y', strtotime($date));
    }

    /**
     * Генерувати URL
     */
    public function url($path = '', $params = [])
    {
        $url = rtrim($_SERVER['REQUEST_URI'], '/') . '/' . ltrim($path, '/');

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}
?>