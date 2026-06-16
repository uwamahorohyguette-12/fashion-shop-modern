DROP DATABASE IF EXISTS fashion_shop;
CREATE DATABASE fashion_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fashion_shop;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(255),
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ecom_products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  handle VARCHAR(255) NOT NULL UNIQUE,
  description TEXT,
  price INT NOT NULL,
  product_type VARCHAR(50) DEFAULT 'Men',
  inventory_qty INT DEFAULT 10,
  images TEXT,
  tags TEXT,
  status ENUM('active','draft','archived') DEFAULT 'active',
  has_variants TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ecom_product_variants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  title VARCHAR(100),
  option1 VARCHAR(50),
  sku VARCHAR(100),
  price INT,
  inventory_qty INT DEFAULT 10,
  position INT DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES ecom_products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ecom_collections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  handle VARCHAR(255) NOT NULL UNIQUE,
  description TEXT,
  image VARCHAR(500),
  is_visible TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS ecom_product_collections (
  product_id INT NOT NULL,
  collection_id INT NOT NULL,
  position INT DEFAULT 0,
  PRIMARY KEY (product_id, collection_id),
  FOREIGN KEY (product_id) REFERENCES ecom_products(id) ON DELETE CASCADE,
  FOREIGN KEY (collection_id) REFERENCES ecom_collections(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ecom_customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  name VARCHAR(255),
  phone VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS ecom_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  status ENUM('pending','paid','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  subtotal INT NOT NULL,
  tax INT DEFAULT 0,
  shipping INT DEFAULT 0,
  total INT NOT NULL,
  shipping_address TEXT,
  payment_ref VARCHAR(255),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES ecom_customers(id)
);

CREATE TABLE IF NOT EXISTS ecom_order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT,
  variant_id INT,
  product_name VARCHAR(255),
  variant_title VARCHAR(100),
  sku VARCHAR(100),
  quantity INT NOT NULL,
  unit_price INT NOT NULL,
  total INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES ecom_orders(id) ON DELETE CASCADE
);

-- Default admin: admin@kigali.local / password
INSERT INTO users (email, password_hash, full_name, is_admin) VALUES
('admin@kigali.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3OdGbvbFDsWQfWfOQ5byMi.Yu', 'Admin User', 1);

INSERT INTO ecom_collections (title, handle, description, image, is_visible) VALUES
('Women', 'women', 'Women''s fashion', 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083138409_183be21e.jpg', 1),
('Men', 'men', 'Men''s fashion', 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083010568_48c6d093.png', 1),
('Kids', 'kids', 'Kids fashion', NULL, 1),
('Shoes', 'shoes', 'Footwear', 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083208276_ea2ca5ad.jpg', 1),
('Accessories', 'accessories', 'Bags & accessories', 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083296089_d207166b.jpg', 1),
('New Arrivals', 'new-arrivals', 'Latest styles', NULL, 1);

INSERT INTO ecom_products (name, handle, description, price, product_type, inventory_qty, images, tags, status, has_variants) VALUES
('Kigali Linen Shirt', 'kigali-linen-shirt', 'Breathable linen shirt perfect for Kigali weather.', 35000, 'Men', 25,
 '["https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083010568_48c6d093.png"]',
 '["new","bestseller"]', 'active', 1),
('Ankara Wrap Dress', 'ankara-wrap-dress', 'Vibrant Ankara print wrap dress.', 45000, 'Women', 18,
 '["https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083138409_183be21e.jpg"]',
 '["new","bestseller"]', 'active', 1),
('Urban Sneakers', 'urban-sneakers', 'Comfortable everyday sneakers.', 55000, 'Shoes', 30,
 '["https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083208276_ea2ca5ad.jpg"]',
 '["bestseller"]', 'active', 1),
('Leather Crossbody Bag', 'leather-crossbody-bag', 'Handcrafted leather bag.', 28000, 'Accessories', 15,
 '["https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083296089_d207166b.jpg"]',
 '["new"]', 'active', 0);

INSERT INTO ecom_product_variants (product_id, title, option1, sku, price, inventory_qty, position) VALUES
(1, 'Small', 'S', 'LINEN-S', 35000, 10, 1),
(1, 'Medium', 'M', 'LINEN-M', 35000, 10, 2),
(1, 'Large', 'L', 'LINEN-L', 35000, 5, 3),
(2, 'Small', 'S', 'ANKARA-S', 45000, 8, 1),
(2, 'Medium', 'M', 'ANKARA-M', 45000, 6, 2),
(2, 'Large', 'L', 'ANKARA-L', 45000, 4, 3),
(3, 'Size 40', '40', 'SNEAK-40', 55000, 10, 1),
(3, 'Size 42', '42', 'SNEAK-42', 55000, 10, 2),
(3, 'Size 44', '44', 'SNEAK-44', 55000, 10, 3);
