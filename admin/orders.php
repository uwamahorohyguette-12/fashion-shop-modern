<?php
require_once __DIR__ . '/includes/init.php';
$user = adminRequireAdmin($pdo);

$orderStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled', 'refunded'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $stmt = $pdo->prepare('UPDATE ecom_orders SET status = ? WHERE id = ?');
    $stmt->execute([$_POST['order_status'], (int) $_POST['order_id']]);
    adminFlash('Order status updated.');
    redirect('admin/orders.php?view=' . (int) $_POST['order_id']);
}

$viewOrder = null;
$viewItems = [];
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare(
        'SELECT o.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
         FROM ecom_orders o LEFT JOIN ecom_customers c ON c.id = o.customer_id WHERE o.id = ?'
    );
    $stmt->execute([(int) $_GET['view']]);
    $viewOrder = $stmt->fetch();
    if ($viewOrder) {
        $stmt = $pdo->prepare('SELECT * FROM ecom_order_items WHERE order_id = ?');
        $stmt->execute([(int) $viewOrder['id']]);
        $viewItems = $stmt->fetchAll();
    }
}

$orders = $pdo->query(
    'SELECT o.*, c.name AS customer_name, c.email AS customer_email
     FROM ecom_orders o LEFT JOIN ecom_customers c ON c.id = o.customer_id
     ORDER BY o.created_at DESC'
)->fetchAll();

$pageTitle = $viewOrder ? 'Order #' . orderDisplayId((int) $viewOrder['id']) : 'Orders';
$activeNav = 'orders';
require __DIR__ . '/includes/layout-header.php';
?>

<?php if ($viewOrder):
    $addr = json_decode($viewOrder['shipping_address'] ?? '{}', true) ?: [];
?>
  <a href="<?= adminUrl('orders.php') ?>" class="text-sm text-gray-500 hover:text-black mb-4 inline-block">&larr; Back to orders</a>
  <div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="font-mono text-lg">#<?= orderDisplayId((int) $viewOrder['id']) ?></h2>
          <p class="text-sm text-gray-500"><?= e(date('F j, Y g:i A', strtotime($viewOrder['created_at']))) ?></p>
        </div>
        <?= orderStatusBadge($viewOrder['status']) ?>
      </div>
      <h3 class="font-medium mb-3">Items</h3>
      <div class="divide-y">
        <?php foreach ($viewItems as $item): ?>
          <div class="flex justify-between py-3 text-sm">
            <span><?= e($item['product_name']) ?><?= $item['variant_title'] ? ' (' . e($item['variant_title']) . ')' : '' ?> × <?= (int) $item['quantity'] ?></span>
            <span class="font-medium"><?= formatRWF((int) $item['total']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="border-t mt-4 pt-4 space-y-2 text-sm">
        <div class="flex justify-between"><span>Subtotal</span><span><?= formatRWF((int) $viewOrder['subtotal']) ?></span></div>
        <div class="flex justify-between"><span>Shipping</span><span><?= formatRWF((int) $viewOrder['shipping']) ?></span></div>
        <div class="flex justify-between font-bold text-lg"><span>Total</span><span><?= formatRWF((int) $viewOrder['total']) ?></span></div>
      </div>
    </div>
    <div class="space-y-6">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-medium mb-3">Update Status</h3>
        <form method="post" class="space-y-3">
          <input type="hidden" name="order_id" value="<?= (int) $viewOrder['id'] ?>">
          <select name="order_status" class="w-full border rounded-lg px-3 py-2">
            <?php foreach ($orderStatuses as $s): ?>
              <option value="<?= $s ?>" <?= $viewOrder['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="w-full bg-black text-white py-2.5 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors">Update Status</button>
        </form>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-medium mb-3">Customer</h3>
        <p class="text-sm"><?= e($viewOrder['customer_name'] ?: $addr['name'] ?? '—') ?></p>
        <p class="text-sm text-gray-500"><?= e($viewOrder['customer_email'] ?: $addr['email'] ?? '') ?></p>
        <p class="text-sm text-gray-500"><?= e($viewOrder['customer_phone'] ?: $addr['phone'] ?? '') ?></p>
        <?php if (!empty($addr['address'])): ?>
          <p class="text-sm text-gray-500 mt-2"><?= e($addr['address']) ?>, <?= e($addr['city'] ?? '') ?></p>
        <?php endif; ?>
        <?php if ($viewOrder['notes']): ?>
          <p class="text-xs text-gray-400 mt-3"><?= e($viewOrder['notes']) ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="text-left px-5 py-3 font-medium text-gray-500">Order</th>
          <th class="text-left px-5 py-3 font-medium text-gray-500 hidden md:table-cell">Customer</th>
          <th class="text-left px-5 py-3 font-medium text-gray-500 hidden sm:table-cell">Date</th>
          <th class="text-left px-5 py-3 font-medium text-gray-500">Total</th>
          <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
          <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php foreach ($orders as $o): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-5 py-3 font-mono">#<?= orderDisplayId((int) $o['id']) ?></td>
            <td class="px-5 py-3 hidden md:table-cell text-gray-600"><?= e($o['customer_name'] ?: $o['customer_email'] ?: '—') ?></td>
            <td class="px-5 py-3 hidden sm:table-cell text-gray-500"><?= e(date('M j, Y', strtotime($o['created_at']))) ?></td>
            <td class="px-5 py-3 font-medium"><?= formatRWF((int) $o['total']) ?></td>
            <td class="px-5 py-3"><?= orderStatusBadge($o['status']) ?></td>
            <td class="px-5 py-3 text-right">
              <a href="<?= adminUrl('orders.php?view=' . (int) $o['id']) ?>" class="text-[#D4AF37] hover:underline">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$orders): ?>
          <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No orders yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
