<?php
require_once __DIR__ . '/includes/init.php';

$handle = trim($_GET['handle'] ?? '');
$product = $handle ? getProductByHandle($pdo, $handle) : null;

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="max-w-7xl mx-auto px-4 py-20"><p>Product not found.</p><a href="' . url('products.php') . '" class="underline">Back to shop</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = e($product['name']) . ' — KigaliThreads';
$hasVariants = $product['has_variants'] && !empty($product['variants']);
$sizes = array_values(array_unique(array_filter(array_column($product['variants'], 'option1'))));
$image = $product['images'][0] ?? '';

require __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-10">
  <a href="<?= url('products.php') ?>" class="text-sm text-gray-400 hover:text-black">&larr; Back to shop</a>
  <div class="grid md:grid-cols-2 gap-12 mt-6">
    <div class="bg-[#f5f5f5] rounded-lg overflow-hidden">
      <?php if ($image): ?>
        <img src="<?= e($image) ?>" alt="<?= e($product['name']) ?>" class="w-full object-cover">
      <?php endif; ?>
    </div>
    <div>
      <p class="text-xs uppercase tracking-widest text-gray-400"><?= e($product['product_type']) ?></p>
      <h1 class="font-serif text-4xl mt-2"><?= e($product['name']) ?></h1>
      <p class="text-2xl font-semibold mt-4" id="product-price"><?= formatRWF((int) $product['price']) ?></p>
      <p class="text-gray-600 mt-5 leading-relaxed"><?= e($product['description'] ?? '') ?></p>

      <?php if ($hasVariants): ?>
        <div class="mt-7">
          <label class="block font-medium mb-3 text-sm uppercase tracking-wide">Select Size</label>
          <div class="flex flex-wrap gap-2" id="size-buttons">
            <?php foreach ($sizes as $s):
              $variant = null;
              foreach ($product['variants'] as $v) {
                  if ($v['option1'] === $s) {
                      $variant = $v;
                      break;
                  }
              }
              $ok = !$variant || $variant['inventory_qty'] === null || (int) $variant['inventory_qty'] > 0;
            ?>
              <button type="button"
                class="size-btn min-w-[3rem] px-4 py-2 border rounded font-medium transition-all <?= $ok ? 'border-gray-300 hover:border-black' : 'border-gray-200 text-gray-300 cursor-not-allowed' ?>"
                data-size="<?= e($s) ?>"
                data-variant-id="<?= (int) ($variant['id'] ?? 0) ?>"
                data-variant-title="<?= e($variant['title'] ?? $s) ?>"
                data-price="<?= (int) ($variant['price'] ?? $product['price']) ?>"
                data-sku="<?= e($variant['sku'] ?? $product['handle']) ?>"
                <?= $ok ? '' : 'disabled' ?>>
                <?= e($s) ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="mt-7 flex items-center gap-4">
        <div class="flex items-center border rounded">
          <button type="button" class="p-3" id="qty-minus">−</button>
          <span class="px-4" id="qty-display">1</span>
          <button type="button" class="p-3" id="qty-plus">+</button>
        </div>
      </div>

      <button type="button" id="add-to-bag"
        class="w-full mt-6 py-4 bg-black text-white rounded font-medium hover:bg-[#D4AF37] hover:text-black transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        data-product-id="<?= (int) $product['id'] ?>"
        data-name="<?= e($product['name']) ?>"
        data-image="<?= e($image) ?>"
        data-price="<?= (int) $product['price'] ?>"
        data-sku="<?= e($product['handle']) ?>"
        data-has-variants="<?= $hasVariants ? '1' : '0' ?>">
        Add to Bag
      </button>

      <p class="mt-6 text-sm text-gray-500">🚚 Free delivery across Rwanda • Pay with Mobile Money</p>
    </div>
  </div>
</div>

<script>
(function () {
  let quantity = 1;
  let selectedSize = '';
  let selectedVariant = null;
  const hasVariants = <?= $hasVariants ? 'true' : 'false' ?>;

  const qtyDisplay = document.getElementById('qty-display');
  const priceEl = document.getElementById('product-price');
  const addBtn = document.getElementById('add-to-bag');

  function formatRWF(n) {
    return new Intl.NumberFormat('en-RW', { maximumFractionDigits: 0 }).format(n) + ' RWF';
  }

  function updateBtn() {
    if (hasVariants && !selectedSize) {
      addBtn.textContent = 'Select a Size';
      addBtn.disabled = true;
    } else {
      addBtn.textContent = 'Add to Bag';
      addBtn.disabled = false;
    }
  }

  document.getElementById('qty-minus')?.addEventListener('click', () => {
    quantity = Math.max(1, quantity - 1);
    qtyDisplay.textContent = quantity;
  });
  document.getElementById('qty-plus')?.addEventListener('click', () => {
    quantity += 1;
    qtyDisplay.textContent = quantity;
  });

  document.querySelectorAll('.size-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      if (btn.disabled) return;
      document.querySelectorAll('.size-btn').forEach((b) => {
        b.classList.remove('bg-black', 'text-white', 'border-black');
      });
      btn.classList.add('bg-black', 'text-white', 'border-black');
      selectedSize = btn.dataset.size;
      selectedVariant = {
        id: btn.dataset.variantId,
        title: btn.dataset.variantTitle,
        price: parseInt(btn.dataset.price, 10),
        sku: btn.dataset.sku,
      };
      priceEl.textContent = formatRWF(selectedVariant.price);
      updateBtn();
    });
  });

  addBtn?.addEventListener('click', () => {
    if (hasVariants && !selectedSize) return;
    const item = {
      product_id: addBtn.dataset.productId,
      name: addBtn.dataset.name,
      image: addBtn.dataset.image,
      price: selectedVariant ? selectedVariant.price : parseInt(addBtn.dataset.price, 10),
      sku: selectedVariant ? selectedVariant.sku : addBtn.dataset.sku,
    };
    if (selectedVariant) {
      item.variant_id = selectedVariant.id;
      item.variant_title = selectedVariant.title;
    }
    Cart.addToCart(item, quantity);
    addBtn.textContent = 'Added to Bag ✓';
    setTimeout(() => updateBtn(), 1500);
  });

  updateBtn();
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
