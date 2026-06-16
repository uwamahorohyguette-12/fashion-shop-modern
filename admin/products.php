<?php
require_once __DIR__ . '/includes/init.php';
$user = adminRequireAdmin($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_product'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $baseHandle = trim($_POST['handle'] ?? '') ?: strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-'));
        // Ensure handle is unique (exclude current product on edit)
        $handle = $baseHandle;
        $suffix = 2;
        while (true) {
            $chk = $pdo->prepare('SELECT id FROM ecom_products WHERE handle = ? AND id != ?');
            $chk->execute([$handle, $id]);
            if (!$chk->fetch()) break;
            $handle = $baseHandle . '-' . $suffix++;
        }
        $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));

        // Resolve image: uploaded file takes priority over URL
        $imageValue = trim($_POST['images'] ?? '');
        if (!empty($_FILES['image_upload']['name'])) {
            $file = $_FILES['image_upload'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            if (!in_array($mime, $allowed, true)) {
                adminFlash('Invalid image type. Only JPG, PNG, WEBP, GIF allowed.', 'error');
                redirect('admin/products.php' . ($id ? '?edit=' . $id : '?action=add'));
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('prod_', true) . '.' . strtolower($ext);
            $dest = __DIR__ . '/../public/uploads/products/' . $filename;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                adminFlash('Failed to upload image.', 'error');
                redirect('admin/products.php' . ($id ? '?edit=' . $id : '?action=add'));
            }
            $imageValue = BASE_URL . '/public/uploads/products/' . $filename;
        }

        $payload = [
            $name,
            $handle,
            trim($_POST['description'] ?? ''),
            (int) ($_POST['price'] ?? 0),
            $_POST['product_type'] ?? 'Men',
            (int) ($_POST['inventory_qty'] ?? 10),
            json_encode($imageValue !== '' ? [$imageValue] : []),
            json_encode(array_values($tags)),
            $_POST['status'] ?? 'active',
            isset($_POST['has_variants']) ? 1 : 0,
        ];

        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE ecom_products SET name=?, handle=?, description=?, price=?, product_type=?, inventory_qty=?, images=?, tags=?, status=?, has_variants=? WHERE id=?'
            );
            $stmt->execute([...$payload, $id]);
            adminFlash('Product updated.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO ecom_products (name, handle, description, price, product_type, inventory_qty, images, tags, status, has_variants) VALUES (?,?,?,?,?,?,?,?,?,?)'
            );
            $stmt->execute($payload);
            adminFlash('Product added.');
        }
        redirect('admin/products.php');
    }

    if (isset($_POST['delete_product'])) {
        $stmt = $pdo->prepare('DELETE FROM ecom_products WHERE id = ?');
        $stmt->execute([(int) $_POST['delete_product']]);
        adminFlash('Product deleted.');
        redirect('admin/products.php');
    }
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM ecom_products WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editProduct = $stmt->fetch();
    if ($editProduct) {
        $editProduct['images'] = decodeJson($editProduct['images']);
        $editProduct['tags'] = decodeJson($editProduct['tags']);
    }
}

$showForm = isset($_GET['action']) && $_GET['action'] === 'add' || $editProduct;
$products = $pdo->query('SELECT * FROM ecom_products ORDER BY created_at DESC')->fetchAll();
foreach ($products as &$p) {
    $p['images'] = decodeJson($p['images']);
}
unset($p);

$pageTitle = 'Products';
$activeNav = 'products';
require __DIR__ . '/includes/layout-header.php';
?>

<?php if ($showForm): ?>
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
  <h2 class="font-semibold text-lg mb-4"><?= $editProduct ? 'Edit Product' : 'Add New Product' ?></h2>
  <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <input type="hidden" name="save_product" value="1">
    <input type="hidden" name="id" value="<?= (int) ($editProduct['id'] ?? 0) ?>">
    <input required name="name" placeholder="Product name" class="md:col-span-2 border rounded-lg px-4 py-2.5" value="<?= e($editProduct['name'] ?? '') ?>">
    <input name="handle" placeholder="URL handle (auto-generated if empty)" class="md:col-span-2 border rounded-lg px-4 py-2.5" value="<?= e($editProduct['handle'] ?? '') ?>">
    <div class="md:col-span-2">
      <div class="flex gap-2 mb-2" id="img-tabs">
        <button type="button" onclick="switchTab('url')" id="tab-url"
          class="text-sm px-3 py-1 rounded border bg-black text-white">Image URL</button>
        <button type="button" onclick="switchTab('upload')" id="tab-upload"
          class="text-sm px-3 py-1 rounded border">Upload File</button>
      </div>
      <!-- hidden field that carries the URL value on submit -->
      <input type="hidden" name="images" id="input-images">
      <div id="pane-url">
        <input type="url" id="input-url" placeholder="https://example.com/image.jpg"
          class="w-full border rounded-lg px-4 py-2.5"
          value="<?= e(!empty($editProduct['images'][0]) && !str_starts_with($editProduct['images'][0], BASE_URL . '/public/uploads') ? $editProduct['images'][0] : '') ?>">
      </div>
      <div id="pane-upload" class="hidden">
        <input type="file" name="image_upload" id="input-file" accept="image/*" class="w-full border rounded-lg px-4 py-2">
        <?php if (!empty($editProduct['images'][0]) && str_starts_with($editProduct['images'][0], BASE_URL . '/public/uploads')): ?>
          <p class="text-xs text-gray-500 mt-1">Current: <a href="<?= e($editProduct['images'][0]) ?>" target="_blank" class="underline">view image</a></p>
        <?php endif; ?>
      </div>
    </div>
    <script>
    var activeTab = 'url';
    function switchTab(tab) {
      activeTab = tab;
      document.getElementById('pane-url').classList.toggle('hidden', tab !== 'url');
      document.getElementById('pane-upload').classList.toggle('hidden', tab !== 'upload');
      document.getElementById('tab-url').className = 'text-sm px-3 py-1 rounded border ' + (tab==='url' ? 'bg-black text-white' : '');
      document.getElementById('tab-upload').className = 'text-sm px-3 py-1 rounded border ' + (tab==='upload' ? 'bg-black text-white' : '');
      // clear the other input when switching
      if (tab === 'upload') document.getElementById('input-url').value = '';
      if (tab === 'url') document.getElementById('input-file').value = '';
      document.getElementById('input-images').value = '';
    }
    // Before submit, copy URL field into the hidden images field
    document.querySelector('form').addEventListener('submit', function() {
      if (activeTab === 'url') {
        document.getElementById('input-images').value = document.getElementById('input-url').value.trim();
      }
    });
    <?php if (!empty($editProduct['images'][0]) && str_starts_with($editProduct['images'][0], BASE_URL . '/public/uploads')): ?>
    switchTab('upload');
    <?php else: ?>
    // pre-fill hidden field with existing URL value
    document.getElementById('input-images').value = document.getElementById('input-url').value;
    <?php endif; ?>
    </script>
    <textarea name="description" placeholder="Description" rows="3" class="md:col-span-2 border rounded-lg px-4 py-2.5"><?= e($editProduct['description'] ?? '') ?></textarea>
    <input required type="number" name="price" placeholder="Price (RWF)" class="border rounded-lg px-4 py-2.5" value="<?= (int) ($editProduct['price'] ?? 0) ?>">
    <input type="number" name="inventory_qty" placeholder="Inventory" class="border rounded-lg px-4 py-2.5" value="<?= (int) ($editProduct['inventory_qty'] ?? 10) ?>">
    <select name="product_type" class="border rounded-lg px-4 py-2.5">
      <?php foreach (['Men', 'Women', 'Kids', 'Shoes', 'Accessories'] as $c): ?>
        <option <?= ($editProduct['product_type'] ?? 'Men') === $c ? 'selected' : '' ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>
    <select name="status" class="border rounded-lg px-4 py-2.5">
      <?php foreach (['active', 'draft', 'archived'] as $s): ?>
        <option value="<?= $s ?>" <?= ($editProduct['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
    <input name="tags" placeholder="Tags (comma-separated: new, bestseller)" class="md:col-span-2 border rounded-lg px-4 py-2.5" value="<?= e(implode(', ', $editProduct['tags'] ?? [])) ?>">
    <label class="md:col-span-2 flex items-center gap-2 text-sm">
      <input type="checkbox" name="has_variants" value="1" <?= !empty($editProduct['has_variants']) ? 'checked' : '' ?>>
      Has size variants
    </label>
    <div class="md:col-span-2 flex gap-3">
      <button type="submit" class="bg-black text-white px-6 py-2.5 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors"><?= $editProduct ? 'Update' : 'Add' ?> Product</button>
      <a href="<?= adminUrl('products.php') ?>" class="border px-6 py-2.5 rounded-lg inline-flex items-center">Cancel</a>
    </div>
  </form>
</div>
<?php else: ?>
<div class="mb-6">
  <a href="<?= adminUrl('products.php?action=add') ?>" class="inline-flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors">
    + Add Product
  </a>
</div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b">
      <tr>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Product</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500 hidden md:table-cell">Category</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Price</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500 hidden sm:table-cell">Stock</th>
        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($products as $p): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              <?php if (!empty($p['images'][0])): ?>
                <img src="<?= e($p['images'][0]) ?>" class="h-10 w-10 object-cover rounded bg-gray-100" alt="">
              <?php endif; ?>
              <span class="font-medium"><?= e($p['name']) ?></span>
            </div>
          </td>
          <td class="px-5 py-3 hidden md:table-cell text-gray-500"><?= e($p['product_type']) ?></td>
          <td class="px-5 py-3"><?= formatRWF((int) $p['price']) ?></td>
          <td class="px-5 py-3 hidden sm:table-cell"><?= (int) $p['inventory_qty'] ?></td>
          <td class="px-5 py-3"><span class="text-xs capitalize px-2 py-1 rounded bg-gray-100"><?= e($p['status']) ?></span></td>
          <td class="px-5 py-3 text-right space-x-2">
            <a href="<?= url('product.php?handle=' . urlencode($p['handle'])) ?>" target="_blank" class="text-gray-400 hover:text-black text-xs">View</a>
            <a href="<?= adminUrl('products.php?edit=' . (int) $p['id']) ?>" class="text-[#D4AF37] hover:underline">Edit</a>
            <form method="post" class="inline" onsubmit="return confirm('Delete this product?')">
              <input type="hidden" name="delete_product" value="<?= (int) $p['id'] ?>">
              <button type="submit" class="text-red-500 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$products): ?>
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No products yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
