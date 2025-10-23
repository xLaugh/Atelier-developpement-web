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
    prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
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

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    model_id INT NOT NULL,
    quantity INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    total_price DECIMAL(10,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE
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
  (1, 1, 'Perceuse percussion 18V', 'Makita', 'https://www.topquincaillerie.fr/43289-large_default/perceuse-visseuse-a-percussion-makita-18-v-li-ion-4-ah-o-13-mm-2-batteries-chargeur-coffret.webp', 10, 'Perceuse sans fil avec 2 batteries'),
  (2, 2, 'Scie sauteuse 750W', 'Bosch', 'https://www.bricodepot.fr/images/page_prod_big/179000/179292.jpg', 5, 'Précise, pour bois et métal'),
  (3, 3, 'Ponceuse excentrique 125mm', 'DeWalt', 'https://www.maxoutil.com/media/catalog_webp/product/p/s/PSA10022_1.webp?width=265&height=265&store=fr&image-type=image', 25, 'Avec aspiration intégrée'),
  (4, 1, 'Visseuse compacte 12V', 'Makita', 'https://www.toolnation.fr/media/catalog/product/cache/918bb768bbbb956a1722ec514f7b2742/m/i/milwaukee_4058546568474_image_1_1.jpg', 8, 'Légère et maniable'),
  (5, 2, 'Scie circulaire 1400W', 'Ryobi', 'https://www.bretagne-materiaux.fr/asset/42/26/AST2374226-XL.jpg', 12, 'Pour découpes droites rapides'),
  (6, 3, 'Ponceuse à bande 100mm', 'Black+Decker', 'https://www.manutan.fr/fstrz/r/s/www.manutan.fr/img/S/GRP/ST/AIG6783001.jpg?frz-v=126', 15, 'Ponçage rapide et efficace'),
  (7, 4, 'Rouleau peinture 18cm', 'Purdy', 'https://media.adeo.com/mkp/6832b63da57159f4d81f0ec9e0538940/media.jpeg', 3, 'Rouleau professionnel haute qualité'),
  (8, 4, 'Pinceau 5cm', 'Wooster', 'https://www.seguret-decoration.fr/52238-large_default/pinceau-5cm-farrow.jpg', 2, 'Pinceau finition soie naturelle'),
  (9, 5, 'Multimètre digital', 'Fluke', 'https://m.media-amazon.com/images/I/71tbi6BftXL._AC_UF1000,1000_QL80_.jpg', 20, 'Mesures précises tension/courant'),
  (10, 5, 'Détecteur de tension', 'Klein Tools', 'https://www.derancourt.com/cache/images/product/vat7622021-web-5109.jpg', 5, 'Détection sans contact'),
  (11, 6, 'Clé à molette 15cm', 'Bahco', 'https://media2.master-outillage.com/372264-medium_default/cle-a-molette-150mm-e187366-expert-by-facom.jpg', 4, 'Acier chromé haute résistance'),
  (12, 6, 'Té plomberie 20mm', 'Géberit', 'https://paturevision.fr/4855-large_default/te-a-90-augmente-20-x-25-x-20-mm.jpg', 6, 'Té en laiton pour raccordements'),
  (13, 1, 'Perceuse visseuse 14.4V', 'Milwaukee', 'https://www.toolnation.fr/media/catalog/product/cache/918bb768bbbb956a1722ec514f7b2742/m/i/milwaukee_4058546568474_image_1_1.jpg', 12, 'Perceuse professionnelle avec couple élevé'),
  (14, 1, 'Perceuse à colonne 550W', 'Einhell', 'https://m.media-amazon.com/images/I/71DeRGoJ+fL.jpg', 8, 'Perceuse fixe pour précision maximale'),
  (15, 2, 'Scie à onglets 216mm', 'Metabo', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT44myQIMMExQf6C_8A9266PkEf-lW-ZgEv-w&s', 18, 'Scie à onglets radiale pour coupes précises'),
  (16, 2, 'Scie sabre 18V', 'DeWalt', 'https://www.maxoutil.com/media/catalog_webp/product/s/d/SDD40006_2.webp?width=265&height=265&store=fr&image-type=image', 15, 'Scie sabre sans fil pour démolition'),
  (17, 3, 'Ponceuse vibrante 150W', 'Bosch', 'https://m.media-amazon.com/images/I/71Lsd5nPwEL.jpg', 6, 'Ponceuse vibrante pour finitions'),
  (18, 3, 'Ponceuse delta 120W', 'Black+Decker', 'https://www.stanleyoutillage.fr/EMEA/PRODUCT/IMAGES/HIRES/SFMEW210S-QS/SFMEW210S_1.jpg?resize=530x530', 7, 'Ponceuse delta pour détails'),
  (19, 4, 'Pinceau 10cm', 'Purdy', 'https://alpes-ecomateriaux.fr/656-thickbox_default/spalter.jpg', 3, 'Pinceau largeur 10cm soie naturelle'),
  (20, 4, 'Rouleau 25cm', 'Wooster', 'https://m.media-amazon.com/images/I/51KRdUru-tL._AC_UF1000,1000_QL80_.jpg', 4, 'Rouleau professionnel 25cm'),
  (21, 5, 'Pince à dénuder', 'Knipex', 'https://www.bis-electric.com/media/catalog/product//p/i/pince-a-denuder-170mm-isolee-1000v-0-84-010-stanley_nolabel.jpeg', 8, 'Pince à dénuder automatique'),
  (22, 5, 'Testeur de continuité', 'Fluke', 'https://professionnelle.sirv.com/mesure/img/p/5/2/1/testeur-de-continuite-compact-professionnel.jpg?w=800&h=800&canvas.width=800&canvas.height=800', 6, 'Testeur de continuité professionnel'),
  (23, 6, 'Clé pipe 24mm', 'Facom', 'https://www.kstools.fr/media/catalog/product/cache/c4fdd3da11b6e5a3a778f5cf45c6ccd5/f/o/fot_pro_alg_517.0406-32__sall__aing__v1.jpg', 5, 'Clé pipe acier chromé 24mm'),
  (24, 6, 'Raccords PER 16mm', 'Géberit', 'https://www.maxoutil.com/media/catalog_webp/product/p/l/PLO01137_1.webp?width=265&height=265&store=fr&image-type=image', 25, 'Marteau perforateur professionnel'),
  (26, 2, 'Tronçonneuse 2000W', 'Stihl', 'https://m.media-amazon.com/images/I/51PyARA-XkL.jpg', 30, 'Tronçonneuse électrique puissante'),
  (27, 3, 'Ponceuse à disque 150mm', 'Makita', 'https://maisondutournage.com/17918-large_default/ponceuse-a-disque-et-a-bande.jpg', 12, 'Ponceuse à disque pour métal'),
  (28, 4, 'Pistolet à peinture', 'Wagner', 'https://www.carter-cash.com/upload/media/product/0001/07/f9f4dfcdeef5b19de8406c8ccfe11779412db274.jpeg', 20, 'Pistolet à peinture haute pression'),
  (29, 5, 'Câble électrique 2.5mm²', 'Nexans', 'https://www.camperwood.com/13654-tm_large_default/cable-25-mm-rouge-ho7vk-special-automobile.jpg', 2, 'Câble électrique 2.5mm² par mètre'),
  (30, 6, 'Robinet thermostatique', 'Grohe', 'https://m.media-amazon.com/images/I/719lvuCn8KL.jpg', 15, 'Robinet thermostatique chromé')
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
  (14, 12, 0),
  (15, 13, 0),
  (16, 13, 0),
  (17, 14, 0),
  (18, 15, 0),
  (19, 15, 1),
  (20, 16, 0),
  (21, 16, 0),
  (22, 17, 0),
  (23, 17, 0),
  (24, 18, 0),
  (25, 19, 0),
  (26, 19, 0),
  (27, 20, 0),
  (28, 20, 0),
  (29, 21, 0),
  (30, 22, 0),
  (31, 22, 0),
  (32, 23, 0),
  (33, 23, 0),
  (34, 24, 0),
  (35, 24, 0),
  (36, 25, 0),
  (37, 25, 0),
  (38, 26, 0),
  (39, 27, 0),
  (40, 27, 0),
  (41, 28, 0),
  (42, 28, 0),
  (43, 29, 0),
  (44, 29, 0),
  (45, 30, 0),
  (46, 30, 0)
ON DUPLICATE KEY UPDATE status = VALUES(status);

-- Utilisateur de test
INSERT INTO users (id, prenom, nom, email, password, role)
VALUES (1, 'Test', 'User', 'test@example.com', '$2y$10$HuZXzXIWS8AqmZZyCfvwNuEsZOXlsou2XC225r.Fg4sNU2yEBKzb6', 'user')
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Utilisateur admin
INSERT INTO users (id, prenom, nom, email, password, role)
VALUES (2, 'Admin', 'System', 'admin@admin.com', '$2y$10$HuZXzXIWS8AqmZZyCfvwNuEsZOXlsou2XC225r.Fg4sNU2yEBKzb6', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email);