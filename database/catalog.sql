-- ====================================================
-- Каталог товарів - MVC версія
-- SQL скрипт для створення бази даних
-- ====================================================

-- Створити базу даних
CREATE DATABASE IF NOT EXISTS catalog_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE catalog_mvc;

-- ====================================================
-- ТАБЛИЦІ
-- ====================================================

-- Таблиця категорій
CREATE TABLE IF NOT EXISTS categories (
                                          id INT AUTO_INCREMENT PRIMARY KEY,
                                          name VARCHAR(255) NOT NULL,
    description TEXT,
    slug VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_name (name),
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця товарів
CREATE TABLE IF NOT EXISTS products (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) DEFAULT NULL,
    sku VARCHAR(100),
    slug VARCHAR(255),
    date_added DATE NOT NULL,
    category_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    stock_quantity INT DEFAULT 0,
    min_quantity INT DEFAULT 1,
    weight DECIMAL(8,2) DEFAULT NULL,
    dimensions VARCHAR(100),
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE,

    INDEX idx_name (name),
    INDEX idx_price (price),
    INDEX idx_category (category_id),
    INDEX idx_date_added (date_added),
    INDEX idx_sku (sku),
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_featured (is_featured),
    INDEX idx_stock (stock_quantity),
    FULLTEXT idx_search (name, description)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- ДЕМОНСТРАЦІЙНІ ДАНІ
-- ====================================================

-- Очистити таблиці перед вставкою
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE products;
TRUNCATE TABLE categories;
SET FOREIGN_KEY_CHECKS = 1;

-- Вставити категорії
INSERT INTO categories (id, name, description, slug, is_active, sort_order) VALUES
                                                                                (1, 'Електроніка', 'Сучасні електронні пристрої та гаджети', 'electronics', TRUE, 1),
                                                                                (2, 'Одяг', 'Модний та якісний одяг для всієї родини', 'clothing', TRUE, 2),
                                                                                (3, 'Книги', 'Художня та навчальна література', 'books', TRUE, 3),
                                                                                (4, 'Дім і сад', 'Товари для дому та садівництва', 'home-garden', TRUE, 4),
                                                                                (5, 'Спорт', 'Спортивні товари та обладнання', 'sports', TRUE, 5);

-- Вставити товари

-- Електроніка
INSERT INTO products (name, description, price, old_price, sku, slug, date_added, category_id, is_active, is_featured, stock_quantity) VALUES
                                                                                                                                           ('iPhone 14 Pro', 'Новітший смартфон Apple з потужним процесором A16 Bionic та професійною системою камер', 35000.00, 38000.00, 'IPHONE14PRO-256', 'iphone-14-pro', '2024-09-01', 1, TRUE, TRUE, 15),
                                                                                                                                           ('Samsung Galaxy S23 Ultra', 'Флагманський Android смартфон з S Pen та 200MP камерою', 32000.00, NULL, 'GALAXY-S23-ULTRA', 'samsung-galaxy-s23-ultra', '2024-08-15', 1, TRUE, TRUE, 8),
                                                                                                                                           ('MacBook Pro 14"', 'Професійний ноутбук з чіпом M2 Pro для творчих завдань', 65000.00, 70000.00, 'MBP14-M2PRO', 'macbook-pro-14', '2024-09-10', 1, TRUE, TRUE, 5),
                                                                                                                                           ('iPad Air 5', 'Універсальний планшет для роботи та розваг з чіпом M1', 22000.00, NULL, 'IPAD-AIR5-256', 'ipad-air-5', '2024-08-20', 1, TRUE, FALSE, 12),
                                                                                                                                           ('AirPods Pro 2', 'Бездротові навушники з активним шумозаглушенням', 8500.00, 9000.00, 'AIRPODS-PRO2', 'airpods-pro-2', '2024-09-05', 1, TRUE, FALSE, 25),
                                                                                                                                           ('Dell XPS 13', 'Компактний ноутбук для бізнесу з процесором Intel i7', 45000.00, NULL, 'DELL-XPS13-I7', 'dell-xps-13', '2024-08-25', 1, TRUE, FALSE, 7),
                                                                                                                                           ('Sony WH-1000XM5', 'Преміальні бездротові навушники з найкращим шумозаглушенням', 12000.00, 13500.00, 'SONY-WH1000XM5', 'sony-wh-1000xm5', '2024-09-12', 1, TRUE, FALSE, 18),
                                                                                                                                           ('Nintendo Switch OLED', 'Гібридна ігрова консоль з OLED екраном', 14500.00, NULL, 'SWITCH-OLED-WHITE', 'nintendo-switch-oled', '2024-08-30', 1, TRUE, TRUE, 10);

-- Одяг
INSERT INTO products (name, description, price, old_price, sku, slug, date_added, category_id, is_active, is_featured, stock_quantity) VALUES
                                                                                                                                           ('Джинси Levi\'s 501', 'Класичні джинси прямого крою з якісного деніму', 3200.00, NULL, 'LEVIS-501-W32L32', 'levis-501-jeans', '2024-09-03', 2, TRUE, FALSE, 30),
('Футболка Nike Dri-FIT', 'Спортивна футболка з технологією вологовідведення', 1500.00, 1800.00, 'NIKE-DRYFIT-M', 'nike-dri-fit-tshirt', '2024-08-18', 2, TRUE, FALSE, 45),
('Кросівки Adidas Ultraboost', 'Бігові кросівки з технологією Boost для максимального комфорту', 4200.00, NULL, 'ADIDAS-UB-42', 'adidas-ultraboost', '2024-09-07', 2, TRUE, TRUE, 22),
('Куртка Zara', 'Стильна демісезонна куртка з водовідштовхувальним покриттям', 5500.00, 6200.00, 'ZARA-JACKET-L', 'zara-jacket', '2024-08-22', 2, TRUE, FALSE, 18),
('Сукня H&M', 'Елегантна коктейльна сукня для особливих випадків', 2200.00, NULL, 'HM-DRESS-M', 'hm-cocktail-dress', '2024-09-11', 2, TRUE, FALSE, 12),
('Костюм Hugo Boss', 'Діловий костюм преміум класу з вовни мерино', 18000.00, 20000.00, 'BOSS-SUIT-52', 'hugo-boss-suit', '2024-08-28', 2, TRUE, TRUE, 6);

-- Книги
INSERT INTO products (name, description, price, old_price, sku, slug, date_added, category_id, is_active, is_featured, stock_quantity) VALUES
('Гаррі Поттер (повне зібрання)', 'Повна колекція романів Дж.К. Роулінг у подарунковому виданні', 1200.00, NULL, 'HP-COMPLETE-SET', 'harry-potter-complete', '2024-09-02', 3, TRUE, TRUE, 25),
('1984', 'Культовий антиутопічний роман Джорджа Орвелла', 320.00, NULL, 'ORWELL-1984', '1984-orwell', '2024-08-16', 3, TRUE, FALSE, 40),
('Майстер і Маргарита', 'Безсмертний роман Михайла Булгакова в новому перекладі', 380.00, 420.00, 'BULGAKOV-MM', 'master-margarita', '2024-09-06', 3, TRUE, FALSE, 35),
('Програмування на PHP', 'Сучасний посібник з веб-розробки на PHP 8+', 650.00, NULL, 'PHP-PROGRAMMING', 'php-programming-guide', '2024-08-24', 3, TRUE, TRUE, 20),
('Історія України', 'Комплексний курс історії України від давніх часів до сьогодення', 580.00, 640.00, 'HISTORY-UKRAINE', 'ukraine-history', '2024-09-13', 3, TRUE, FALSE, 15);

-- Дім і сад
INSERT INTO products (name, description, price, old_price, sku, slug, date_added, category_id, is_active, is_featured, stock_quantity) VALUES
('Газонокосарка Bosch', 'Електрична газонокосарка з мультифункцією та збірником трави', 12500.00, NULL, 'BOSCH-ROTAK32', 'bosch-lawnmower', '2024-09-04', 4, TRUE, TRUE, 8),
('Набір інструментів', 'Професійний набір інструментів у зручному кейсі (108 предметів)', 3200.00, 3600.00, 'TOOLSET-108', 'professional-tool-set', '2024-08-19', 4, TRUE, FALSE, 15),
('Садовий шланг 50м', 'Армований садовий шланг з набором насадок для поливу', 680.00, NULL, 'HOSE-50M-SET', 'garden-hose-50m', '2024-09-08', 4, TRUE, FALSE, 25),
('Квіткові горщики (набір)', 'Набір з 5 керамічних горщиків різних розмірів з підставками', 450.00, 520.00, 'POTS-CERAMIC-5', 'ceramic-flower-pots', '2024-08-26', 4, TRUE, FALSE, 20),
('Барбекю гриль Weber', 'Газовий гриль для приготування на відкритому повітрі', 22000.00, 25000.00, 'WEBER-GENESIS', 'weber-bbq-grill', '2024-09-09', 4, TRUE, TRUE, 4);

-- Спорт
INSERT INTO products (name, description, price, old_price, sku, slug, date_added, category_id, is_active, is_featured, stock_quantity) VALUES
('Велосипед Trek', 'Гірський велосипед з 21 швидкістю та алюмінієвою рамою', 18500.00, NULL, 'TREK-MTB-21', 'trek-mountain-bike', '2024-09-14', 5, TRUE, TRUE, 6),
('Гантелі регульовані', 'Набір регульованих гантелей від 5 до 25 кг кожна', 4800.00, 5200.00, 'ADJ-DUMBBELLS', 'adjustable-dumbbells', '2024-08-21', 5, TRUE, FALSE, 12),
('Фітнес-браслет Xiaomi', 'Розумний браслет з моніторингом здоров\'я та GPS', 2200.00, NULL, 'XIAOMI-BAND7', 'xiaomi-fitness-band', '2024-09-15', 5, TRUE, FALSE, 30),
                                                                                                                                           ('Футбольний м\'яч Nike', 'Офіційний футбольний м\'яч FIFA Quality Pro', 1200.00, 1400.00, 'NIKE-BALL-FIFA', 'nike-football-fifa', '2024-08-29', 5, TRUE, FALSE, 40);

-- ====================================================
-- ІНДЕКСИ ДЛЯ ОПТИМІЗАЦІЇ
-- ====================================================

-- Додаткові індекси для швидкого пошуку
ALTER TABLE products ADD INDEX idx_price_category (category_id, price);
ALTER TABLE products ADD INDEX idx_date_category (category_id, date_added);
ALTER TABLE products ADD INDEX idx_active_featured (is_active, is_featured);

-- ====================================================
-- СТАТИСТИКА
-- ====================================================

-- Показати статистику
SELECT
    'Категорії' as entity,
    COUNT(*) as count,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_count
FROM categories

UNION ALL

SELECT
    'Товари' as entity,
    COUNT(*) as count,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_count
FROM products;

-- Показати кількість товарів по категоріях
SELECT
    c.name as category,
    COUNT(p.id) as products_count,
    AVG(p.price) as avg_price,
    MIN(p.price) as min_price,
    MAX(p.price) as max_price
FROM categories c
         LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
WHERE c.is_active = 1
GROUP BY c.id, c.name
ORDER BY products_count DESC;

-- ====================================================
-- ЗАВЕРШЕННЯ
-- ====================================================

SELECT '✅ База даних успішно створена та наповнена даними!' as status;