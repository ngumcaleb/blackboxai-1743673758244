<?php
// Create tables for SQLite
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        profile_image TEXT DEFAULT 'default.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender_id INTEGER NOT NULL,
        receiver_id INTEGER NOT NULL,
        content TEXT,
        image_url TEXT,
        status TEXT DEFAULT 'sent',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id),
        FOREIGN KEY (receiver_id) REFERENCES users(id)
    );

    CREATE TABLE IF NOT EXISTS shops (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        shop_name TEXT NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    );

    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        shop_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        price REAL NOT NULL,
        description TEXT,
        image_url TEXT DEFAULT 'default_product.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (shop_id) REFERENCES shops(id)
    )
");

// Insert sample data if tables are empty
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($userCount == 0) {
    $pdo->exec("
        INSERT INTO users (username, email, password) VALUES 
        ('john_doe', 'john@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
        ('jane_smith', 'jane@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
        
        INSERT INTO shops (user_id, shop_name, description) VALUES
        (1, 'John''s Electronics', 'Best gadgets in town'),
        (2, 'Jane''s Fashion', 'Trendy clothes and accessories');
        
        INSERT INTO products (shop_id, title, price, description) VALUES
        (1, 'Wireless Earbuds', 59.99, 'Noise cancelling wireless earbuds'),
        (1, 'Smart Watch', 199.99, 'Fitness tracking smart watch'),
        (2, 'Summer Dress', 39.99, 'Lightweight cotton summer dress'),
        (2, 'Denim Jacket', 49.99, 'Classic blue denim jacket');
    ");
}
?>