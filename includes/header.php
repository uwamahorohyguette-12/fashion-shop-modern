<?php
/** @var PDO $pdo */
$user = currentUser($pdo);
$collections = getCollections($pdo);
$pageTitle = $pageTitle ?? SITE_NAME . ' E-Commerce';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="Discover KigaliThreads, a Rwandan online store offering fashion for all ages.">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .font-serif { font-family: 'Playfair Display', serif; }
  </style>
</head>
<body class="min-h-screen flex flex-col bg-white text-gray-900">
<header class="sticky top-0 z-40 bg-white border-b border-gray-100">
  <div class="bg-black text-white text-center text-xs py-2 tracking-widest">
    FREE DELIVERY ACROSS RWANDA — PAY WITH MOBILE MONEY
  </div>
  <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
    <button type="button" class="md:hidden" id="mobile-menu-open" aria-label="Open menu">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <a href="<?= url() ?>" class="font-serif text-2xl tracking-tight">
      Kigali<span class="text-[#D4AF37]">Thread</span>
    </a>

<a href="<?= url() ?>" class="font-serif text-2xl tracking-tight">
      Home<span class="text-[#D4AF37]"></span>
    </a>







    <nav class="hidden md:flex items-center gap-7 text-sm font-medium">
      <?php foreach ($collections as $col): ?>
        <a href="<?= url('collection.php?handle=' . urlencode($col['handle'])) ?>" class="relative group py-1">
          <?= e($col['title']) ?>
          <span class="absolute left-0 -bottom-0.5 w-0 h-0.5 bg-[#D4AF37] group-hover:w-full transition-all"></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="flex items-center gap-4">
      <button type="button" id="search-toggle" aria-label="Search">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </button>
      <?php if ($user): ?>
        <div class="relative group">
          <button type="button" class="flex items-center" aria-label="Account">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </button>
          <div class="absolute right-0 mt-2 w-44 bg-white border rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
            <div class="px-4 py-2 text-xs text-gray-500 border-b"><?= e($user['full_name'] ?: $user['email']) ?></div>
            <a href="<?= url('account.php') ?>" class="block px-4 py-2 text-sm hover:bg-gray-50">My Orders</a>
            <?php if ((int) $user['is_admin']): ?>
              <a href="<?= url('admin/') ?>" class="block px-4 py-2 text-sm hover:bg-gray-50">Admin Dashboard</a>
            <?php endif; ?>
            <a href="<?= url('logout.php') ?>" class="block px-4 py-2 text-sm hover:bg-gray-50">Sign out</a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= url('login.php') ?>" aria-label="Login">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </a>
      <?php endif; ?>
      <button type="button" id="cart-open" class="relative" aria-label="Cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        <span id="cart-count" class="absolute -top-2 -right-2 bg-[#D4AF37] text-black text-[10px] font-bold h-4 min-w-4 px-1 rounded-full flex items-center justify-center hidden">0</span>
      </button>
    </div>
  </div>

  <form id="search-bar" action="<?= url('products.php') ?>" method="get" class="hidden border-t border-gray-100 p-4 max-w-7xl mx-auto">
    <input type="search" name="q" placeholder="Search for products..." class="w-full border-b-2 border-black py-2 outline-none text-lg">
  </form>
</header>

<div id="mobile-menu" class="fixed inset-0 z-50 bg-white hidden md:hidden">
  <div class="flex items-center justify-between p-4 border-b">
    <span class="font-serif text-xl">Menu</span>
    <button type="button" id="mobile-menu-close" aria-label="Close menu">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  <nav class="flex flex-col p-4 gap-1">
    <?php foreach ($collections as $col): ?>
      <a href="<?= url('collection.php?handle=' . urlencode($col['handle'])) ?>" class="py-3 border-b text-lg"><?= e($col['title']) ?></a>
    <?php endforeach; ?>
    <a href="<?= url('products.php') ?>" class="py-3 border-b text-lg">Shop All</a>
  </nav>
</div>

<div id="cart-drawer" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" id="cart-backdrop"></div>
  <aside class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col">
    <div class="flex items-center justify-between p-4 border-b">
      <h2 class="font-serif text-xl">Your Bag</h2>
      <button type="button" id="cart-close" aria-label="Close cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div id="cart-drawer-items" class="flex-1 overflow-y-auto p-4 space-y-4"></div>
    <div class="border-t p-4">
      <div class="flex justify-between font-semibold mb-4">
        <span>Subtotal</span>
        <span id="cart-drawer-subtotal">0 RWF</span>
      </div>
      <a href="<?= url('cart.php') ?>" class="block text-center border py-3 rounded mb-2 hover:bg-gray-50">View Bag</a>
      <a href="<?= url('checkout.php') ?>" class="block text-center bg-black text-white py-3 rounded hover:bg-[#D4AF37] hover:text-black transition-colors">Checkout</a>
    </div>
  </aside>
</div>

<main class="flex-1">
