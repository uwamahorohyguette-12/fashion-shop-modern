<?php
require_once __DIR__ . '/includes/init.php';

$user = currentUser($pdo);
if (!$user) {
    redirect('login.php?redirect=' . urlencode(url('admin/')));
}

if (!(int) $user['is_admin'] && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['become_admin'])) {
    $stmt = $pdo->prepare('UPDATE users SET is_admin = 1 WHERE id = ?');
    $stmt->execute([$user['id']]);
    $_SESSION['is_admin'] = 1;
    adminFlash('Admin access granted.');
    redirect('admin/');
}

if ((int) $user['is_admin']) {
    redirect('admin/');
}

$pageTitle = 'Admin Access Required';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Access — KigaliThreads</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-8 text-center">
    <div class="text-5xl mb-4">🛡</div>
    <h1 class="text-2xl font-bold mb-2">Admin Access Required</h1>
    <p class="text-gray-500 mb-6">Signed in as <strong><?= e($user['email']) ?></strong>. You need admin rights to access the dashboard.</p>
    <form method="post">
      <input type="hidden" name="become_admin" value="1">
      <button type="submit" class="w-full bg-black text-white py-3 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors font-medium">
        Grant Admin Access
      </button>
    </form>
    <a href="<?= url() ?>" class="block mt-4 text-sm text-gray-400 hover:text-black">← Back to store</a>
  </div>
</body>
</html>
