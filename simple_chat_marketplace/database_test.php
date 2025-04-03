<?php
require_once 'includes/config.php';

header('Content-Type: text/plain');

try {
    // Test database connection
    echo "Testing database connection...\n";
    $pdo->query("SELECT 1");
    echo "✓ Connection successful\n\n";
    
    // Check required tables (SQLite compatible)
    $tables = ['users', 'messages', 'shops', 'products'];
    $missing = [];
    
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
            echo "✓ Table '$table' exists\n";
        } catch (PDOException $e) {
            echo "✗ Table '$table' missing\n";
            $missing[] = $table;
        }
    }
    
    if (!empty($missing)) {
        echo "\nError: Missing tables detected\n";
        echo "Please run database/database.sql to create tables\n";
        exit(1);
    }
    
    // Test users table
    echo "\nTesting users table...\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "Found $count users\n";
    
    if ($count < 2) {
        echo "Warning: Sample users may be missing\n";
    }
    
    echo "\nAll database tests passed successfully!\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>