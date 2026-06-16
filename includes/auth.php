<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentUser(PDO $pdo): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, email, full_name, is_admin FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? url('account.php'));
        redirect('login.php?redirect=' . $redirect);
    }
}

function requireAdmin(PDO $pdo): void
{
    requireLogin();
    $user = currentUser($pdo);
    if (!$user || !(int) $user['is_admin']) {
        redirect('admin/access.php');
    }
}

function loginUser(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_admin'] = (int) $user['is_admin'];
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
