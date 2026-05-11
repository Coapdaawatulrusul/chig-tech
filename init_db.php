<?php
$host = 'localhost'; $user = 'root'; $pass = ''; $db = 'chig_tech';
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
$conn->select_db($db);

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), email VARCHAR(100) UNIQUE,
    password VARCHAR(255), role ENUM('customer','admin') DEFAULT 'customer',
    is_approved TINYINT(1) DEFAULT 0, city VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), slug VARCHAR(100) UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(200), description TEXT,
    price DECIMAL(10,2), image VARCHAR(255), category_id INT,
    type ENUM('product','service') DEFAULT 'product', `condition` VARCHAR(50),
    shipping_fee DECIMAL(10,2) DEFAULT 0, stock INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(200), description TEXT,
    price DECIMAL(10,2), image VARCHAR(255), duration VARCHAR(100)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(200), description TEXT,
    image VARCHAR(255), link VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, total DECIMAL(10,2),
    shipping_address TEXT, status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY, order_id INT, product_id INT,
    quantity INT, price DECIMAL(10,2)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, product_id INT,
    rating INT, comment TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), email VARCHAR(100),
    message TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
";
$conn->multi_query($sql);
while ($conn->more_results()) $conn->next_result();

$password = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("INSERT IGNORE INTO users (name, email, password, role, is_approved) VALUES ('Admin', 'admin@chigtech.com', '$password', 'admin', 1)");
$conn->query("INSERT IGNORE INTO categories (name, slug) VALUES ('Electronics', 'electronics'), ('Software', 'software'), ('IT Services', 'it-services')");

echo "Database installed successfully! <a href='index.php'>Go to website</a>";
?>