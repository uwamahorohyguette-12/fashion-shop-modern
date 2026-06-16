<?php
require_once __DIR__ . '/includes/init.php';
$user = adminRequireAdmin($pdo);

$customers = $pdo->query(
    'SELECT c.*,
            (SELECT COUNT(*) FROM ecom_orders o WHERE o.customer_id = c.id) AS order_count,
            (SELECT COALESCE(SUM(o.total),0) FROM ecom_orders o WHERE o.customer_id = c.id AND o.status NOT IN (\'cancelled\',\'refunded\')) AS total_spent
     FROM ecom_customers c ORDER BY c.id DESC'
)->fetchAll();

$pageTitle = 'Customers';
$activeNav = 'customers';
require __DIR__ . '/includes/layout-header.php';
?>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Name</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Email</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500 hidden md:table-cell">Phone</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Orders</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Total Spent</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($customers as $c): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-5 py-3 font-medium"><?= e($c['name'] ?: '—') ?></td>
          <td class="px-5 py-3 text-gray-600"><?= e($c['email']) ?></td>
          <td class="px-5 py-3 hidden md:table-cell text-gray-500"><?= e($c['phone'] ?: '—') ?></td>
          <td class="px-5 py-3"><?= (int) $c['order_count'] ?></td>
          <td class="px-5 py-3 font-medium"><?= formatRWF((int) $c['total_spent']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$customers): ?>
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No customers yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
