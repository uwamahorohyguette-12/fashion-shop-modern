<?php
require_once __DIR__ . '/includes/init.php';

if (currentUser($pdo)) {
    redirect('account.php');
}

$error = '';
$mode = ($_GET['mode'] ?? 'login') === 'register' ? 'register' : 'login';
$redirectTo = $_GET['redirect'] ?? url('account.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'login';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $redirectTo = $_POST['redirect'] ?? url('account.php');

    if ($mode === 'register') {
        if (strlen($password) < 4) {
            $error = 'Password must be at least 4 characters.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, full_name) VALUES (?, ?, ?)');
                $stmt->execute([$email, $hash, $fullName]);
                $userId = (int) $pdo->lastInsertId();
                loginUser(['id' => $userId, 'is_admin' => 0]);
                header('Location: ' . $redirectTo);
                exit;
            }
        }
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
        } else {
            loginUser($user);
            header('Location: ' . $redirectTo);
            exit;
        }
    }
}

$pageTitle = ($mode === 'login' ? 'Sign In' : 'Register') . ' — KigaliThreads';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto px-4 py-16">
  <h1 class="font-serif text-4xl text-center mb-2"><?= $mode === 'login' ? 'Welcome Back' : 'Create Account' ?></h1>
  <p class="text-center text-gray-500 mb-8"><?= $mode === 'login' ? 'Sign in to your KigaliThreads account' : 'Join the KigaliThreads community' ?></p>

  <form method="post" class="space-y-4">
    <input type="hidden" name="mode" value="<?= e($mode) ?>">
    <input type="hidden" name="redirect" value="<?= e($redirectTo) ?>">
    <?php if ($mode === 'register'): ?>
      <input required name="full_name" placeholder="Full name" class="w-full border rounded px-4 py-3" value="<?= e($_POST['full_name'] ?? '') ?>">
    <?php endif; ?>
    <input required type="email" name="email" placeholder="Email" class="w-full border rounded px-4 py-3" value="<?= e($_POST['email'] ?? '') ?>">
    <input required type="password" name="password" minlength="4" placeholder="Password" class="w-full border rounded px-4 py-3">
    <?php if ($error): ?><p class="text-red-500 text-sm"><?= e($error) ?></p><?php endif; ?>
    <button type="submit" class="w-full bg-black text-white py-3 rounded font-medium hover:bg-[#D4AF37] hover:text-black transition-colors">
      <?= $mode === 'login' ? 'Sign In' : 'Create Account' ?>
    </button>
  </form>

  <p class="text-center text-sm mt-6 text-gray-500">
    <?= $mode === 'login' ? "Don't have an account? " : 'Already have an account? ' ?>
    <a href="<?= url('login.php?mode=' . ($mode === 'login' ? 'register' : 'login') . '&redirect=' . urlencode($redirectTo)) ?>" class="underline text-black font-medium">
      <?= $mode === 'login' ? 'Register' : 'Sign In' ?>
    </a>
  </p>
  <p class="text-center text-xs mt-4"><a href="<?= url() ?>" class="underline text-gray-400">Continue shopping</a></p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
