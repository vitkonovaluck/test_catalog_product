<div class="container">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger fade-in" role="alert">
            <strong>❌ Помилка!</strong> <?= $this->escape($error) ?>
        </div>
    <?php endif; ?>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/" class="text-decoration-none">🏠 Головна</a>
            </li>
            <?php if ($currentCategory): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    📁 <?= $this->escape($currentCategory['name']) ?>
                </li>
            <?php endif; ?>
            <?php if (isset($searchTerm) && $searchTerm): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    🔍 Пошук: "<?= $this->escape($searchTerm) ?>"
                </li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- Page Header with Statistics -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">
                🛍️ Каталог товарів
                <?php if ($currentCategory): ?>
                    - <?= $this->escape($currentCategory['name']) ?>
                <?php endif; ?>
            </h1>
            <p class="text-muted mb-0">
                <?php if (isset($searchTerm) && $searchTerm): ?>
                    Результати пошуку для "<?= $this->escape($searchTerm) ?>"
                <?php else: ?>
                    Знайдіть найкращі товари за вигідними цінами
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-inline-flex align-items-center bg-light rounded-pill px-3 py-2">
                <span class="text-muted me-2">📊 Товарів:</span>
                <span class="badge bg-primary fs-6">
                    <?= isset($pagination) && $pagination ? $pagination['total_items'] : count($products) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <form method="GET" action="/search" class="search-container">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-primary text-white">
                        🔍
                    </span>
                    <input type="text"
                           class="form-control"
                           name="q"
                           placeholder="Пошук товарів за назвою..."
                           value="<?= isset($searchTerm) ? $this->escape($searchTerm) : '' ?>"
                           autocomplete="off"
                           id="searchInput">
                    <?php if ($currentCategoryId): ?>
                        <input type="hidden" name="category" value="<?= $currentCategoryId ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary px-4">
                        Знайти
                    </button>
                </div>
            </form>

            <!-- Quick Search Results -->
            <div id="quickSearchResults" class="mt-2" style="display: none;">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-2" id="quickSearchContent">
                        <!-- Ajax results -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar - Categories -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="sidebar sticky-top">
                <!-- Categories Section -->
                <div class="mb-4">
                    <h4 class="mb-3 d-flex align-items-center">
                        Категорії
                        <span class="badge bg-secondary ms-2"><?= count($categories) ?></span>
                    </h4>

                    <div id="categories">
                        <!-- All Products Category -->
                        <div class="category-item <?= $currentCategoryId === null ? 'active' : '' ?>"
                             data-category="all">
                            <div class="d-flex align-items-center">
                                <span class="flex-grow-1">Всі товари</span>
                                <span class="category-count"><?= $totalProducts ?></span>
                            </div>
                        </div>

                        <!-- Individual Categories -->
                        <?php foreach ($categories as $category): ?>
                            <div class="category-item <?= $currentCategoryId === $category['id'] ? 'active' : '' ?>"
                                 data-category="<?= $category['id'] ?>">
                                <div class="d-flex align-items-center">
                                    <span class="flex-grow-1"><?= $this->escape($category['name']) ?></span>
                                    <span class="category-count"><?= $category['product_count'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Products -->
        <div class="col-lg-9 col-md-8">
            <!-- Control Panel -->
            <div class="control-panel mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <!-- Results Info -->
                            <div class="col-md-6 mb-2 mb-md-0">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <?php if ($currentCategory): ?>
                                        <?= $this->escape($currentCategory['name']) ?>
                                    <?php elseif (isset($searchTerm) && $searchTerm): ?>
                                        Результати пошуку
                                    <?php else: ?>
                                        Всі товари
                                    <?php endif ?>

                                    <span class="badge bg-primary ms-2">
                                        <?= isset($pagination) ? $pagination['total_items'] : count($products) ?>
                                    </span>
                                </h5>
                                <?php if (isset($pagination)): ?>
                                    <small class="text-muted">
                                        Сторінка <?= $pagination['current_page'] ?> з <?= $pagination['total_pages'] ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <!-- Sort Controls -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-md-end">
                                    <label for="sortSelect" class="form-label me-2 mb-0 text-nowrap">
                                        Сортування:
                                    </label>
                                    <select id="sortSelect" class="form-select" style="max-width: 200px;">
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
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products" class="products-grid">
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <div class="text-center py-5">
                            <div style="font-size: 4rem; opacity: 0.5;">📭</div>
                            <h4 class="mt-3">Товари не знайдені</h4>
                            <p class="text-muted mb-4">
                                <?php if (isset($searchTerm) && $searchTerm): ?>
                                    За запитом "<?= $this->escape($searchTerm) ?>" нічого не знайдено.
                                <?php elseif ($currentCategory): ?>
                                    В категорії "<?= $this->escape($currentCategory['name']) ?>" поки немає товарів.
                                <?php else: ?>
                                    В каталозі поки немає товарів.
                                <?php endif; ?>
                            </p>
                            <?php if (isset($searchTerm) && $searchTerm): ?>
                                <a href="/" class="btn btn-primary">
                                    🔍 Переглянути всі товари
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                <div class="card product-card h-100 border-0 shadow-sm">
                                    <!-- Product Image Placeholder -->
                                    <div class="product-image-placeholder">
                                        <div class="d-flex align-items-center justify-content-center"
                                             style="height: 200px; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                                            <span style="font-size: 3rem; opacity: 0.6;">📦</span>
                                        </div>
                                        <!-- Product Badge -->
                                        <?php if (strtotime($product['date_added']) > strtotime('-7 days')): ?>
                                            <div class="product-badge">
                                                <span class="badge bg-success position-absolute" style="top: 10px; right: 10px;">
                                                    🆕 Новинка
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <!-- Product Title -->
                                        <h5 class="card-title product-title">
                                            <a href="/product/<?= $product['id'] ?>"
                                               class="text-decoration-none text-dark stretched-link">
                                                <?= $this->escape($product['name']) ?>
                                            </a>
                                        </h5>

                                        <!-- Price -->
                                        <div class="price-section mb-2">
                                            <div class="product-price">
                                                <?= $this->formatPrice($product['price']) ?>
                                            </div>
                                            <!-- Price comparison could be added here -->
                                        </div>

                                        <!-- Product Meta -->
                                        <div class="product-meta mb-3">
                                            <div class="d-flex align-items-center text-muted small mb-1">
                                                <span class="me-2">📁</span>
                                                <span><?= $this->escape($product['category_name']) ?></span>
                                            </div>
                                            <div class="d-flex align-items-center text-muted small">
                                                <span class="me-2">📅</span>
                                                <span><?= $this->formatDate($product['date_added']) ?></span>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="mt-auto">
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success btn-buy position-relative"
                                                        data-product-id="<?= $product['id'] ?>"
                                                        style="z-index: 10;">
                                                    🛒 Купити
                                                </button>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-primary btn-sm flex-fill position-relative"
                                                            onclick="addToWishlist(<?= $product['id'] ?>)"
                                                            style="z-index: 10;">
                                                        ❤️ В обране
                                                    </button>
                                                    <button class="btn btn-outline-info btn-sm flex-fill position-relative"
                                                            onclick="compareProduct(<?= $product['id'] ?>)"
                                                            style="z-index: 10;">
                                                        📊 Порівняти
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($pagination) && $pagination && $pagination['total_pages'] > 1): ?>
                <?= $this->partial('pagination', [
                        'pagination' => $pagination,
                        'currentCategoryId' => $currentCategoryId,
                        'currentSort' => $currentSort,
                        'searchTerm' => isset($searchTerm) ? $searchTerm : null
                ]) ?>
            <?php endif; ?>

            <!-- Load More Button (Alternative to pagination) -->
            <?php if (isset($pagination) && $pagination && $pagination['has_next']): ?>
                <div class="text-center mt-4 d-none" id="loadMoreContainer">
                    <button class="btn btn-outline-primary btn-lg" id="loadMoreBtn">
                        ⬇️ Завантажити ще товари
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none"
     style="background: rgba(255,255,255,0.8); z-index: 9999;">
    <div class="d-flex align-items-center justify-content-center h-100">
        <div class="text-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Завантаження...</span>
            </div>
            <p class="mt-3 h5">Завантаження товарів...</p>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="productModalLabel">
                    🛍️ Деталі товару
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="productModalBody">
                <div class="loading-content text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Завантаження...</span>
                    </div>
                    <p class="text-muted">Завантаження інформації про товар...</p>
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

<!-- Custom Styles for this page -->
<style>
    .sidebar {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        top: 2rem;
    }

    .category-item {
        cursor: pointer;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .category-item:hover {
        background-color: #f8f9fa;
        border-color: #007bff;
        transform: translateX(5px);
    }

    .category-item.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border-color: #007bff;
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }

    .category-count {
        font-size: 0.85em;
        opacity: 0.8;
        font-weight: 600;
        background: rgba(255,255,255,0.2);
        padding: 2px 8px;
        border-radius: 12px;
        min-width: 24px;
        text-align: center;
    }

    .product-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        line-height: 1.4;
        height: 2.8rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-price {
        color: #28a745;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .control-panel {
        position: sticky;
        top: 1rem;
        z-index: 100;
    }

    .search-container .input-group-text {
        border-right: none;
    }

    .search-container .form-control {
        border-left: none;
        border-right: none;
    }

    .search-container .btn {
        border-left: none;
    }

    .btn-buy {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-buy:hover {
        background: linear-gradient(135deg, #20c997, #17a2b8);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }

    .price-filter input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .no-products {
        background: #f8f9fa;
        border-radius: 15px;
        margin: 2rem 0;
    }

    .products-grid {
        min-height: 400px;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: static;
            margin-bottom: 2rem;
        }

        .product-card {
            margin-bottom: 1.5rem;
        }

        .control-panel {
            position: static;
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .loading-pulse {
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>

<script>
    /**
     * Enhanced Product Catalog Class with Pagination
     */
    class ProductCatalog {
        constructor() {
            this.currentCategory = <?= json_encode($currentCategoryId) ?>;
            this.currentSort = <?= json_encode($currentSort) ?>;
            this.currentPage = <?= json_encode(isset($currentPage) ? $currentPage : 1) ?>;
            this.currentProductId = null;
            this.apiUrl = '/api';
            this.isLoading = false;
            this.searchTimeout = null;

            this.init();
        }

        init() {
            this.bindEvents();
            this.initializeState();
            this.initQuickSearch();
        }

        initializeState() {
            this.updateActiveStates();
        }

        updateActiveStates() {
            // Update active category
            document.querySelectorAll('.category-item').forEach(item => {
                item.classList.remove('active');
                const category = item.getAttribute('data-category');

                if ((category === 'all' && this.currentCategory === null) ||
                    (parseInt(category) === this.currentCategory)) {
                    item.classList.add('active');
                }
            });

            // Update sort select
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.value = this.currentSort;
            }
        }

        bindEvents() {
            // Category selection
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

            // Sort change
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.addEventListener('change', (e) => {
                    this.changeSort(e.target.value);
                });
            }

            // Product buy buttons
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-buy')) {
                    e.preventDefault();
                    e.stopPropagation();
                    const productId = parseInt(e.target.getAttribute('data-product-id'));
                    this.showProductModal(productId);
                }
            });

            // Buy now button in modal
            const buyNowBtn = document.querySelector('.btn-buy-now');
            if (buyNowBtn) {
                buyNowBtn.addEventListener('click', () => {
                    this.buyNow();
                });
            }

            // Pagination links
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('a[data-page]');
                if (paginationLink) {
                    e.preventDefault();
                    const page = parseInt(paginationLink.getAttribute('data-page'));
                    this.goToPage(page);
                }
            });

            // Load more button
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', () => {
                    this.loadMoreProducts();
                });
            }
        }

        initQuickSearch() {
            const searchInput = document.getElementById('searchInput');
            const quickResults = document.getElementById('quickSearchResults');

            if (searchInput && quickResults) {
                searchInput.addEventListener('input', (e) => {
                    const term = e.target.value.trim();

                    clearTimeout(this.searchTimeout);

                    if (term.length >= 2) {
                        this.searchTimeout = setTimeout(() => {
                            this.performQuickSearch(term);
                        }, 300);
                    } else {
                        quickResults.style.display = 'none';
                    }
                });

                // Hide results when clicking outside
                document.addEventListener('click', (e) => {
                    if (!searchInput.contains(e.target) && !quickResults.contains(e.target)) {
                        quickResults.style.display = 'none';
                    }
                });
            }
        }

        performQuickSearch(term) {
            const quickResults = document.getElementById('quickSearchResults');
            const quickContent = document.getElementById('quickSearchContent');

            fetch(`${this.apiUrl}/quick-search?q=${encodeURIComponent(term)}&limit=5`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.data.length > 0) {
                        let html = '<div class="small text-muted mb-2">Швидкі результати:</div>';

                        data.data.forEach(product => {
                            html += `
                            <div class="d-flex align-items-center p-2 border-bottom">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${this.escapeHtml(product.name)}</div>
                                    <div class="text-muted small">${this.formatPrice(product.price)} - ${this.escapeHtml(product.category_name)}</div>
                                </div>
                                <a href="/product/${product.id}" class="btn btn-sm btn-outline-primary">
                                    Переглянути
                                </a>
                            </div>
                        `;
                        });

                        quickContent.innerHTML = html;
                        quickResults.style.display = 'block';
                    } else {
                        quickResults.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Quick search error:', error);
                    quickResults.style.display = 'none';
                });
        }

        selectCategory(category) {
            if (this.isLoading) return;

            this.currentCategory = category === 'all' ? null : parseInt(category);
            this.currentPage = 1; // Reset to first page
            this.updateUrl();
            this.loadProducts();
        }

        changeSort(sort) {
            if (this.isLoading) return;

            this.currentSort = sort;
            this.currentPage = 1; // Reset to first page
            this.updateUrl();
            this.loadProducts();
        }

        goToPage(page) {
            if (this.isLoading || page === this.currentPage) return;

            this.currentPage = page;
            this.updateUrl();
            this.loadProducts();

            // Smooth scroll to products
            document.getElementById('products').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        updateUrl() {
            const params = new URLSearchParams();

            if (this.currentCategory !== null) {
                params.set('category', this.currentCategory);
            }

            params.set('sort', this.currentSort);

            if (this.currentPage > 1) {
                params.set('page', this.currentPage);
            }

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);
        }

        showLoadingOverlay(show = true) {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.toggle('d-none', !show);
            }
        }

        loadProducts() {
            if (this.isLoading) return;

            this.isLoading = true;
            this.showLoadingOverlay(true);

            // Prepare request parameters
            const params = new URLSearchParams({
                sort: this.currentSort,
                page: this.currentPage,
                limit: 12
            });

            if (this.currentCategory !== null) {
                params.set('category', this.currentCategory);
            }

            // Make Ajax request
            fetch(`${this.apiUrl}/products?${params}`)
                .then(response => response.json())
                .then(data => {
                    this.isLoading = false;
                    this.showLoadingOverlay(false);

                    if (data.status === 'success') {
                        this.renderProducts(data.data.products || data.data);
                        if (data.data.pagination) {
                            this.renderPagination(data.data.pagination);
                        }
                        this.updateActiveStates();
                    } else {
                        this.showError(data.message || 'Помилка завантаження товарів');
                    }
                })
                .catch(error => {
                    this.isLoading = false;
                    this.showLoadingOverlay(false);
                    console.error('Ajax Error:', error);
                    this.showError('Помилка мережі при завантаженні товарів');
                });
        }

        renderProducts(products) {
            const productsContainer = document.getElementById('products');

            if (!products || products.length === 0) {
                productsContainer.innerHTML = `
                <div class="no-products">
                    <div class="text-center py-5">
                        <div style="font-size: 4rem; opacity: 0.5;">📭</div>
                        <h4 class="mt-3">Товари не знайдені</h4>
                        <p class="text-muted mb-4">
                            Спробуйте змінити фільтри або категорію.
                        </p>
                        <button class="btn btn-primary" onclick="location.href='/'">
                            🔍 Переглянути всі товари
                        </button>
                    </div>
                </div>
            `;
                return;
            }

            let html = '<div class="row">';
            products.forEach(product => {
                const price = parseFloat(product.price);
                const date = this.formatDate(product.date_added);
                const isNew = new Date(product.date_added) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);

                html += `
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <!-- Product Image Placeholder -->
                        <div class="product-image-placeholder">
                            <div class="d-flex align-items-center justify-content-center"
                                 style="height: 200px; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                                <span style="font-size: 3rem; opacity: 0.6;">📦</span>
                            </div>
                            ${isNew ? `
                                <div class="product-badge">
                                    <span class="badge bg-success position-absolute" style="top: 10px; right: 10px;">
                                        🆕 Новинка
                                    </span>
                                </div>
                            ` : ''}
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Product Title -->
                            <h5 class="card-title product-title">
                                <a href="/product/${product.id}"
                                   class="text-decoration-none text-dark stretched-link">
                                    ${this.escapeHtml(product.name)}
                                </a>
                            </h5>

                            <!-- Price -->
                            <div class="price-section mb-2">
                                <div class="product-price">
                                    ${this.formatPrice(price)}
                                </div>
                            </div>

                            <!-- Product Meta -->
                            <div class="product-meta mb-3">
                                <div class="d-flex align-items-center text-muted small mb-1">
                                    <span class="me-2">📁</span>
                                    <span>${this.escapeHtml(product.category_name)}</span>
                                </div>
                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-2">📅</span>
                                    <span>${date}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success btn-buy position-relative"
                                            data-product-id="${product.id}"
                                            style="z-index: 10;">
                                        🛒 Купити
                                    </button>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm flex-fill position-relative"
                                                onclick="addToWishlist(${product.id})"
                                                style="z-index: 10;">
                                            ❤️ В обране
                                        </button>
                                        <button class="btn btn-outline-info btn-sm flex-fill position-relative"
                                                onclick="compareProduct(${product.id})"
                                                style="z-index: 10;">
                                            📊 Порівняти
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            });
            html += '</div>';

            productsContainer.innerHTML = html;
            productsContainer.classList.add('fade-in');
        }

        renderPagination(pagination) {
            // Remove existing pagination
            const existingPagination = document.querySelector('.pagination-container');
            if (existingPagination) {
                existingPagination.remove();
            }

            if (!pagination || pagination.total_pages <= 1) {
                return;
            }

            const current = pagination.current_page;
            const total = pagination.total_pages;
            const hasNext = pagination.has_next;
            const hasPrev = pagination.has_prev;

            // Calculate page range
            const range = 2;
            const start = Math.max(1, current - range);
            const end = Math.min(total, current + range);

            let paginationHtml = `
            <nav aria-label="Навігація по сторінках" class="mt-4 pagination-container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            📄 Показано ${pagination.start_item}-${pagination.end_item}
                            з ${pagination.total_items} товар${pagination.total_items != 1 ? 'ів' : ''}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <ul class="pagination justify-content-md-end justify-content-center mb-0">
        `;

            // First page button
            if (current > 1) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1" title="Перша сторінка">⏮️</a>
                </li>
            `;
            }

            // Previous button
            if (hasPrev) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.prev_page}" title="Попередня сторінка">
                        ⬅️ Попередня
                    </a>
                </li>
            `;
            }

            // Page numbers
            for (let i = start; i <= end; i++) {
                if (i === current) {
                    paginationHtml += `
                    <li class="page-item active">
                        <span class="page-link bg-primary text-white">${i}</span>
                    </li>
                `;
                } else {
                    paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
                }
            }

            // Ellipsis and last page
            if (end < total) {
                if (end < total - 1) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${total}" title="Остання сторінка">${total}</a>
                </li>
            `;
            }

            // Next button
            if (hasNext) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.next_page}" title="Наступна сторінка">
                        Наступна ➡️
                    </a>
                </li>
            `;
            }

            // Last page button
            if (current < total) {
                paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${total}" title="Остання сторінка">⏭️</a>
                </li>
            `;
            }

            paginationHtml += `
                        </ul>
                    </div>
                </div>
            </nav>
        `;

            // Add pagination after products
            document.getElementById('products').insertAdjacentHTML('afterend', paginationHtml);
        }

        showProductModal(productId) {
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            const modalBody = document.getElementById('productModalBody');
            const modalTitle = document.getElementById('productModalLabel');

            // Show loading in modal
            modalTitle.textContent = '🛍️ Завантаження...';
            modalBody.innerHTML = `
            <div class="loading-content text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Завантаження...</span>
                </div>
                <p class="text-muted">Завантаження інформації про товар...</p>
            </div>
        `;

            modal.show();

            // Load product data
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
            const date = this.formatDate(product.date_added);

            modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <div class="border rounded p-4" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                            <div style="font-size: 4rem; opacity: 0.6;">📦</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <h4 class="mb-3">${this.escapeHtml(product.name)}</h4>
                    <div class="row mb-3">
                        <div class="col-6">
                            <p><strong>💰 Ціна:</strong></p>
                            <div class="fs-4 text-success fw-bold">${this.formatPrice(price)}</div>
                        </div>
                        <div class="col-6">
                            <p><strong>📁 Категорія:</strong></p>
                            <span class="badge bg-primary">${this.escapeHtml(product.category_name)}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <p><strong>📅 Дата:</strong></p>
                            <span>${date}</span>
                        </div>
                        <div class="col-6">
                            <p><strong>🆔 Артикул:</strong></p>
                            <code>#${product.id}</code>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6>ℹ️ Інформація про доставку</h6>
                        <ul class="mb-0">
                            <li>🚚 Безкоштовна доставка від 500 грн</li>
                            <li>⚡ Швидка доставка 1-2 дні</li>
                            <li>🔄 Повернення протягом 14 днів</li>
                            <li>💳 Оплата при отриманні</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;

            // Store current product ID for purchase
            this.currentProductId = product.id;
        }

        buyNow() {
            if (this.currentProductId) {
                // Demo purchase logic
                const product = document.getElementById('productModalLabel').textContent.replace('🛍️ ', '');

                if (confirm(`🛒 Купити товар "${product}"?\n\nЦе демонстрація - реальне замовлення не буде створене.`)) {
                    alert(`✅ Товар додано до кошика!\n\n(Демо режим - реальна покупка не здійснена)`);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // Here you would implement real purchase logic
                    // this.addToCart(this.currentProductId);
                }
            }
        }

        showError(message) {
            const productsContainer = document.getElementById('products');
            productsContainer.innerHTML = `
            <div class="alert alert-danger">
                <strong>❌ Помилка!</strong> ${this.escapeHtml(message)}
            </div>
        `;
        }

        // Utility functions
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        formatPrice(price) {
            return new Intl.NumberFormat('uk-UA', {
                style: 'currency',
                currency: 'UAH',
                minimumFractionDigits: 2
            }).format(price);
        }

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('uk-UA');
        }
    }



    // Initialize catalog when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        new ProductCatalog();
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', () => {
        location.reload();
    });
</script>