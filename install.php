<?php
/**
 * One-time setup: creates database tables and sample data.
 * Open http://localhost/fashion-shop-modern/install.php then delete this file.
 */
header('Content-Type: text/html; charset=utf-8');

$sqlFile = __DIR__ . '/database/schema.sql';
if (!is_file($sqlFile)) {
    die('schema.sql not found.');
}

try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    $pdo = new PDO('mysql:host=localhost;dbname=fashion_shop;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $count = (int) $pdo->query('SELECT COUNT(*) FROM ecom_products')->fetchColumn();
    $email = $pdo->query('SELECT email FROM users LIMIT 1')->fetchColumn();

    echo '<h1>Install complete</h1>';
    echo '<p>Database <strong>fashion_shop</strong> is ready.</p>';
    echo '<p>Products: <strong>' . $count . '</strong></p>';
    echo '<p>Admin user: <strong>' . htmlspecialchars((string) $email) . '</strong> (default password is documented in database/schema.sql)</p>';
    echo '<p><a href="index.php">Open store</a> · <a href="test-db.php">Test connection</a></p>';
    echo '<p style="color:#b45309">Delete install.php after setup for security.</p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Install failed</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}
