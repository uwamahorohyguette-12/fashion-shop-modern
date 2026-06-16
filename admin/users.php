<?php
require_once __DIR__ . '/includes/init.php';
$user = adminRequireAdmin($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_admin'])) {
        $targetId = (int) $_POST['user_id'];
        if ($targetId === (int) $user['id']) {
            adminFlash('You cannot remove your own admin access.', 'error');
        } else {
            $stmt = $pdo->prepare('UPDATE users SET is_admin = NOT is_admin WHERE id = ?');
            $stmt->execute([$targetId]);
            adminFlash('User admin status updated.');
        }
        redirect('admin/users.php');
    }

    if (isset($_POST['delete_user'])) {
        $targetId = (int) $_POST['delete_user'];
        if ($targetId === (int) $user['id']) {
            adminFlash('You cannot delete your own account.', 'error');
        } else {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$targetId]);
            adminFlash('User deleted.');
        }
        redirect('admin/users.php');
    }
}

$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Users';
$activeNav = 'users';
require __DIR__ . '/includes/layout-header.php';
?>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left px-5 py-3 font-medium text-gray-500">User</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500 hidden md:table-cell">Email</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Role</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500 hidden sm:table-cell">Joined</th>
        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($users as $u): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-5 py-3 font-medium"><?= e($u['full_name'] ?: '—') ?></td>
          <td class="px-5 py-3 hidden md:table-cell text-gray-600"><?= e($u['email']) ?></td>
          <td class="px-5 py-3">
            <?php if ((int) $u['is_admin']): ?>
              <span class="text-xs bg-[#D4AF37]/20 text-[#8a7020] px-2 py-1 rounded font-medium">Admin</span>
            <?php else: ?>
              <span class="text-xs bg-gray-100 px-2 py-1 rounded">Customer</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 hidden sm:table-cell text-gray-500"><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td>
          <td class="px-5 py-3 text-right space-x-2">
            <?php if ((int) $u['id'] !== (int) $user['id']): ?>
              <form method="post" class="inline">
                <input type="hidden" name="toggle_admin" value="1">
                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                <button type="submit" class="text-[#D4AF37] hover:underline">
                  <?= (int) $u['is_admin'] ? 'Remove Admin' : 'Make Admin' ?>
                </button>
              </form>
              <form method="post" class="inline" onsubmit="return confirm('Delete this user?')">
                <input type="hidden" name="delete_user" value="<?= (int) $u['id'] ?>">
                <button type="submit" class="text-red-500 hover:underline">Delete</button>
              </form>
            <?php else: ?>
              <span class="text-xs text-gray-400">You</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
