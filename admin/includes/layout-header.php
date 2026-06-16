<?php
/** @var array $user */
/** @var string $pageTitle */
/** @var string $activeNav */
$flash = adminGetFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Admin') ?> — KigaliThreads</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .font-serif { font-family: 'Playfair Display', serif; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="flex min-h-screen">
  <aside class="w-64 bg-[#0F0F0F] text-white flex flex-col shrink-0">
    <div class="p-6 border-b border-white/10">
      <a href="<?= adminUrl() ?>" class="font-serif text-xl block">
        Kigali<span class="text-[#D4AF37]">Threads</span>
      </a>
      <p class="text-xs text-gray-400 mt-1">Admin Dashboard</p>
    </div>
    <nav class="flex-1 p-4 space-y-1">
      <?php
      $nav = [
          'dashboard' => ['label' => 'Overview', 'href' => adminUrl(), 'icon' => '📊'],
          'products' => ['label' => 'Products', 'href' => adminUrl('products.php'), 'icon' => '👕'],
          'orders' => ['label' => 'Orders', 'href' => adminUrl('orders.php'), 'icon' => '📦'],
          'customers' => ['label' => 'Customers', 'href' => adminUrl('customers.php'), 'icon' => '👥'],
          'collections' => ['label' => 'Collections', 'href' => adminUrl('collections.php'), 'icon' => '🏷'],
          'users' => ['label' => 'Users', 'href' => adminUrl('users.php'), 'icon' => '🔐'],
      ];
      foreach ($nav as $key => $item):
          $active = ($activeNav ?? '') === $key;
      ?>
        <a href="<?= $item['href'] ?>"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-colors <?= $active ? 'bg-[#D4AF37] text-black font-medium' : 'text-gray-300 hover:bg-white/10 hover:text-white' ?>">
          <span><?= $item['icon'] ?></span> <?= e($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="p-4 border-t border-white/10 space-y-2">
      <p class="text-xs text-gray-400 truncate px-2"><?= e($user['full_name'] ?: $user['email']) ?></p>
      <a href="<?= url() ?>" class="block text-center text-sm text-gray-400 hover:text-white py-2">← View Store</a>
      <a href="<?= url('logout.php') ?>" class="block text-center text-sm bg-white/10 hover:bg-white/20 py-2 rounded">Sign Out</a>
    </div>
  </aside>

  <main class="flex-1 overflow-auto">
    <header class="bg-white border-b px-8 py-5 flex items-center justify-between sticky top-0 z-10">
      <h1 class="font-serif text-2xl"><?= e($pageTitle ?? 'Dashboard') ?></h1>
      <span class="text-xs bg-[#D4AF37]/20 text-[#8a7020] px-3 py-1 rounded-full font-medium">Admin</span>
    </header>
    <div class="p-8">
      <?php if ($flash): ?>
        <div class="mb-6 px-4 py-3 rounded-lg text-sm <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' ?>">
          <?= e($flash['msg']) ?>
        </div>
      <?php endif; ?>
