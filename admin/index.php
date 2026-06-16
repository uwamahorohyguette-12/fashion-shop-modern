<?php
require_once __DIR__ . '/includes/init.php';
$user = adminRequireAdmin($pdo);
$stats = adminStats($pdo);

$recentOrders = $pdo->query(
    'SELECT o.*, c.name AS customer_name, c.email AS customer_email
     FROM ecom_orders o
     LEFT JOIN ecom_customers c ON c.id = o.customer_id
     ORDER BY o.created_at DESC LIMIT 5'
)->fetchAll();

$lowStock = $pdo->query(
    "SELECT id, name, inventory_qty, status FROM ecom_products
     WHERE inventory_qty <= 5 AND status = 'active' ORDER BY inventory_qty ASC LIMIT 5"
)->fetchAll();

$pageTitle = 'Overview';
$activeNav = 'dashboard';
require __DIR__ . '/includes/layout-header.php';
?>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
  <?php
  $cards = [
      ['label' => 'Total Revenue', 'value' => formatRWF($stats['revenue']), 'sub' => 'All non-cancelled orders', 'color' => 'border-[#D4AF37]'],
      ['label' => 'Orders', 'value' => $stats['orders'], 'sub' => $stats['pending_orders'] . ' pending/paid', 'color' => 'border-blue-400'],
      ['label' => 'Products', 'value' => $stats['products'], 'sub' => $stats['active_products'] . ' active', 'color' => 'border-green-400'],
      ['label' => 'Customers', 'value' => $stats['customers'], 'sub' => $stats['users'] . ' registered users', 'color' => 'border-purple-400'],
  ];
  foreach ($cards as $card):
  ?>
    <div class="bg-white rounded-xl border-l-4 <?= $card['color'] ?> p-5 shadow-sm">
      <p class="text-sm text-gray-500"><?= e($card['label']) ?></p>
      <p class="text-2xl font-bold mt-1"><?= e((string) $card['value']) ?></p>
      <p class="text-xs text-gray-400 mt-1"><?= e($card['sub']) ?></p>
    </div>
  <?php endforeach; ?>
</div>

<div class="grid lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-semibold">Recent Orders</h2>
      <a href="<?= adminUrl('orders.php') ?>" class="text-sm text-[#D4AF37] hover:underline">View all</a>
    </div>
    <?php if (!$recentOrders): ?>
      <p class="text-gray-400 text-sm">No orders yet.</p>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($recentOrders as $o): ?>
          <a href="<?= adminUrl('orders.php?view=' . (int) $o['id']) ?>" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border">
            <div>
              <p class="font-mono text-sm">#<?= orderDisplayId((int) $o['id']) ?></p>
              <p class="text-xs text-gray-500"><?= e($o['customer_name'] ?: $o['customer_email'] ?: 'Guest') ?> · <?= e(date('M j, Y', strtotime($o['created_at']))) ?></p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-sm"><?= formatRWF((int) $o['total']) ?></p>
              <?= orderStatusBadge($o['status']) ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-semibold">Low Stock Alert</h2>
      <a href="<?= adminUrl('products.php') ?>" class="text-sm text-[#D4AF37] hover:underline">Manage products</a>
    </div>
    <?php if (!$lowStock): ?>
      <p class="text-gray-400 text-sm">All products are well stocked.</p>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($lowStock as $p): ?>
          <div class="flex items-center justify-between p-3 rounded-lg border">
            <p class="font-medium text-sm"><?= e($p['name']) ?></p>
            <span class="text-xs font-bold px-2 py-1 rounded <?= (int) $p['inventory_qty'] === 0 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' ?>">
              <?= (int) $p['inventory_qty'] ?> left
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="mt-6 grid sm:grid-cols-3 gap-4">
  <a href="<?= adminUrl('products.php?action=add') ?>" class="bg-black text-white rounded-xl p-5 text-center hover:bg-[#D4AF37] hover:text-black transition-colors">
    <span class="text-2xl block mb-1">+</span>
    <span class="font-medium">Add Product</span>
  </a>
  <a href="<?= adminUrl('orders.php') ?>" class="bg-white border rounded-xl p-5 text-center hover:border-[#D4AF37] transition-colors">
    <span class="text-2xl block mb-1">📦</span>
    <span class="font-medium">Manage Orders</span>
  </a>
  <a href="<?= adminUrl('collections.php?action=add') ?>" class="bg-white border rounded-xl p-5 text-center hover:border-[#D4AF37] transition-colors">
    <span class="text-2xl block mb-1">🏷</span>
    <span class="font-medium">Add Collection</span>
  </a>
</div>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
