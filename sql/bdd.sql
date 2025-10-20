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
  (3, 'Ponçage', 'Ponceuses et abrasifs'),
  (4, 'Peinture', 'Pinceaux, rouleaux et accessoires'),
  (5, 'Électricité', 'Outils électriques et câblage'),
  (6, 'Plomberie', 'Clés, joints et accessoires')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Données de test (modèles)
INSERT INTO models (id, category_id, name, brand, image_url, price_per_day, description) VALUES
  (1, 1, 'Perceuse percussion 18V', 'Makita', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 10, 'Perceuse sans fil avec 2 batteries'),
  (2, 2, 'Scie sauteuse 750W', 'Bosch', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 5, 'Précise, pour bois et métal'),
  (3, 3, 'Ponceuse excentrique 125mm', 'DeWalt', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 25, 'Avec aspiration intégrée'),
  (4, 1, 'Visseuse compacte 12V', 'Makita', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 8, 'Légère et maniable'),
  (5, 2, 'Scie circulaire 1400W', 'Ryobi', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 12, 'Pour découpes droites rapides'),
  (6, 3, 'Ponceuse à bande 100mm', 'Black+Decker', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 15, 'Ponçage rapide et efficace'),
  (7, 4, 'Rouleau peinture 18cm', 'Purdy', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 3, 'Rouleau professionnel haute qualité'),
  (8, 4, 'Pinceau 5cm', 'Wooster', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 2, 'Pinceau finition soie naturelle'),
  (9, 5, 'Multimètre digital', 'Fluke', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 20, 'Mesures précises tension/courant'),
  (10, 5, 'Détecteur de tension', 'Klein Tools', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 5, 'Détection sans contact'),
  (11, 6, 'Clé à molette 15cm', 'Bahco', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 4, 'Acier chromé haute résistance'),
  (12, 6, 'Té plomberie 20mm', 'Géberit', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_bTfemPHfHmxcNoaLLO3hbxaaMU1npXHsPw&s', 6, 'Té en laiton pour raccordements')
ON DUPLICATE KEY UPDATE name = VALUES(name), image_url = VALUES(image_url), price_per_day = VALUES(price_per_day);

-- Données de test (exemplaires)
INSERT INTO items (id, model_id, status) VALUES
  (1, 1, 0),
  (2, 1, 1),
  (3, 2, 0),
  (4, 3, 0),
  (5, 3, 1),
  (6, 4, 0),
  (7, 5, 0),
  (8, 6, 0),
  (9, 7, 0),
  (10, 8, 0),
  (11, 9, 0),
  (12, 10, 0),
  (13, 11, 0),
  (14, 12, 0)
ON DUPLICATE KEY UPDATE status = VALUES(status);