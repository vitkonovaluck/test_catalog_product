<?php

if (!isset($pagination) || !$pagination || $pagination['total_pages'] <= 1) {
    return; // Не показувати пагінацію якщо сторінка одна або менше
}

$current = $pagination['current_page'];
$total = $pagination['total_pages'];
$hasNext = $pagination['has_next'];
$hasPrev = $pagination['has_prev'];

// Параметри для URL
$urlParams = [];
if (isset($currentCategoryId) && $currentCategoryId) {
    $urlParams['category'] = $currentCategoryId;
}
if (isset($currentSort)) {
    $urlParams['sort'] = $currentSort;
}
if (isset($searchTerm)) {
    $urlParams['q'] = $searchTerm;
}

// Функція для генерації URL з пагінацією
function buildPaginationUrl($page, $params) {
    $params['page'] = $page;
    return '/?' . http_build_query($params);
}

// Обчислити діапазон сторінок для показу
$range = 2; // Скільки сторінок показувати з кожного боку від поточної
$start = max(1, $current - $range);
$end = min($total, $current + $range);

?>

<nav aria-label="Навігація по сторінках" class="mt-4">
    <div class="row align-items-center">
        <!-- Інформація про результати -->
        <div class="col-md-6">
            <p class="text-muted mb-0">
                📄 Показано <?= $pagination['start_item'] ?>-<?= $pagination['end_item'] ?>
                з <?= $pagination['total_items'] ?> товар<?= $pagination['total_items'] != 1 ? 'ів' : '' ?>
            </p>
        </div>

        <!-- Навігація пагінації -->
        <div class="col-md-6">
            <?php if ($total > 1): ?>
                <ul class="pagination justify-content-md-end justify-content-center mb-0">

                    <!-- Кнопка "Перша сторінка" -->
                    <?php if ($current > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl(1, $urlParams) ?>"
                               data-page="1" title="Перша сторінка">
                                ⏮️
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Кнопка "Попередня" -->
                    <?php if ($hasPrev): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl($pagination['prev_page'], $urlParams) ?>"
                               data-page="<?= $pagination['prev_page'] ?>" title="Попередня сторінка">
                                ⬅️ Попередня
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Номери сторінок -->
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i === $current ? 'active' : '' ?>">
                            <?php if ($i === $current): ?>
                                <span class="page-link bg-primary text-white">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a class="page-link" href="<?= buildPaginationUrl($i, $urlParams) ?>" data-page="<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <!-- Три крапки якщо є ще сторінки -->
                    <?php if ($end < $total): ?>
                        <?php if ($end < $total - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>

                        <!-- Остання сторінка -->
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl($total, $urlParams) ?>"
                               data-page="<?= $total ?>" title="Остання сторінка">
                                <?= $total ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Кнопка "Наступна" -->
                    <?php if ($hasNext): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl($pagination['next_page'], $urlParams) ?>"
                               data-page="<?= $pagination['next_page'] ?>" title="Наступна сторінка">
                                Наступна ➡️
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Кнопка "Остання сторінка" -->
                    <?php if ($current < $total): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl($total, $urlParams) ?>"
                               data-page="<?= $total ?>" title="Остання сторінка">
                                ⏭️
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Швидкий перехід до сторінки -->
<?php if ($total > 10): ?>
    <div class="row mt-3">
        <div class="col-md-6 offset-md-6">
            <form method="GET" class="d-flex align-items-center justify-content-md-end justify-content-center">
                <!-- Зберегти поточні параметри -->
                <?php foreach ($urlParams as $key => $value): ?>
                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                <?php endforeach; ?>

                <label for="pageJump" class="form-label me-2 mb-0 text-nowrap">
                    Перейти до:
                </label>
                <input type="number"
                       id="pageJump"
                       name="page"
                       class="form-control form-control-sm me-2"
                       style="width: 80px;"
                       min="1"
                       max="<?= $total ?>"
                       value="<?= $current ?>"
                       placeholder="№">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    Перейти
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>

<style>
    .pagination .page-link {
        border-radius: 6px;
        margin: 0 2px;
        transition: all 0.2s ease;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border-color: #007bff;
        transform: scale(1.1);
    }

    .pagination .page-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }

    @media (max-width: 576px) {
        .pagination {
            flex-wrap: wrap;
        }

        .pagination .page-item {
            margin-bottom: 5px;
        }

        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>