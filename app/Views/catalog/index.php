<div class="container">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger fade-in" role="alert">
            <strong>Помилка!</strong> <?= $this->escape($error) ?>
        </div>
    <?php endif; ?>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/" class="text-decoration-none">Головна</a>
            </li>
            <?php if ($currentCategory): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= $this->escape($currentCategory['name']) ?>
                </li>
            <?php endif; ?>
            <?php if (isset($searchTerm)): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    Пошук: "<?= $this->escape($searchTerm) ?>"
                </li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <form method="GET" action="/search" class="search-box">
                <input type="text"
                       class="form-control form-control-lg"
                       name="q"
                       placeholder="Пошук товарів..."
                       value="<?= isset($searchTerm) ? $this->escape($searchTerm) : '' ?>">
                <?php if ($currentCategoryId): ?>
                    <input type="hidden" name="category" value="<?= $currentCategoryId ?>">
                <?php endif; ?>
                <button type="submit" class="search-btn">
                    🔍
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar - Categories -->
        <div class="col-md-3">
            <div class="sidebar">
                <h4 class="mb-4">
                    Категорії
                    <span class="badge bg-primary ms-2"><?= count($categories) ?></span>
                </h4>

                <div id="categories">
                    <!-- All Products Category -->
                    <div class="category-item <?= $currentCategoryId === null ? 'active' : '' ?>"
                         data-category="all">
                        <span>Всі товари</span>
                        <span class="category-count"><?= $totalProducts ?></span>
                    </div>

                    <!-- Individual Categories -->
                    <?php foreach ($categories as $category): ?>
                        <div class="category-item <?= $currentCategoryId === $category['id'] ? 'active' : '' ?>"
                             data-category="<?= $category['id'] ?>">
                            <span><?= $this->escape($category['name']) ?></span>
                            <span class="category-count"><?= $category['product_count'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Category Statistics -->
                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted">
                        <strong>Статистика:</strong><br>
                        Категорій: <?= count($categories) ?><br>
                        Товарів: <?= $totalProducts ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Main Content - Products -->
        <div class="col-md-9">
            <!-- Sort and Filter Controls -->
            <div class="sort-container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <?php if ($currentCategory): ?>
                                <?= $this->escape($currentCategory['name']) ?>
                            <?php elseif (isset($searchTerm)): ?>
                                Результати пошуку: "<?= $this->escape($searchTerm) ?>"
                            <?php else: ?>
                                Всі товари
                            <?php endif ?>
                            <span class="badge bg-secondary ms-2"><?= count($products) ?></span>
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <label for="sortSelect" class="form-label me-2 mb-0">
                                🔽 Сортування:
                            </label>
                            <select id="sortSelect" class="form-select" style="width: auto;">
                                <option value="price_asc" <?= $currentSort === 'price_asc' ? 'selected' : '' ?>>
                                    Спочатку дешевші
                                </option>
                                <option value="name_asc" <?= $currentSort === 'name_asc' ? 'selected' : '' ?>>
                                    По алфавіту
                                </option>
                                <option value="date_desc" <?= $currentSort === 'date_desc' ? 'selected' : '' ?>>
                                    Спочатку нові
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products" class="fade-in">
                <?= $this->partial('products', ['products' => $products]) ?>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">🛍️ Деталі товару</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productModalBody">
                <div class="loading">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Завантаження...</span>
                    </div>
                    <p class="mt-3">Завантаження інформації про товар...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    ❌ Закрити
                </button>
                <button type="button" class="btn btn-success btn-buy-now">
                    💰 Купити зараз
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * MVC Product Catalog Class
     */
    class ProductCatalog {
        constructor() {
            this.currentCategory = <?= json_encode($currentCategoryId) ?>;
            this.currentSort = <?= json_encode($currentSort) ?>;
            this.apiUrl = '/api';
            this.isLoading = false;

            this.init();
        }

        init() {
            this.bindEvents();
            this.initializeState();
        }

        initializeState() {
            // Встановити активний стан категорій та сортування
            this.updateActiveStates();
        }

        updateActiveStates() {
            // Оновити активну категорію
            document.querySelectorAll('.category-item').forEach(item => {
                item.classList.remove('active');
                const category = item.getAttribute('data-category');

                if ((category === 'all' && this.currentCategory === null) ||
                    (parseInt(category) === this.currentCategory)) {
                    item.classList.add('active');
                }
            });

            // Оновити сортування
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.value = this.currentSort;
            }
        }

        bindEvents() {
            // Клік по категорії
            const categoriesContainer = document.getElementById('categories');
            if (categoriesContainer) {
                categoriesContainer.addEventListener('click', (e) => {
                    const categoryItem = e.target.closest('.category-item');
                    if (categoryItem) {
                        const category = categoryItem.getAttribute('data-category');
                        this.selectCategory(category);
                    }
                });
            }

            // Зміна сортування
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.addEventListener('change', (e) => {
                    this.changeSort(e.target.value);
                });
            }

            // Кнопки "Купити"
            const productsContainer = document.getElementById('products');
            if (productsContainer) {
                productsContainer.addEventListener('click', (e) => {
                    if (e.target.classList.contains('btn-buy')) {
                        const productId = parseInt(e.target.getAttribute('data-product-id'));
                        this.showProductModal(productId);
                    }
                });
            }

            // Кнопка "Купити зараз" в модалці
            const buyNowBtn = document.querySelector('.btn-buy-now');
            if (buyNowBtn) {
                buyNowBtn.addEventListener('click', () => {
                    this.buyNow();
                });
            }
        }

        selectCategory(category) {
            if (this.isLoading) return;

            this.currentCategory = category === 'all' ? null : parseInt(category);
            this.updateUrl();
            this.loadProducts();
        }

        changeSort(sort) {
            if (this.isLoading) return;

            this.currentSort = sort;
            this.updateUrl();
            this.loadProducts();
        }

        updateUrl() {
            const params = new URLSearchParams();

            if (this.currentCategory !== null) {
                params.set('category', this.currentCategory);
            }

            params.set('sort', this.currentSort);

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);
        }

        loadProducts() {
            if (this.isLoading) return;

            this.isLoading = true;
            const productsContainer = document.getElementById('products');

            // Показати завантаження
            productsContainer.innerHTML = `
            <div class="loading">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Завантаження...</span>
                </div>
                <p class="mt-3">Завантаження товарів...</p>
            </div>
        `;

            // Підготувати параметри запиту
            const params = new URLSearchParams({
                sort: this.currentSort
            });

            if (this.currentCategory !== null) {
                params.set('category', this.currentCategory);
            }

            // Виконати Ajax запит
            fetch(`${this.apiUrl}/products?${params}`)
                .then(response => response.json())
                .then(data => {
                    this.isLoading = false;

                    if (data.status === 'success') {
                        this.renderProducts(data.data.products || data.data);
                        this.updateActiveStates();
                    } else {
                        this.showError(data.message || 'Помилка завантаження товарів');
                    }
                })
                .catch(error => {
                    this.isLoading = false;
                    console.error('Ajax Error:', error);
                    this.showError('Помилка мережі при завантаженні товарів');
                });
        }

        renderProducts(products) {
            const productsContainer = document.getElementById('products');

            if (!products || products.length === 0) {
                productsContainer.innerHTML = `
                <div class="alert alert-info">
                    <h5>📭 Товари не знайдені</h5>
                    <p class="mb-0">В цій категорії поки що немає товарів.</p>
                </div>
            `;
                return;
            }

            let html = '';
            products.forEach(product => {
                const price = parseFloat(product.price);
                const date = formatDate(product.date_added);

                html += `
                <div class="card product-card">
                    <div class="card-body">
                        <h5 class="card-title">${escapeHtml(product.name)}</h5>
                        <p class="card-text price">${formatPrice(price)}</p>
                        <p class="card-text">
                            <small class="text-muted">
                                📁 Категорія: ${escapeHtml(product.category_name)}
                            </small>
                        </p>
                        <p class="card-text">
                            <small class="text-muted">📅 Дата: ${date}</small>
                        </p>
                        <button class="btn btn-success btn-buy" data-product-id="${product.id}">
                            🛒 Купити
                        </button>
                    </div>
                </div>
            `;
            });

            productsContainer.innerHTML = html;
            productsContainer.classList.add('fade-in');
        }

        showProductModal(productId) {
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            const modalBody = document.getElementById('productModalBody');
            const modalTitle = document.getElementById('productModalLabel');

            // Показати завантаження в модалці
            modalTitle.textContent = '🛍️ Завантаження...';
            modalBody.innerHTML = `
            <div class="loading">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Завантаження...</span>
                </div>
                <p class="mt-3">Завантаження інформації про товар...</p>
            </div>
        `;

            modal.show();

            // Завантажити дані товару
            fetch(`${this.apiUrl}/product?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const product = data.data;
                        this.renderProductModal(product);
                    } else {
                        modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            ❌ Помилка: ${data.message}
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Ajax Error:', error);
                    modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        ❌ Помилка завантаження товару
                    </div>
                `;
                });
        }

        renderProductModal(product) {
            const modalTitle = document.getElementById('productModalLabel');
            const modalBody = document.getElementById('productModalBody');

            modalTitle.textContent = `🛍️ ${product.name}`;

            const price = parseFloat(product.price);
            const date = formatDate(product.date_added);

            modalBody.innerHTML = `
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3">${escapeHtml(product.name)}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>💰 Ціна:</strong> <span class="price fs-4">${formatPrice(price)}</span></p>
                            <p><strong>📁 Категорія:</strong> ${escapeHtml(product.category_name)}</p>
                            <p><strong>📅 Дата додавання:</strong> ${date}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6>ℹ️ Інформація про доставку</h6>
                                <ul class="mb-0">
                                    <li>🚚 Безкоштовна доставка від 500 грн</li>
                                    <li>⚡ Швидка доставка 1-2 дні</li>
                                    <li>🔄 Повернення протягом 14 днів</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

            // Зберегти ID товару для покупки
            this.currentProductId = product.id;
        }

        buyNow() {
            if (this.currentProductId) {
                // Тут буде логіка покупки
                alert(`🛒 Товар ID ${this.currentProductId} додано до кошика!\n\n(Це демонстрація - реальна логіка покупки не реалізована)`);

                // Закрити модалку
                const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
                if (modal) {
                    modal.hide();
                }
            }
        }

        showError(message) {
            const productsContainer = document.getElementById('products');
            productsContainer.innerHTML = `
            <div class="alert alert-danger">
                <strong>❌ Помилка!</strong> ${escapeHtml(message)}
            </div>
        `;
        }
    }

    // Ініціалізація після завантаження DOM
    document.addEventListener('DOMContentLoaded', () => {
        new ProductCatalog();
    });

    // Обробка кнопки "Назад" браузера
    window.addEventListener('popstate', () => {
        location.reload();
    });
</script>