<?php
require_once __DIR__ . '/config/database.php';

echo '<h1>Database connection OK</h1>';
echo '<p>Connected to <strong>fashion_shop</strong>.</p>';
echo '<h2>Tables:</h2><ul>';
foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
    echo '<li>' . htmlspecialchars($table) . '</li>';
}
echo '</ul>';
echo '<p><a href="index.php">Go to store</a></p>';
