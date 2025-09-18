<div class="container">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/" class="text-decoration-none">Головна</a>
            </li>
            <li class="breadcrumb-item">
                <a href="/?category=<?= $product['category_id'] ?>" class="text-decoration-none">
                    <?= $this->escape($product['category_name']) ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $this->escape($product['name']) ?>
            </li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="row mb-5">
        <div class="col-md-6">
            <!-- Product Image Placeholder -->
            <div class="card">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); min-height: 400px;">
                    <div style="font-size: 8rem; color: #dee2e6; margin-bottom: 2rem;">
                        📦
                    </div>
                    <p class="text-muted">Зображення товару</p>
                    <small class="text-muted">(Демо версія - зображення не завантажуються)</small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="product-info">
                <h1 class="mb-3"><?= $this->escape($product['name']) ?></h1>

                <div class="price-section mb-4">
                    <h3 class="price mb-2"><?= $this->formatPrice($product['price']) ?></h3>
                    <p class="text-muted">Ціна вказана в гривнях з ПДВ</p>
                </div>

                <div class="product-meta mb-4">
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>📁 Категорія:</strong><br>
                               <a href="/?category=<?= $product['category_id'] ?>" class="text-decoration-none">
                                   <?= $this->escape($product['category_name']) ?>
                               </a>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>📅 Дата додавання:</strong><br>
                               <?= $this->formatDate($product['date_added']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="product-features mb-4">
                    <h5>✨ Переваги:</h5>
                    <ul class="list-unstyled">
                        <li>✅ Офіційна гарантія</li>
                        <li>🚚 Безкоштовна доставка від 500 грн</li>
                        <li>⚡ Швидка доставка 1-2 дні</li>
                        <li>🔄 Повернення протягом 14 днів</li>
                        <li>💳 Оплата при отриманні</li>
                    </ul>
                </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">📋 Опис товару</h4>
                </div>
                <div class="card-body">
                    <div class="product-description">
                        <p>Це детальний опис товару <strong><?= $this->escape($product['name']) ?></strong>.</p>

                        <h6>Основні характеристики:</h6>
                        <ul>
                            <li>Високоякісний товар з офіційної колекції</li>
                            <li>Відповідає всім стандартам якості</li>
                            <li>Рекомендований для повсякденного використання</li>
                            <li>Має сертифікати відповідності</li>
                        </ul>

                        <div class="alert alert-info mt-4">
                            <h6>ℹ️ Додаткова інформація:</h6>
                            <p class="mb-0">
                                Цей товар належить до категорії "<?= $this->escape($product['category_name']) ?>"
                                і був доданий до каталогу <?= $this->formatDate($product['date_added']) ?>.
                                Ціна може змінюватися в залежності від курсу валют та акційних пропозицій.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-4">🔗 Схожі товари</h4>
                <div class="row">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card product-card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">
                                        <a href="/product/<?= $relatedProduct['id'] ?>" class="text-decoration-none">
                                            <?= $this->escape($relatedProduct['name']) ?>
                                        </a>
                                    </h6>

                                    <p class="card-text price">
                                        <?= $this->formatPrice($relatedProduct['price']) ?>
                                    </p>

                                    <p class="card-text">
                                        <small class="text-muted">
                                            📅 <?= $this->formatDate($relatedProduct['date_added']) ?>
                                        </small>
                                    </p>

                                    <div class="mt-auto">
                                        <a href="/product/<?= $relatedProduct['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            👁️ Переглянути
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back to Catalog -->
    <div class="row">
        <div class="col-12 text-center">
            <a href="/?category=<?= $product['category_id'] ?>" class="btn btn-secondary">
                ⬅️ Повернутись до категорії "<?= $this->escape($product['category_name']) ?>"
            </a>
            <a href="/" class="btn btn-outline-secondary ms-2">
                🏠 На головну
            </a>
        </div>
    </div>
</div>

<script>
function buyProduct(productId) {
    // Демонстрація покупки
    if (confirm(`🛒 Купити товар "${<?= json_encode($product['name']) ?>}"?\n\nЦіна: ${<?= json_encode($this->formatPrice($product['price'])) ?>}`)) {
        alert(`✅ Товар додано до кошика!\n\n(Це демонстрація - реальна логіка покупки не реалізована)`);

        // Тут була би реальна логіка додавання до кошика
        // addToCart(productId);
    }
}

function addToWishlist(productId) {
    alert(`❤️ Товар додано в обране!\n\n(Це демонстрація - реальна логіка не реалізована)`);

    // Тут була би реальна логіка додавання в обране
    // addToWishlist(productId);
}

// Можна додати більше інтерактивності
document.addEventListener('DOMContentLoaded', function() {
    // Анімація появи елементів
    const elements = document.querySelectorAll('.product-card, .product-info, .card');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';

        setTimeout(() => {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>