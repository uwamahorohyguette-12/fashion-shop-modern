<?php
require_once __DIR__ . '/includes/init.php';
requireLogin();

$user = currentUser($pdo);
$orders = [];

$stmt = $pdo->prepare('SELECT id FROM ecom_customers WHERE email = ? LIMIT 1');
$stmt->execute([$user['email']]);
$customer = $stmt->fetch();

if ($customer) {
    $stmt = $pdo->prepare('SELECT * FROM ecom_orders WHERE customer_id = ? ORDER BY created_at DESC');
    $stmt->execute([$customer['id']]);
    $orders = $stmt->fetchAll();
}

$pageTitle = 'My Account — KigaliThreads';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-12">
  <h1 class="font-serif text-4xl mb-1">My Account</h1>
  <p class="text-gray-500 mb-8"><?= e($user['full_name'] ?: $user['email']) ?></p>

  <h2 class="font-medium text-lg mb-4">Order History</h2>
  <?php if (!$orders): ?>
    <p class="text-gray-500">You have no orders yet. <a href="<?= url('products.php') ?>" class="underline">Shop now</a></p>
  <?php else: ?>
    <div class="space-y-4">
      <?php foreach ($orders as $o): ?>
        <div class="flex justify-between items-center border rounded-lg p-4 hover:bg-gray-50">
          <a href="<?= url('order-tracking.php?order_id=' . orderDisplayId((int) $o['id'])) ?>" class="flex-1">
            <p class="font-mono text-sm">#<?= orderDisplayId((int) $o['id']) ?></p>
            <p class="text-xs text-gray-500"><?= e(date('M j, Y', strtotime($o['created_at']))) ?>
              · <span class="capitalize <?= match($o['status']) {
                    'delivered' => 'text-green-600',
                    'shipped'   => 'text-blue-600',
                    'paid'      => 'text-yellow-600',
                    'cancelled','refunded' => 'text-red-500',
                    default     => 'text-gray-500'
                  } ?>"><?= e($o['status']) ?></span>
            </p>
          </a>
          <div class="flex items-center gap-3">
            <span class="font-semibold text-sm"><?= formatRWF((int) $o['total']) ?></span>
            <a href="<?= url('order-tracking.php?order_id=' . orderDisplayId((int) $o['id'])) ?>" class="text-xs border px-3 py-1.5 rounded hover:bg-black hover:text-white transition-colors">Track</a>
            <a href="<?= url('receipt.php?id=' . (int) $o['id']) ?>" target="_blank" class="text-xs border px-3 py-1.5 rounded hover:bg-black hover:text-white transition-colors">🧾 Receipt</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
