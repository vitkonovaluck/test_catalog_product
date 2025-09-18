<?php
/**
 * Оновлення app/Views/catalog/index.php
 * Додати після блоку з товарами і перед закриваючим </div>
 */
?>

<!-- Products Grid -->
<div id="products" class="fade-in">
    <?= $this->partial('products', ['products' => $products]) ?>
</div>

<!-- Pagination -->
<?= $this->partial('pagination', [
        'pagination' => $pagination,
        'currentCategoryId' => $currentCategoryId,
        'currentSort' => $currentSort,
        'searchTerm' => isset($searchTerm) ? $searchTerm : null
]) ?>

<!-- Додати до JavaScript -->
<script>
    class ProductCatalog {
        constructor() {
            this.currentCategory = <?= json_encode($currentCategoryId) ?>;
            this.currentSort = <?= json_encode($currentSort) ?>;
            this.currentPage = <?= json_encode($currentPage) ?>;
            this.apiUrl = '/api';
            this.isLoading = false;

            this.init();
        }

        init() {
            this.bindEvents();
            this.initializeState();
            this.bindPaginationEvents();
        }

        /**
         * Прив'язати події пагінації
         */
        bindPaginationEvents() {
            // Ajax пагінація
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('a[data-page]');
                if (paginationLink) {
                    e.preventDefault();
                    const page = parseInt(paginationLink.getAttribute('data-page'));
                    this.goToPage(page);
                }
            });
        }

        /**
         * Перейти на сторінку
         */
        goToPage(page) {
            if (this.isLoading || page === this.currentPage) return;

            this.currentPage = page;
            this.updateUrl();
            this.loadProducts();

            // Прокрутити до початку товарів
            document.getElementById('products').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        /**
         * Оновити URL з пагінацією
         */
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

        /**
         * Завантажити товари з пагінацією
         */
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
                sort: this.currentSort,
                page