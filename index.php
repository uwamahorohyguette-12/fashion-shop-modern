<?php
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'KigaliThreads — Fashion Forward, Made in Kigali';
$allProducts = getActiveProducts($pdo);
$newArrivals = array_values(array_filter($allProducts, fn($p) => in_array('new', $p['tags'], true)));
$bestSellers = array_values(array_filter($allProducts, fn($p) => in_array('bestseller', $p['tags'], true)));
$newArrivals = array_slice($newArrivals, 0, 8);
$bestSellers = array_slice($bestSellers, 0, 8);

$hero = 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781082988050_b26ebcf8.png';
$categoryTiles = [
    ['handle' => 'women', 'title' => 'Women', 'img' => 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083138409_183be21e.jpg'],
    ['handle' => 'men', 'title' => 'Men', 'img' => 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083010568_48c6d093.png'],
    ['handle' => 'shoes', 'title' => 'Shoes', 'img' => 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083208276_ea2ca5ad.jpg'],
    ['handle' => 'accessories', 'title' => 'Accessories', 'img' => 'https://d64gsuwffb70l.cloudfront.net/6a292a97d5eafa2e198b412c_1781083296089_d207166b.jpg'],
];

require __DIR__ . '/includes/header.php';
?>

<section class="relative h-[78vh] min-h-[500px] w-full overflow-hidden">
  <img src="<?= e($hero) ?>" alt="KigaliThreads collection" class="absolute inset-0 h-full w-full object-cover">
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
  <div class="relative max-w-7xl mx-auto px-6 h-full flex flex-col justify-center">
    <p class="text-[#D4AF37] tracking-[0.3em] text-sm mb-4">NEW SEASON 2026</p>
    <h1 class="font-serif text-5xl md:text-7xl text-white max-w-2xl leading-tight">
      Fashion Forward, <br> Made in Kigali
    </h1>
    <p class="text-gray-200 mt-5 max-w-md text-lg">
      Discover premium clothing, shoes &amp; accessories for men, women and kids.
    </p>
    <div class="flex gap-4 mt-8 flex-wrap">
      <a href="<?= url('products.php') ?>" class="bg-[#D4AF37] text-black px-8 py-3.5 font-medium rounded hover:bg-white transition-colors inline-flex items-center gap-2">
        Shop Collection →
      </a>
      <a href="<?= url('collection.php?handle=new-arrivals') ?>" class="border border-white/60 text-white px-8 py-3.5 font-medium rounded hover:bg-white hover:text-black transition-colors">
        New Arrivals
      </a>
    </div>
  </div>
</section>

<section class="bg-black text-white">
  <div class="max-w-7xl mx-auto px-6 py-6 grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm text-center sm:text-left">
    <div class="flex items-center gap-3 justify-center sm:justify-start"><span class="text-[#D4AF37]">🚚</span> Free Delivery Across Rwanda</div>
    <div class="flex items-center gap-3 justify-center sm:justify-start"><span class="text-[#D4AF37]">📱</span> Pay with Mobile Money</div>
    <div class="flex items-center gap-3 justify-center sm:justify-start"><span class="text-[#D4AF37]">✓</span> Authentic Premium Quality</div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-16">
  <h2 class="font-serif text-3xl text-center mb-10">Shop by Category</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php foreach ($categoryTiles as $tile): ?>
      <a href="<?= url('collection.php?handle=' . urlencode($tile['handle'])) ?>" class="group relative aspect-[3/4] overflow-hidden rounded-lg">
        <img src="<?= e($tile['img']) ?>" alt="<?= e($tile['title']) ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-black/30 group-hover:bg-black/50 transition-colors flex items-end p-5">
          <span class="text-white font-serif text-2xl"><?= e($tile['title']) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($newArrivals): ?>
<section class="max-w-7xl mx-auto px-6 pb-16">
  <div class="flex items-end justify-between mb-8">
    <h2 class="font-serif text-3xl">New Arrivals</h2>
    <a href="<?= url('collection.php?handle=new-arrivals') ?>" class="text-sm underline hover:text-[#D4AF37]">View all</a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
    <?php foreach ($newArrivals as $product): ?>
      <?php require __DIR__ . '/includes/product-card.php'; ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="relative py-20 bg-[#0F0F0F] text-white text-center px-6">
  <p class="text-[#D4AF37] tracking-[0.3em] text-sm mb-3">EXCLUSIVE</p>
  <h2 class="font-serif text-4xl md:text-5xl mb-4">The Gold Standard Collection</h2>
  <p class="text-gray-400 max-w-xl mx-auto mb-7">Elevated essentials designed to last. Refined silhouettes, premium fabrics.</p>
  <a href="<?= url('collection.php?handle=best-sellers') ?>" class="inline-block bg-[#D4AF37] text-black px-8 py-3.5 font-medium rounded hover:bg-white transition-colors">
    Explore Best Sellers
  </a>
</section>

<?php if ($bestSellers): ?>
<section class="max-w-7xl mx-auto px-6 py-16">
  <div class="flex items-end justify-between mb-8">
    <h2 class="font-serif text-3xl">Best Sellers</h2>
    <a href="<?= url('collection.php?handle=best-sellers') ?>" class="text-sm underline hover:text-[#D4AF37]">View all</a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
    <?php foreach ($bestSellers as $product): ?>
      <?php require __DIR__ . '/includes/product-card.php'; ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
