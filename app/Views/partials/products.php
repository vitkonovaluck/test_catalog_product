<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <h5>📭 Товари не знайдені</h5>
        <p class="mb-0">В цій категорії поки що немає товарів або вони не відповідають критеріям пошуку.</p>
    </div>
<?php else: ?>
    <?php foreach ($products as $product): ?>
        <div class="card product-card">
            <div class="card-body">
                <h5 class="card-title"><?= $this->escape($product['name']) ?></h5>

                <p class="card-text price">
                    <?= $this->formatPrice($product['price']) ?>
                </p>

                <p class="card-text">
                    <small class="text-muted">
                        📁 Категорія: <?= $this->escape($product['category_name']) ?>
                    </small>
                </p>

                <p class="card-text">
                    <small class="text-muted">
                        📅 Дата: <?= $this->formatDate($product['date_added']) ?>
                    </small>
                </p>

                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-success btn-buy" data-product-id="<?= $product['id'] ?>">
                        🛒 Купити
                    </button>

                    <a href="/product/<?= $product['id'] ?>"
                       class="btn btn-outline-primary btn-sm"
                       target="_blank">
                        👁️ Детальніше
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Products Statistics -->
    <div class="mt-4 text-center">
        <small class="text-muted">
            Показано <?= count($products) ?> товар<?= count($products) != 1 ? 'ів' : '' ?>
        </small>
    </div>
<?php endif; ?>