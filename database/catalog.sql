-- Структура бази даних для каталогу товарів

-- Таблиця категорій
CREATE TABLE categories (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            name VARCHAR(255) NOT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблиця товарів
CREATE TABLE products (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(255) NOT NULL,
                          price DECIMAL(10,2) NOT NULL,
                          date_added DATE NOT NULL,
                          category_id INT NOT NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Вставка демонстраційних даних

-- Категорії
INSERT INTO categories (name) VALUES
                                  ('Електроніка'),
                                  ('Одяг'),
                                  ('Книги'),
                                  ('Дім і сад');

-- Товари
INSERT INTO products (name, price, date_added, category_id) VALUES
-- Електроніка
('iPhone 14', 25000.00, '2024-09-01', 1),
('Samsung Galaxy S23', 22000.00, '2024-08-15', 1),
('MacBook Pro', 45000.00, '2024-09-10', 1),
('iPad Air', 18000.00, '2024-08-20', 1),
('AirPods Pro', 7000.00, '2024-09-05', 1),
('Dell XPS 13', 35000.00, '2024-08-25', 1),
('Sony WH-1000XM4', 8500.00, '2024-09-12', 1),
('Nintendo Switch', 12000.00, '2024-08-30', 1),

-- Одяг
('Джинси Levis', 2500.00, '2024-09-03', 2),
('Футболка Nike', 1200.00, '2024-08-18', 2),
('Кросівки Adidas', 3500.00, '2024-09-07', 2),
('Куртка Zara', 4500.00, '2024-08-22', 2),
('Сукня H&M', 1800.00, '2024-09-11', 2),
('Костюм Hugo Boss', 12000.00, '2024-08-28', 2),

-- Книги
('Гаррі Поттер', 350.00, '2024-09-02', 3),
('1984', 280.00, '2024-08-16', 3),
('Майстер і Маргарита', 320.00, '2024-09-06', 3),
('Програмування на PHP', 450.00, '2024-08-24', 3),

-- Дім і сад
('Газонокосарка', 8500.00, '2024-09-04', 4),
('Набір інструментів', 2500.00, '2024-08-19', 4),
('Садовий шланг', 450.00, '2024-09-08', 4),
('Квіткові горщики', 320.00, '2024-08-26', 4),
('Барбекю гриль', 15000.00, '2024-09-09', 4);