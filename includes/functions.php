<?php

function formatRWF(int $amount): string
{
    return number_format($amount, 0, '.', ',') . ' RWF';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function decodeJson(?string $json): array
{
    if (!$json) {
        return [];
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function normalizeProduct(array $row): array
{
    $row['images'] = decodeJson($row['images'] ?? null);
    $row['tags'] = decodeJson($row['tags'] ?? null);
    $row['has_variants'] = (bool) ($row['has_variants'] ?? false);
    return $row;
}

function getProductVariants(PDO $pdo, int $productId): array
{
    $stmt = $pdo->prepare('SELECT * FROM ecom_product_variants WHERE product_id = ? ORDER BY position, id');
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function attachVariants(PDO $pdo, array $products): array
{
    foreach ($products as &$product) {
        $product = normalizeProduct($product);
        $product['variants'] = getProductVariants($pdo, (int) $product['id']);
    }
    unset($product);

    return $products;
}

function getActiveProducts(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM ecom_products WHERE status = 'active' ORDER BY created_at DESC");
    return attachVariants($pdo, $stmt->fetchAll());
}

function getProductByHandle(PDO $pdo, string $handle): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM ecom_products WHERE handle = ? AND status = 'active' LIMIT 1");
    $stmt->execute([$handle]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $product = normalizeProduct($row);
    $product['variants'] = getProductVariants($pdo, (int) $product['id']);
    return $product;
}

function getCollections(PDO $pdo): array
{
    $handles = ['men', 'women', 'kids', 'shoes', 'accessories', 'new-arrivals'];
    $placeholders = implode(',', array_fill(0, count($handles), '?'));
    $stmt = $pdo->prepare(
        "SELECT id, title, handle FROM ecom_collections
         WHERE handle IN ({$placeholders})
         ORDER BY FIELD(handle, {$placeholders})"
    );
    $stmt->execute(array_merge($handles, $handles));
    return $stmt->fetchAll();
}

function getCollectionByHandle(PDO $pdo, string $handle): ?array
{
    $titles = [
        'new-arrivals' => 'New Arrivals',
        'best-sellers' => 'Best Sellers',
        'men' => 'Men',
        'women' => 'Women',
        'kids' => 'Kids',
        'shoes' => 'Shoes',
        'accessories' => 'Accessories',
    ];

    if (isset($titles[$handle])) {
        return [
            'handle' => $handle,
            'title' => $titles[$handle],
            'description' => null,
        ];
    }

    $stmt = $pdo->prepare('SELECT * FROM ecom_collections WHERE handle = ? LIMIT 1');
    $stmt->execute([$handle]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getCollectionProducts(PDO $pdo, string $handle): array
{
    if ($handle === 'new-arrivals') {
        $products = getActiveProducts($pdo);
        return array_values(array_filter($products, fn($p) => in_array('new', $p['tags'], true)));
    }

    if ($handle === 'best-sellers') {
        $products = getActiveProducts($pdo);
        return array_values(array_filter($products, fn($p) => in_array('bestseller', $p['tags'], true)));
    }

    $typeMap = [
        'men' => 'Men',
        'women' => 'Women',
        'kids' => 'Kids',
        'shoes' => 'Shoes',
        'accessories' => 'Accessories',
    ];

    if (isset($typeMap[$handle])) {
        $stmt = $pdo->prepare("SELECT * FROM ecom_products WHERE status = 'active' AND product_type = ? ORDER BY created_at DESC");
        $stmt->execute([$typeMap[$handle]]);
        return attachVariants($pdo, $stmt->fetchAll());
    }

    $stmt = $pdo->prepare('SELECT * FROM ecom_collections WHERE handle = ? LIMIT 1');
    $stmt->execute([$handle]);
    $collection = $stmt->fetch();
    if (!$collection) {
        return [];
    }

    $stmt = $pdo->prepare(
        "SELECT p.* FROM ecom_products p
         INNER JOIN ecom_product_collections pc ON pc.product_id = p.id
         WHERE pc.collection_id = ? AND p.status = 'active'
         ORDER BY pc.position, p.created_at DESC"
    );
    $stmt->execute([$collection['id']]);
    return attachVariants($pdo, $stmt->fetchAll());
}

function orderDisplayId(int $id): string
{
    return strtoupper(substr(md5((string) $id), 0, 8));
}

function findOrderByDisplayId(PDO $pdo, string $displayId): ?array
{
    $displayId = strtoupper(trim($displayId));
    if (strlen($displayId) !== 8) return null;
    // Fetch recent orders (max 10k) and match display ID
    $stmt = $pdo->query('SELECT o.*, c.email AS customer_email, c.name AS customer_name, c.phone AS customer_phone FROM ecom_orders o LEFT JOIN ecom_customers c ON c.id = o.customer_id ORDER BY o.id DESC LIMIT 10000');
    foreach ($stmt->fetchAll() as $row) {
        if (orderDisplayId((int) $row['id']) === $displayId) {
            return $row;
        }
    }
    return null;
}
