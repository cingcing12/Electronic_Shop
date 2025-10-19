CREATE DATABASE shopdb CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shopdb;

-- users
CREATE TABLE
  users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

-- categories
CREATE TABLE
  categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
  );

-- products
CREATE TABLE
  products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255),
    description TEXT,
    price DECIMAL(10, 2),
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
  );

-- cart_items
-- This holds items a user has added (but not yet ordered)
CREATE TABLE
  cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
  );

-- wishlist_items
CREATE TABLE
  wishlist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
  );

-- orders
CREATE TABLE
  orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
  );

-- order_items
CREATE TABLE
  order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    unit_price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
  );

-- Create categories
INSERT INTO
  categories (id, name)
VALUES
  (1, 'Electronics'),
  (2, 'Accessorise Computer'),
  (3, 'Accessorise Mobile'),
  (4, 'Gaming');

-- Then insert products
INSERT INTO
  products (category_id, name, description, price, image)
VALUES
  (
    1,
    'iPhone 15',
    'Latest Apple iPhone 15 with amazing features',
    999.99,
    'https://www.applex.com.bd/cdn/shop/files/iPhone-15-Plus-_3_-8671.jpg?v=1738426440&width=1445'
  ),
  (
    1,
    'Samsung Galaxy S23',
    'Samsung flagship phone with high performance',
    899.99,
    'https://m.media-amazon.com/images/I/61yUiD1CVML._UF1000,1000_QL80_.jpg'
  ),
  (
    2,
    'Asus ROG keyboard',
    'ASUS ROG keyboards are high-performance gaming keyboards often featuring premium components like a full aluminum chassis, gasket mounting, and customizable features such as an OLED screen and multi-function control knob. They come in various layouts, including compact 75% and 65% sizes, and offer a range of options like hot-swappable switches, multiple connectivity modes (wired USB-C, 2.4 GHz wireless, Bluetooth), and unique magnetic switches with Hall effect sensors for fast actuation. Key technologies include the ASUS Speed Nova wireless technology for low latency, and features like "rapid trigger" for near-instant key registration. ',
    119.99,
    'https://cdn.mos.cms.futurecdn.net/wB9kzVzVhN2MC54RDagKYj-1000-80.jpg'
  ),
  (
    2,
    'Aerox 5 Wireless',
    'The Aerox 5 Wireless is a lightweight, versatile, 74g wireless gaming mouse from SteelSeries, suitable for various game genres, with a 74g weight, 9 programmable buttons including a unique flick switch, and the TrueMove Air sensor for high precision. It offers both 2.4 GHz and Bluetooth 5.0 connectivity, and features IP54-rated water and dust resistance, 100% PTFE glide skates for smooth movement, and up to 180 hours of battery life with fast charging capabilities.',
    99.99,
    'https://images.ctfassets.net/w5r1fvmogo3f/5EPZQX7wn6HZIuf2BsJg2h/e252915b04a0bebb25645b7cc70eb913/aerox_5_wl_black_pdp_tile_m_lightweight.jpg?fm=webp&q=90&fit=scale&w=1398'
  ),
  (
    3,
    'AirPods Max',
    `AirPods Max are premium, over-ear wireless headphones from Apple that offer high-fidelity audio and advanced features like Active Noise Cancellation and Spatial Audio. They are built with a premium design, comfort in mind, and use Apple's H1 chip for efficient performance. Key features include custom-built drivers for detailed sound, Adaptive Transparency Mode, and head-tracking for immersive audio.`,
    489.00,
    'https://arystorephone.com/wp-content/uploads/2021/08/airpods-max-spacegray.jpg'
  ),
  (
    3,
    'Apple AirPods',
    `Apple offers several versions of its AirPods, a line of wireless Bluetooth earbuds with a distinctive white, minimalist design. Recent updates have improved comfort, battery life, and overall audio experience across the product family, with the standard AirPods 4 offering a semi-open fit and the AirPods Pro 3 providing an in-ear fit with enhanced features.`,
    299.00,
    'https://jo-cell.com/cdn/shop/products/MME73_800x_6da587dc-a02a-4aca-8903-c22080d20fe2.webp?v=1669381321'
  ),
  (
    4,
    'Gaming Chair',
    'Ergonomic chair designed for gamers',
    199.99,
    'https://www.greensoul.online/cdn/shop/files/717896796-slide-1_f4bcd7ae-676f-4618-a81e-074a84749005_650x.jpg?v=1756711932'
  ),
  (
    4,
    'PlayStation 5',
    'Next-gen gaming console with amazing graphics',
    499.99,
    'https://www.cnet.com/a/img/resize/05f4f1af2b2243d7dfb1349ab1888878fbf84ceb/hub/2022/10/24/a316fc5e-b8d6-4914-925a-a33170c9abeb/ps5.jpg?auto=webp&fit=crop&height=1200&width=1200'
  ),
  (
    1,
    'MacBook Pro 16"',
    'Apple MacBook Pro 16-inch with M1 chip',
    2499.99,
    'https://www.shutterstock.com/image-photo/macbook-pro-m2-16inch-apple-600nw-2288025241.jpg'
  ),
  (
    1,
    'ASUS ROG Strix SCAR 18',
    'The ASUS ROG Strix SCAR 18 is a high-performance, desktop-replacement gaming laptop designed for enthusiasts who want maximum power in a portable format. It is known for its large 18-inch Mini-LED display, powerful processor and graphics options, and extensive RGB lighting.',
    2999.99,
    'https://img.pacifiko.com/PROD/resize/1/500x500/B0DW29H85Z.jpg'
  ),
  (
    4,
    'Gaming PC Case',
    'A gaming PC case is a chassis that houses and protects the components of a gaming computer, such as the motherboard, CPU, and GPU. Unlike standard computer cases, gaming cases are designed with a focus on optimal airflow, robust cooling, and aesthetic customization. The size and features vary widely, accommodating different hardware configurations and personal preferences.',
    349.99,
    'https://easypc.com.ph/cdn/shop/files/YGT_V300_MAtx_Tempered_Glass_Gaming_PC_Case_Black-b_2048x.png?v=1701411825'
  ),
  (
    1,
    'Curved Gaming Monitor',
    'A curved gaming monitor is a display with a gently arcing screen designed to wrap around your field of vision, creating a more immersive experience for gaming. By mimicking the natural curve of the human eye, these monitors reduce distortion and eye strain, especially on larger, ultrawide displays.',
    499.99,
    'https://xiaomistoreph.com/cdn/shop/files/Xiaomi_CurvedGamingMonitorG34WQI_WBG_1_1024x1024.jpg?v=1749552823'
  );

ALTER TABLE categories
ADD COLUMN icon VARCHAR(100) NULL;

UPDATE categories
SET
  icon = 'bi-phone'
WHERE
  id = 1;

-- Electronics
UPDATE categories
SET
  icon = 'bi-laptop'
WHERE
  id = 2;

-- Accessorise Computer
UPDATE categories
SET
  icon = 'bi-phone'
WHERE
  id = 3;

-- Furniture
UPDATE categories
SET
  icon = 'bi-controller'
WHERE
  id = 4;

-- Gaming
ALTER TABLE orders
ADD COLUMN address VARCHAR(255) NOT NULL AFTER total_amount,
ADD COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cash',
ADD COLUMN card_number VARCHAR(50) NULL,
ADD COLUMN card_name VARCHAR(100) NULL;

ALTER TABLE users
ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user';