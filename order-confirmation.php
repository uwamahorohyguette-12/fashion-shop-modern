<?php
require_once __DIR__ . '/includes/init.php';

$orderId = (int) ($_GET['id'] ?? 0);
$order = null;
$items = [];

if ($orderId) {
    $stmt = $pdo->prepare('SELECT * FROM ecom_orders WHERE id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order) {
        $stmt = $pdo->prepare('SELECT * FROM ecom_order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();
    }
}

$clearCart = isset($_GET['clear_cart']);
$pageTitle = 'Order Confirmation — KigaliThreads';
require __DIR__ . '/includes/header.php';

$shipping = [];
if ($order && !empty($order['shipping_address'])) {
    $shipping = json_decode($order['shipping_address'], true) ?: [];
}
?>

<div class="max-w-2xl mx-auto px-4 py-16 text-center">
  <div class="text-[#D4AF37] text-5xl mb-5">✓</div>
  <h1 class="font-serif text-4xl mb-2">Thank You!</h1>
  <p class="text-gray-500 mb-2">Your order has been placed and is awaiting confirmation.</p>
  <?php if ($orderId): ?>
    <p class="text-sm mb-2">Order ID: <span class="font-mono font-medium bg-gray-100 px-2 py-1 rounded tracking-widest"><?= orderDisplayId($orderId) ?></span></p>
    <p class="text-xs text-gray-400 mb-2">Save this ID to track your order anytime.</p>
    <a href="<?= url('order-tracking.php?order_id=' . orderDisplayId($orderId)) ?>" class="inline-block text-xs underline text-gray-500 mb-8 hover:text-black">Track this order &rarr;</a>
  <?php endif; ?>

  <?php if ($order): ?>
    <!-- Status tracker -->
    <?php
      $statuses = ['pending', 'paid', 'shipped', 'delivered'];
      $currentIdx = array_search($order['status'], $statuses);
      if ($currentIdx === false) $currentIdx = -1;
      $labels = ['Order Placed', 'Payment Confirmed', 'Shipped', 'Delivered'];
      $icons  = ['🛒', '💳', '📦', '✅'];
    ?>
    <div class="flex items-center justify-center gap-0 mb-8">
      <?php foreach ($statuses as $i => $s): ?>
        <?php $done = $i <= $currentIdx; ?>
        <div class="flex items-center">
          <div class="flex flex-col items-center">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold border-2 <?= $done ? 'bg-black border-black text-white' : 'border-gray-300 text-gray-400' ?>">
              <?= $done ? '✓' : ($i + 1) ?>
            </div>
            <span class="text-[10px] mt-1 w-16 text-center leading-tight <?= $done ? 'text-black font-medium' : 'text-gray-400' ?>"><?= $labels[$i] ?></span>
          </div>
          <?php if ($i < count($statuses) - 1): ?>
            <div class="w-10 h-0.5 mb-4 <?= $i < $currentIdx ? 'bg-black' : 'bg-gray-200' ?>"></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="bg-[#f5f5f5] rounded-lg p-6 text-left">
      <h2 class="font-medium mb-4">Order Details</h2>
      <?php foreach ($items as $i): ?>
        <div class="flex justify-between text-sm py-2 border-b last:border-0">
          <span><?= e($i['product_name']) ?><?= $i['variant_title'] ? ' (' . e($i['variant_title']) . ')' : '' ?> × <?= (int) $i['quantity'] ?></span>
          <span><?= formatRWF((int) $i['total']) ?></span>
        </div>
      <?php endforeach; ?>
      <div class="flex justify-between font-semibold text-lg mt-4 pt-3 border-t">
        <span>Total</span><span><?= formatRWF((int) $order['total']) ?></span>
      </div>
      <?php if (!empty($shipping['address'])): ?>
        <p class="text-sm text-gray-500 mt-4">Delivering to: <?= e($shipping['address']) ?>, <?= e($shipping['city'] ?? '') ?></p>
      <?php endif; ?>
      <div class="mt-4 pt-3 border-t">
        <span class="text-sm">Status: </span>
        <?php
          $badgeClass = match($order['status']) {
            'delivered' => 'bg-green-100 text-green-700',
            'shipped'   => 'bg-blue-100 text-blue-700',
            'paid'      => 'bg-yellow-100 text-yellow-800',
            'cancelled', 'refunded' => 'bg-red-100 text-red-700',
            default     => 'bg-gray-100 text-gray-600',
          };
        ?>
        <span class="text-xs font-medium px-2.5 py-1 rounded capitalize <?= $badgeClass ?>"><?= e($order['status']) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <div class="flex gap-3 justify-center mt-8 flex-wrap">
    <a href="<?= url('products.php') ?>" class="bg-black text-white px-6 py-3 rounded hover:bg-[#D4AF37] hover:text-black transition-colors">Continue Shopping</a>
    <a href="<?= url('order-tracking.php?order_id=' . orderDisplayId($orderId)) ?>" class="border px-6 py-3 rounded hover:bg-gray-50">Track Order</a>
    <a href="<?= url('receipt.php?id=' . $orderId) ?>" target="_blank" class="border px-6 py-3 rounded hover:bg-gray-50">🧾 Receipt</a>
    <a href="<?= url('account.php') ?>" class="border px-6 py-3 rounded hover:bg-gray-50">My Orders</a>
  </div>
</div>

<?php if ($clearCart): ?>
<script>document.addEventListener('DOMContentLoaded', () => Cart.clearCart());</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
