<?php
require_once __DIR__ . '/includes/init.php';

$handle = trim($_GET['handle'] ?? '');
$collection = $handle ? getCollectionByHandle($pdo, $handle) : null;
$products = $handle ? getCollectionProducts($pdo, $handle) : [];

if (!$collection) {
    http_response_code(404);
    $pageTitle = 'Collection Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="max-w-7xl mx-auto px-4 py-20"><p>Collection not found.</p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = e($collection['title']) . ' — KigaliThreads';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="font-serif text-4xl mb-2"><?= e($collection['title']) ?></h1>
  <?php if (!empty($collection['description'])): ?>
    <p class="text-gray-500 mb-8 max-w-xl"><?= e($collection['description']) ?></p>
  <?php endif; ?>
  <p class="text-gray-400 text-sm mb-8"><?= count($products) ?> products</p>

  <?php if (!$products): ?>
    <p class="text-gray-500">No products in this collection yet.</p>
  <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-8">
      <?php foreach ($products as $product): ?>
        <?php require __DIR__ . '/includes/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
