<?php
/** @var array $product */
$image = $product['images'][0] ?? '';
$hasVariants = $product['has_variants'] && !empty($product['variants']);
$isNew = in_array('new', $product['tags'] ?? [], true);
?>
<article class="group block product-card" data-product-id="<?= (int) $product['id'] ?>" data-has-variants="<?= $hasVariants ? '1' : '0' ?>">
  <a href="<?= url('product.php?handle=' . urlencode($product['handle'])) ?>" class="block">
    <div class="relative overflow-hidden rounded-lg bg-[#f5f5f5] aspect-[4/5]">
      <?php if ($image): ?>
        <img src="<?= e($image) ?>" alt="<?= e($product['name']) ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
      <?php endif; ?>
      <?php if ($isNew): ?>
        <span class="absolute left-3 top-3 bg-[#D4AF37] text-black text-[10px] font-bold tracking-widest px-2 py-1 rounded">NEW</span>
      <?php endif; ?>
      <button type="button"
        class="quick-add-btn absolute bottom-3 right-3 bg-black text-white p-3 rounded-full opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all hover:bg-[#D4AF37] hover:text-black"
        data-product='<?= e(json_encode([
          'product_id' => (string) $product['id'],
          'name' => $product['name'],
          'sku' => $product['handle'],
          'price' => (int) $product['price'],
          'image' => $image,
          'handle' => $product['handle'],
        ])) ?>'
        aria-label="Add to cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
      </button>
    </div>
    <div class="mt-3">
      <p class="text-[11px] uppercase tracking-widest text-gray-400"><?= e($product['product_type']) ?></p>
      <h3 class="font-medium text-sm text-gray-900 mt-1 group-hover:text-[#D4AF37] transition-colors"><?= e($product['name']) ?></h3>
      <p class="mt-1 font-semibold text-gray-900"><?= formatRWF((int) $product['price']) ?></p>
    </div>
  </a>
</article>
