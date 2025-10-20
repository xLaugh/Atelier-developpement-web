SET NAMES utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    brand VARCHAR(100),
    image_url VARCHAR(255),
    price_per_day DECIMAL(10,2),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    status TINYINT(1) DEFAULT 0, -- 0 = libre, 1 = pris
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_id INT,
    action ENUM('create', 'update', 'borrow', 'return', 'delete') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Données de test (catégories)
INSERT INTO categories (id, name, description) VALUES
  (1, 'Perçage', 'Outils de perçage et trépan'),
  (2, 'Sciage', 'Scies et lames'),
  (3, 'Ponçage', 'Ponceuses et abrasifs')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Données de test (modèles)
INSERT INTO models (id, category_id, name, brand, image_url, price_per_day, description) VALUES
  (1, 1, 'Perceuse percussion 18V', 'Makita', 'https://via.placeholder.com/300x200?text=Perceuse', 10, 'Perceuse sans fil avec 2 batteries'),
  (2, 2, 'Scie sauteuse 750W', 'Bosch', 'https://via.placeholder.com/300x200?text=Scie+sauteuse', 5, 'Précise, pour bois et métal'),
  (3, 3, 'Ponceuse excentrique 125mm', 'DeWalt', 'https://via.placeholder.com/300x200?text=Ponceuse', 25, 'Avec aspiration intégrée')
ON DUPLICATE KEY UPDATE name = VALUES(name), image_url = VALUES(image_url), price_per_day = VALUES(price_per_day);

-- Données de test (exemplaires)
INSERT INTO items (id, model_id, status) VALUES
  (1, 1, 0),
  (2, 2, 0),
  (3, 3, 0)
ON DUPLICATE KEY UPDATE status = VALUES(status);