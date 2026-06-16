<?php
require_once __DIR__ . '/includes/init.php';

$search = strtolower(trim($_GET['q'] ?? ''));
$catFilter = $_GET['cat'] ?? [];
if (!is_array($catFilter)) {
    $catFilter = $catFilter ? [$catFilter] : [];
}
$priceIdx = isset($_GET['price']) && $_GET['price'] !== '' ? (int) $_GET['price'] : null;
$sizeFilter = $_GET['size'] ?? [];
if (!is_array($sizeFilter)) {
    $sizeFilter = $sizeFilter ? [$sizeFilter] : [];
}
$sort = $_GET['sort'] ?? 'featured';

$priceRanges = [
    ['label' => 'Under 20,000 RWF', 'min' => 0, 'max' => 20000],
    ['label' => '20,000 – 40,000 RWF', 'min' => 20000, 'max' => 40000],
    ['label' => '40,000 – 60,000 RWF', 'min' => 40000, 'max' => 60000],
    ['label' => 'Over 60,000 RWF', 'min' => 60000, 'max' => PHP_INT_MAX],
];
$categories = ['Men', 'Women', 'Kids', 'Shoes', 'Accessories'];
$sizes = ['S', 'M', 'L', 'XL'];

$products = getActiveProducts($pdo);

if ($search) {
    $products = array_filter($products, fn($p) =>
        str_contains(strtolower($p['name']), $search) ||
        str_contains(strtolower($p['product_type'] ?? ''), $search) ||
        str_contains(strtolower($p['description'] ?? ''), $search)
    );
}
if ($catFilter) {
    $products = array_filter($products, fn($p) => in_array($p['product_type'], $catFilter, true));
}
if ($priceIdx !== null && isset($priceRanges[$priceIdx])) {
    $r = $priceRanges[$priceIdx];
    $products = array_filter($products, fn($p) => $p['price'] >= $r['min'] && $p['price'] < $r['max']);
}
if ($sizeFilter) {
    $products = array_filter($products, fn($p) =>
        array_intersect($sizeFilter, array_column($p['variants'] ?? [], 'option1'))
    );
}

$products = array_values($products);
if ($sort === 'price-asc') {
    usort($products, fn($a, $b) => $a['price'] <=> $b['price']);
} elseif ($sort === 'price-desc') {
    usort($products, fn($a, $b) => $b['price'] <=> $a['price']);
} elseif ($sort === 'name') {
    usort($products, fn($a, $b) => strcmp($a['name'], $b['name']));
}

$pageTitle = $search ? 'Results for "' . $search . '"' : 'Shop All — KigaliThreads';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="font-serif text-4xl mb-2"><?= $search ? 'Results for "' . e($search) . '"' : 'Shop All' ?></h1>
  <p class="text-gray-500 mb-8"><?= count($products) ?> products</p>

  <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-8">
    <aside class="space-y-7">
      <div class="font-medium">☰ Filters</div>
      <form method="get" class="space-y-7">
        <?php if ($search): ?><input type="hidden" name="q" value="<?= e($search) ?>"><?php endif; ?>

        <div>
          <h3 class="font-medium mb-3 text-sm uppercase tracking-wide">Category</h3>
          <?php foreach ($categories as $c): ?>
            <label class="flex items-center gap-2 py-1 text-sm cursor-pointer">
              <input type="checkbox" name="cat[]" value="<?= e($c) ?>" <?= in_array($c, $catFilter, true) ? 'checked' : '' ?>> <?= e($c) ?>
            </label>
          <?php endforeach; ?>
        </div>

        <div>
          <h3 class="font-medium mb-3 text-sm uppercase tracking-wide">Price</h3>
          <?php foreach ($priceRanges as $i => $r): ?>
            <label class="flex items-center gap-2 py-1 text-sm cursor-pointer">
              <input type="radio" name="price" value="<?= $i ?>" <?= $priceIdx === $i ? 'checked' : '' ?>> <?= e($r['label']) ?>
            </label>
          <?php endforeach; ?>
        </div>

        <div>
          <h3 class="font-medium mb-3 text-sm uppercase tracking-wide">Size</h3>
          <?php foreach ($sizes as $s): ?>
            <label class="flex items-center gap-2 py-1 text-sm cursor-pointer">
              <input type="checkbox" name="size[]" value="<?= e($s) ?>" <?= in_array($s, $sizeFilter, true) ? 'checked' : '' ?>> <?= e($s) ?>
            </label>
          <?php endforeach; ?>
        </div>

        <div>
          <h3 class="font-medium mb-3 text-sm uppercase tracking-wide">Sort</h3>
          <select name="sort" class="w-full border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
            <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name</option>
          </select>
        </div>

        <button type="submit" class="w-full bg-black text-white py-2.5 rounded text-sm hover:bg-[#D4AF37] hover:text-black transition-colors">Apply Filters</button>
      </form>
    </aside>

    <div>
      <?php if (!$products): ?>
        <p class="text-gray-500">No products match your filters.</p>
      <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-8">
          <?php foreach ($products as $product): ?>
            <?php require __DIR__ . '/includes/product-card.php'; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
