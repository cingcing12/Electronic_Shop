CREATE DATABASE shopdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shopdb;

-- users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- categories
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

-- products
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT,
  name VARCHAR(255),
  description TEXT,
  price DECIMAL(10,2),
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- cart_items
-- This holds items a user has added (but not yet ordered)
CREATE TABLE cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  quantity INT DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- wishlist_items
CREATE TABLE wishlist_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- orders
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  total_amount DECIMAL(10,2),
  status VARCHAR(50) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- order_items
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT,
  unit_price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);


INSERT INTO products (category_id, name, description, price, image) VALUES
(1, 'iPhone 15', 'Latest Apple iPhone 15 with amazing features', 999.99, 'https://www.applex.com.bd/cdn/shop/files/iPhone-15-Plus-_3_-8671.jpg?v=1738426440&width=1445'),
(1, 'Samsung Galaxy S23', 'Samsung flagship phone with high performance', 899.99, 'https://m.media-amazon.com/images/I/61yUiD1CVML._UF1000,1000_QL80_.jpg'),
(2, 'Nike T-Shirt', 'Comfortable sports t-shirt for everyday use', 29.99, 'https://p.turbosquid.com/ts-thumb/zi/J2kOOY/bSKHE9YU/nike_tshirt/jpg/1437859592/1920x1080/fit_q87/fbef5e71c0eacef1027cb01898fb22400659f92d/nike_tshirt.jpg'),
(2, 'Leather Handbag', 'Stylish leather handbag for women', 120.00, 'https://i.etsystatic.com/11844756/r/il/bef9d6/2499118387/il_fullxfull.2499118387_9rig.jpg'),
(3, 'Sofa Set', 'Modern 3-piece sofa set for living room', 550.00, 'https://craftsmill.in/cdn/shop/files/sofas-accent-chairs-cider-orange-soft-velvet-touch-fabric-emily-flared-arm-2-seater-sofa-60-46567514931491.jpg?v=1725047510'),
(4, 'Gaming Chair', 'Ergonomic chair designed for gamers', 199.99, 'https://www.greensoul.online/cdn/shop/files/717896796-slide-1_f4bcd7ae-676f-4618-a81e-074a84749005_650x.jpg?v=1756711932'),
(4, 'PlayStation 5', 'Next-gen gaming console with amazing graphics', 499.99, 'https://www.cnet.com/a/img/resize/05f4f1af2b2243d7dfb1349ab1888878fbf84ceb/hub/2022/10/24/a316fc5e-b8d6-4914-925a-a33170c9abeb/ps5.jpg?auto=webp&fit=crop&height=1200&width=1200'),
(1, 'MacBook Pro 16"', 'Apple MacBook Pro 16-inch with M1 chip', 2499.99, 'https://www.shutterstock.com/image-photo/macbook-pro-m2-16inch-apple-600nw-2288025241.jpg');
