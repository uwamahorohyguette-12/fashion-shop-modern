<?php
require_once __DIR__ . '/includes/init.php';
$user = adminRequireAdmin($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_collection'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $handle = trim($_POST['handle'] ?? '') ?: strtolower(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-'));
        $payload = [$title, $handle, trim($_POST['description'] ?? ''), trim($_POST['image'] ?? ''), isset($_POST['is_visible']) ? 1 : 0];

        if ($id) {
            $stmt = $pdo->prepare('UPDATE ecom_collections SET title=?, handle=?, description=?, image=?, is_visible=? WHERE id=?');
            $stmt->execute([...$payload, $id]);
            adminFlash('Collection updated.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO ecom_collections (title, handle, description, image, is_visible) VALUES (?,?,?,?,?)');
            $stmt->execute($payload);
            adminFlash('Collection added.');
        }
        redirect('admin/collections.php');
    }

    if (isset($_POST['delete_collection'])) {
        $stmt = $pdo->prepare('DELETE FROM ecom_collections WHERE id = ?');
        $stmt->execute([(int) $_POST['delete_collection']]);
        adminFlash('Collection deleted.');
        redirect('admin/collections.php');
    }
}

$editCollection = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM ecom_collections WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editCollection = $stmt->fetch();
}

$showForm = (isset($_GET['action']) && $_GET['action'] === 'add') || $editCollection;
$collections = $pdo->query('SELECT * FROM ecom_collections ORDER BY title')->fetchAll();

$pageTitle = 'Collections';
$activeNav = 'collections';
require __DIR__ . '/includes/layout-header.php';
?>

<?php if ($showForm): ?>
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
  <h2 class="font-semibold text-lg mb-4"><?= $editCollection ? 'Edit Collection' : 'Add Collection' ?></h2>
  <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <input type="hidden" name="save_collection" value="1">
    <input type="hidden" name="id" value="<?= (int) ($editCollection['id'] ?? 0) ?>">
    <input required name="title" placeholder="Title" class="border rounded-lg px-4 py-2.5" value="<?= e($editCollection['title'] ?? '') ?>">
    <input name="handle" placeholder="Handle (URL slug)" class="border rounded-lg px-4 py-2.5" value="<?= e($editCollection['handle'] ?? '') ?>">
    <input name="image" placeholder="Image URL" class="md:col-span-2 border rounded-lg px-4 py-2.5" value="<?= e($editCollection['image'] ?? '') ?>">
    <textarea name="description" placeholder="Description" rows="2" class="md:col-span-2 border rounded-lg px-4 py-2.5"><?= e($editCollection['description'] ?? '') ?></textarea>
    <label class="md:col-span-2 flex items-center gap-2 text-sm">
      <input type="checkbox" name="is_visible" value="1" <?= ($editCollection['is_visible'] ?? 1) ? 'checked' : '' ?>>
      Visible in navigation
    </label>
    <div class="md:col-span-2 flex gap-3">
      <button type="submit" class="bg-black text-white px-6 py-2.5 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors">Save</button>
      <a href="<?= adminUrl('collections.php') ?>" class="border px-6 py-2.5 rounded-lg inline-flex items-center">Cancel</a>
    </div>
  </form>
</div>
<?php else: ?>
<div class="mb-6">
  <a href="<?= adminUrl('collections.php?action=add') ?>" class="inline-flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-lg hover:bg-[#D4AF37] hover:text-black transition-colors">+ Add Collection</a>
</div>
<?php endif; ?>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
  <?php foreach ($collections as $col): ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <?php if ($col['image']): ?>
        <img src="<?= e($col['image']) ?>" alt="" class="h-32 w-full object-cover">
      <?php else: ?>
        <div class="h-32 bg-gray-100 flex items-center justify-center text-gray-400">No image</div>
      <?php endif; ?>
      <div class="p-4">
        <div class="flex items-center justify-between">
          <h3 class="font-medium"><?= e($col['title']) ?></h3>
          <?php if (empty($col['is_visible'])): ?>
            <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">Hidden</span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-gray-400 mt-1">/collections/<?= e($col['handle']) ?></p>
        <div class="flex gap-3 mt-3 text-sm">
          <a href="<?= url('collection.php?handle=' . urlencode($col['handle'])) ?>" target="_blank" class="text-gray-400 hover:text-black">View</a>
          <a href="<?= adminUrl('collections.php?edit=' . (int) $col['id']) ?>" class="text-[#D4AF37] hover:underline">Edit</a>
          <form method="post" class="inline" onsubmit="return confirm('Delete this collection?')">
            <input type="hidden" name="delete_collection" value="<?= (int) $col['id'] ?>">
            <button type="submit" class="text-red-500 hover:underline">Delete</button>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$collections): ?>
    <p class="text-gray-400 col-span-full text-center py-8">No collections yet.</p>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/layout-footer.php'; ?>
