<?php
require_once __DIR__ . '/includes/init.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('checkout.php');
}

$items = json_decode($_POST['cart_json'] ?? '[]', true);
if (!is_array($items) || !$items) {
    redirect('checkout.php');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? 'Kigali');
$country = trim($_POST['country'] ?? 'Rwanda');
$momo = trim($_POST['momo_number'] ?? '');

if (!$name || !$email || !$phone || !$address || !$momo) {
    redirect('checkout.php');
}

$subtotal = 0;
foreach ($items as $item) {
    $subtotal += (int) $item['price'] * (int) $item['quantity'];
}
$total = $subtotal;

$shippingAddress = json_encode([
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    'city' => $city,
    'country' => $country,
]);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT id FROM ecom_customers WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if ($customer) {
        $customerId = (int) $customer['id'];
        $stmt = $pdo->prepare('UPDATE ecom_customers SET name = ?, phone = ? WHERE id = ?');
        $stmt->execute([$name, $phone, $customerId]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO ecom_customers (email, name, phone) VALUES (?, ?, ?)');
        $stmt->execute([$email, $name, $phone]);
        $customerId = (int) $pdo->lastInsertId();
    }

    $paymentRef = 'momo_' . time();
    $stmt = $pdo->prepare(
        'INSERT INTO ecom_orders (customer_id, status, subtotal, tax, shipping, total, shipping_address, payment_ref, notes)
         VALUES (?, \'pending\', ?, 0, 0, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $customerId,
        $subtotal,
        $total,
        $shippingAddress,
        $paymentRef,
        'MTN MoMo (sim) ' . $momo,
    ]);
    $orderId = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        'INSERT INTO ecom_order_items (order_id, product_id, variant_id, product_name, variant_title, sku, quantity, unit_price, total)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    foreach ($items as $item) {
        $qty = (int) $item['quantity'];
        $unitPrice = (int) $item['price'];
        $itemStmt->execute([
            $orderId,
            $item['product_id'] ?: null,
            $item['variant_id'] ?: null,
            $item['name'] ?? '',
            $item['variant_title'] ?? null,
            $item['sku'] ?? null,
            $qty,
            $unitPrice,
            $unitPrice * $qty,
        ]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    die('Order failed. Please try again.');
}

redirect('order-confirmation.php?id=' . $orderId . '&clear_cart=1');
