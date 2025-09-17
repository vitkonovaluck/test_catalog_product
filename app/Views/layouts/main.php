<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $this->escape($title) . ' - ' : '' ?>Каталог товарів</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .category-item {
            cursor: pointer;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-item:hover {
            background-color: var(--light-color);
            border-color: var(--primary-color);
            transform: translateX(5px);
        }

        .category-item.active {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            border-color: var(--primary-color);
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .category-count {
            font-size: 0.85em;
            opacity: 0.8;
            font-weight: 500;
        }

        .product-card {
            margin-bottom: 25px;
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .product-card .card-body {
            padding: 1.5rem;
        }

        .product-card .card-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .price {
            color: var(--success-color);
            font-weight: 700;
            font-size: 1.25em;
            margin-bottom: 0.5rem;
        }

        .btn-buy {
            background: linear-gradient(135deg, var(--success-color), #218838);
            border: none;
            padding: 8px 20px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-buy:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .loading {
            text-align: center;
            padding: 3rem;
        }

        .loading .spinner-border {
            width: 3rem;
            height: 3rem;
            color: var(--primary-color);
        }

        .sidebar {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .sort-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 40px;
            border-radius: 25px;
        }

        .search-box .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
        }

        .breadcrumb {
            background: none;
            padding: 0;
        }

        .alert {
            border: none;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                margin-bottom: 2rem;
            }

            .category-item {
                padding: 10px;
                margin-bottom: 5px;
            }

            .product-card {
                margin-bottom: 15px;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/">
            🛍️ Каталог товарів
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Головна</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="py-4">
    <?= $content ?>
</main>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-5">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> Каталог товарів. Тестове завдання з використанням MVC архітектури.</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
    // Глобальні налаштування для Ajax запитів
    window.CatalogConfig = {
        apiUrl: '/api',
        baseUrl: '/'
    };

    // Утилітна функція для показу toast повідомлень
    function showToast(message, type = 'info') {
        // Можна додати бібліотеку для toast або використати alert
        if (type === 'error') {
            console.error(message);
        } else {
            console.log(message);
        }
    }

    // Утилітна функція для форматування ціни
    function formatPrice(price) {
        return new Intl.NumberFormat('uk-UA', {
            style: 'currency',
            currency: 'UAH',
            minimumFractionDigits: 2
        }).format(price);
    }

    // Утилітна функція для форматування дати
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('uk-UA');
    }

    // Утилітна функція для екранування HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<?php if (isset($jsFiles)): ?>
    <?php foreach ($jsFiles as $jsFile): ?>
        <script src="<?= $jsFile ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>