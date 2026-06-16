<?php
require_once __DIR__ . '/includes/init.php';

$searchId = strtoupper(trim($_GET['order_id'] ?? ''));
$order    = null;
$items    = [];
$error    = '';

if ($searchId !== '') {
    $order = findOrderByDisplayId($pdo, $searchId);
    if (!$order) {
        $error = 'No order found with that ID. Please check and try again.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM ecom_order_items WHERE order_id = ?');
        $stmt->execute([(int) $order['id']]);
        $items = $stmt->fetchAll();
    }
}

$pageTitle = 'Track Your Order — KigaliThreads';
require __DIR__ . '/includes/header.php';

$statuses = ['pending', 'paid', 'shipped', 'delivered'];
$labels   = ['Order Placed', 'Payment Confirmed', 'Shipped', 'Delivered'];
$descs    = [
    'pending'  => 'Your order has been received and is awaiting payment confirmation.',
    'paid'     => 'Payment confirmed. We are preparing your order.',
    'shipped'  => 'Your order is on its way! Expected delivery in 1–3 business days.',
    'delivered'=> 'Your order has been delivered. Enjoy your purchase!',
    'cancelled'=> 'This order has been cancelled.',
    'refunded' => 'A refund has been issued for this order.',
];

$shipping = [];
if ($order && !empty($order['shipping_address'])) {
    $shipping = json_decode($order['shipping_address'], true) ?: [];
}
?>

<div class="max-w-2xl mx-auto px-4 py-14">

  <!-- Search form -->
  <div class="text-center mb-10">
    <h1 class="font-serif text-4xl mb-2">Track Your Order</h1>
    <p class="text-gray-500 text-sm mb-8">Enter the Order ID from your confirmation page or email.</p>

    <form method="get" action="<?= url('order-tracking.php') ?>" class="flex gap-2 max-w-md mx-auto">
      <input
        type="text"
        name="order_id"
        value="<?= e($searchId) ?>"
        placeholder="e.g. ECCBC87E"
        maxlength="8"
        required
        oninput="this.value=this.value.toUpperCase()"
        class="flex-1 border rounded-lg px-4 py-3 text-sm uppercase tracking-widest font-mono focus:outline-none focus:ring-2 focus:ring-black"
      >
      <button type="submit" class="bg-black text-white px-6 py-3 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors text-sm font-medium">
        Track
      </button>
    </form>

    <?php if ($error): ?>
      <p class="mt-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3 max-w-md mx-auto"><?= e($error) ?></p>
    <?php endif; ?>
  </div>

  <?php if ($order):
    $currentIdx  = array_search($order['status'], $statuses);
    if ($currentIdx === false) $currentIdx = -1;
    $isCancelled = in_array($order['status'], ['cancelled', 'refunded']);
    $badgeClass  = match($order['status']) {
        'delivered'            => 'bg-green-100 text-green-700',
        'shipped'              => 'bg-blue-100 text-blue-700',
        'paid'                 => 'bg-yellow-100 text-yellow-800',
        'cancelled','refunded' => 'bg-red-100 text-red-700',
        default                => 'bg-gray-100 text-gray-600',
    };
  ?>

    <!-- Order header -->
    <div class="flex items-center justify-between mb-6 border rounded-xl px-5 py-4 bg-white">
      <div>
        <p class="font-mono font-semibold text-lg">#<?= e(orderDisplayId((int) $order['id'])) ?></p>
        <p class="text-xs text-gray-400 mt-0.5"><?= e(date('F j, Y \a\t g:i A', strtotime($order['created_at']))) ?></p>
      </div>
      <span class="text-xs font-semibold px-3 py-1.5 rounded-full capitalize <?= $badgeClass ?>"><?= e($order['status']) ?></span>
    </div>

    <?php if ($isCancelled): ?>
      <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6 text-sm text-red-700 text-center">
        <?= e($descs[$order['status']]) ?>
      </div>
    <?php else: ?>

      <!-- Timeline -->
      <div class="bg-white border rounded-xl p-6 mb-6">
        <h2 class="font-medium mb-6">Order Progress</h2>
        <ol class="relative border-l border-gray-200 ml-3 space-y-8">
          <?php foreach ($statuses as $i => $s):
            $done    = $i <= $currentIdx;
            $current = $i === $currentIdx;
          ?>
            <li class="ml-6">
              <span class="absolute -left-3.5 flex items-center justify-center w-7 h-7 rounded-full border-2 text-xs font-bold
                <?= $done ? 'bg-black border-black text-white' : 'bg-white border-gray-300 text-gray-400' ?>">
                <?= $done ? '✓' : ($i + 1) ?>
              </span>
              <p class="font-medium text-sm <?= $done ? 'text-black' : 'text-gray-400' ?>"><?= $labels[$i] ?></p>
              <?php if ($current): ?>
                <p class="text-xs text-gray-500 mt-0.5"><?= e($descs[$s]) ?></p>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>

    <?php endif; ?>

    <!-- Items -->
    <div class="bg-white border rounded-xl p-6 mb-6">
      <h2 class="font-medium mb-4">Items Ordered</h2>
      <div class="divide-y">
        <?php foreach ($items as $item): ?>
          <div class="flex justify-between py-3 text-sm">
            <span class="text-gray-700">
              <?= e($item['product_name']) ?>
              <?= $item['variant_title'] ? '<span class="text-gray-400"> (' . e($item['variant_title']) . ')</span>' : '' ?>
              <span class="text-gray-400"> × <?= (int) $item['quantity'] ?></span>
            </span>
            <span class="font-medium"><?= formatRWF((int) $item['total']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="flex justify-between font-semibold mt-4 pt-3 border-t">
        <span>Total</span>
        <span><?= formatRWF((int) $order['total']) ?></span>
      </div>
    </div>

    <!-- Delivery address -->
    <?php if (!empty($shipping['address'])): ?>
      <div class="bg-white border rounded-xl p-6 mb-6">
        <h2 class="font-medium mb-3">Delivery Address</h2>
        <p class="text-sm font-medium"><?= e($shipping['name'] ?? '') ?></p>
        <p class="text-sm text-gray-500"><?= e($shipping['address']) ?>, <?= e($shipping['city'] ?? '') ?>, <?= e($shipping['country'] ?? '') ?></p>
        <?php if (!empty($shipping['phone'])): ?>
          <p class="text-sm text-gray-500"><?= e($shipping['phone']) ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Receipt button -->
    <div class="flex justify-end">
      <a href="<?= url('receipt.php?id=' . (int) $order['id']) ?>" target="_blank"
        class="inline-flex items-center gap-2 border px-5 py-2.5 rounded-lg text-sm hover:bg-black hover:text-white transition-colors">
        🧾 View / Print Receipt
      </a>
    </div>

  <?php endif; ?>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
