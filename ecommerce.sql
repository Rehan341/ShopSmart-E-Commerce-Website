-- ShopSmart e-commerce demonstration database
-- Import this file in phpMyAdmin (XAMPP). It creates the database and sample data.
CREATE DATABASE IF NOT EXISTS ecommerce_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_portal;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NULL,
  name VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  image VARCHAR(255) NOT NULL DEFAULT 'assets/images/product-placeholder.svg',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_products_name (name),
  INDEX idx_products_active (active)
) ENGINE=InnoDB;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  postal_code VARCHAR(20) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  payment_method ENUM('COD') NOT NULL DEFAULT 'COD',
  status ENUM('Placed','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Placed',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (status)
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  product_name VARCHAR(180) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  line_total DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO categories (name) VALUES
('Electronics'),('Home & Living'),('Accessories'),('Fitness');

INSERT INTO products (category_id,name,description,price,stock,image) VALUES
(1,'Wireless Headphones','Comfortable over-ear headphones for music, study sessions, and calls. Sample product for demonstrating product details, stock tracking, and order processing.',2499.00,25,'assets/images/product-placeholder.svg'),
(1,'Portable Bluetooth Speaker','Compact speaker for desks and travel. The demo store does not represent a real brand or warranty.',1799.00,18,'assets/images/product-placeholder.svg'),
(1,'USB-C Charging Cable','Durable everyday USB-C cable suitable for compatible devices. Check your device requirements before purchasing in a real store.',299.00,80,'assets/images/product-placeholder.svg'),
(2,'Ceramic Coffee Mug','Minimal ceramic mug for coffee, tea, or a favourite warm drink.',349.00,40,'assets/images/product-placeholder.svg'),
(2,'Desk Organiser','A simple organiser for stationery and small desk accessories.',599.00,22,'assets/images/product-placeholder.svg'),
(2,'Reusable Water Bottle','Reusable bottle for everyday hydration at school, work, or on the move.',499.00,35,'assets/images/product-placeholder.svg'),
(3,'Everyday Backpack','A versatile backpack with room for books, a laptop, and daily essentials.',1299.00,16,'assets/images/product-placeholder.svg'),
(3,'Classic Wrist Watch','A clean, minimalist watch style for the sample catalogue.',1899.00,12,'assets/images/product-placeholder.svg'),
(4,'Yoga Mat','A cushioned mat for stretching, yoga, and home exercise routines.',899.00,20,'assets/images/product-placeholder.svg'),
(4,'Resistance Bands Set','A compact set for basic resistance training and mobility exercises.',699.00,28,'assets/images/product-placeholder.svg'),
(3,'Notebook Set','Three notebooks for planning, study notes, and everyday writing.',249.00,50,'assets/images/product-placeholder.svg'),
(2,'LED Desk Lamp','An adjustable desk lamp for reading and focused work.',1099.00,14,'assets/images/product-placeholder.svg');

-- Admin creation:
-- Register a normal account through register.php first, then promote that account
-- using phpMyAdmin SQL, replacing the email below with your own registered email:
-- UPDATE users SET role='admin' WHERE email='your-email@example.com';
-- This avoids shipping a public, hard-coded administrator password.
