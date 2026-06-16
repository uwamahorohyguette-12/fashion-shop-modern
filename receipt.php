<?php
require_once __DIR__ . '/includes/init.php';

$orderId = (int) ($_GET['id'] ?? 0);
$order   = null;
$items   = [];

if ($orderId) {
    $stmt = $pdo->prepare(
        'SELECT o.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
         FROM ecom_orders o LEFT JOIN ecom_customers c ON c.id = o.customer_id
         WHERE o.id = ? LIMIT 1'
    );
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order) {
        $stmt = $pdo->prepare('SELECT * FROM ecom_order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();
    }
}

$shipping = [];
if ($order && !empty($order['shipping_address'])) {
    $shipping = json_decode($order['shipping_address'], true) ?: [];
}

$badgeColor = match($order['status'] ?? '') {
    'delivered' => '#16a34a',
    'shipped'   => '#2563eb',
    'paid'      => '#d97706',
    'cancelled','refunded' => '#dc2626',
    default     => '#6b7280',
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt #<?= $order ? orderDisplayId($orderId) : 'N/A' ?> — KigaliThreads</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .font-serif { font-family: 'Playfair Display', serif; }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
      .receipt-card { box-shadow: none !important; border: none !important; }
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">

  <!-- Action bar -->
  <div class="no-print max-w-2xl mx-auto flex items-center justify-between mb-6">
    <a href="<?= url('order-confirmation.php?id=' . $orderId) ?>" class="text-sm text-gray-500 hover:text-black">&larr; Back to Order</a>
    <button onclick="window.print()" class="bg-black text-white text-sm px-5 py-2.5 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors">
      🖨️ Print / Save as PDF
    </button>
  </div>

  <?php if (!$order): ?>
    <div class="max-w-2xl mx-auto text-center text-gray-500 py-20">Order not found.</div>
  <?php else: ?>

  <!-- Receipt card -->
  <div class="receipt-card max-w-2xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">

    <!-- Header -->
    <div class="bg-black text-white px-8 py-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="font-serif text-3xl">Kigali<span class="text-[#D4AF37]">Threads</span></h1>
          <p class="text-gray-400 text-xs mt-1">Premium Fashion — Made in Rwanda</p>
        </div>
        <div class="text-right">
          <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Receipt</p>
          <p class="font-mono text-lg font-semibold">#<?= orderDisplayId($orderId) ?></p>
        </div>
      </div>
    </div>

    <div class="px-8 py-7">

      <!-- Meta row -->
      <div class="flex flex-wrap gap-6 mb-7 pb-7 border-b text-sm">
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Date</p>
          <p class="font-medium"><?= e(date('F j, Y', strtotime($order['created_at']))) ?></p>
          <p class="text-gray-400 text-xs"><?= e(date('g:i A', strtotime($order['created_at']))) ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Payment</p>
          <p class="font-medium">MTN Mobile Money</p>
          <p class="text-gray-400 text-xs"><?= e($order['payment_ref'] ?? '') ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Status</p>
          <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded capitalize text-white" style="background:<?= $badgeColor ?>">
            <?= e($order['status']) ?>
          </span>
        </div>
      </div>

      <!-- Customer & Delivery -->
      <div class="grid grid-cols-2 gap-6 mb-7 pb-7 border-b text-sm">
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Bill To</p>
          <p class="font-semibold"><?= e($shipping['name'] ?? $order['customer_name'] ?? '—') ?></p>
          <p class="text-gray-500"><?= e($order['customer_email'] ?? '') ?></p>
          <p class="text-gray-500"><?= e($shipping['phone'] ?? $order['customer_phone'] ?? '') ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Ship To</p>
          <p class="font-semibold"><?= e($shipping['address'] ?? '—') ?></p>
          <p class="text-gray-500"><?= e(($shipping['city'] ?? '') . ($shipping['country'] ? ', ' . $shipping['country'] : '')) ?></p>
        </div>
      </div>

      <!-- Items table -->
      <table class="w-full text-sm mb-7">
        <thead>
          <tr class="border-b">
            <th class="text-left pb-2 text-xs text-gray-400 uppercase tracking-wide font-medium">Item</th>
            <th class="text-center pb-2 text-xs text-gray-400 uppercase tracking-wide font-medium">Qty</th>
            <th class="text-right pb-2 text-xs text-gray-400 uppercase tracking-wide font-medium">Unit Price</th>
            <th class="text-right pb-2 text-xs text-gray-400 uppercase tracking-wide font-medium">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($items as $item): ?>
            <tr>
              <td class="py-3 pr-4">
                <p class="font-medium"><?= e($item['product_name']) ?></p>
                <?php if ($item['variant_title']): ?>
                  <p class="text-xs text-gray-400"><?= e($item['variant_title']) ?></p>
                <?php endif; ?>
                <?php if ($item['sku']): ?>
                  <p class="text-xs text-gray-300">SKU: <?= e($item['sku']) ?></p>
                <?php endif; ?>
              </td>
              <td class="py-3 text-center text-gray-600"><?= (int) $item['quantity'] ?></td>
              <td class="py-3 text-right text-gray-600"><?= formatRWF((int) $item['unit_price']) ?></td>
              <td class="py-3 text-right font-medium"><?= formatRWF((int) $item['total']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Totals -->
      <div class="flex justify-end mb-8">
        <div class="w-64 space-y-2 text-sm">
          <div class="flex justify-between text-gray-500">
            <span>Subtotal</span><span><?= formatRWF((int) $order['subtotal']) ?></span>
          </div>
          <div class="flex justify-between text-gray-500">
            <span>Shipping</span>
            <span><?= (int) $order['shipping'] > 0 ? formatRWF((int) $order['shipping']) : 'Free' ?></span>
          </div>
          <?php if ((int) $order['tax'] > 0): ?>
            <div class="flex justify-between text-gray-500">
              <span>Tax</span><span><?= formatRWF((int) $order['tax']) ?></span>
            </div>
          <?php endif; ?>
          <div class="flex justify-between font-bold text-base border-t pt-2 mt-2">
            <span>Total</span><span><?= formatRWF((int) $order['total']) ?></span>
          </div>
        </div>
      </div>

      <!-- Footer note -->
      <div class="border-t pt-6 text-center text-xs text-gray-400 space-y-1">
        <p>Thank you for shopping with <strong class="text-gray-600">KigaliThreads</strong>!</p>
        <p>Questions? Contact us at <strong class="text-gray-600">support@kigalithreads.com</strong></p>
        <p class="mt-3 font-mono text-gray-300">Generated <?= e(date('F j, Y \a\t g:i A')) ?></p>
      </div>

    </div>
  </div>

  <?php endif; ?>
</body>
</html>
