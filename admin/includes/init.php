<?php

require_once __DIR__ . '/../../includes/init.php';

function adminUrl(string $path = ''): string
{
    return url('admin/' . ltrim($path, '/'));
}

function adminRequireAccess(PDO $pdo): array
{
    $user = currentUser($pdo);
    if (!$user) {
        redirect('login.php?redirect=' . urlencode(url('admin/')));
    }
    return $user;
}

function adminRequireAdmin(PDO $pdo): array
{
    $user = adminRequireAccess($pdo);
    if (!(int) $user['is_admin']) {
        redirect('admin/access.php');
    }
    return $user;
}

function adminFlash(string $msg, string $type = 'success'): void
{
    $_SESSION['admin_flash'] = ['msg' => $msg, 'type' => $type];
}

function adminGetFlash(): ?array
{
    if (empty($_SESSION['admin_flash'])) {
        return null;
    }
    $flash = $_SESSION['admin_flash'];
    unset($_SESSION['admin_flash']);
    return $flash;
}

function adminStats(PDO $pdo): array
{
    return [
        'products' => (int) $pdo->query('SELECT COUNT(*) FROM ecom_products')->fetchColumn(),
        'active_products' => (int) $pdo->query("SELECT COUNT(*) FROM ecom_products WHERE status = 'active'")->fetchColumn(),
        'orders' => (int) $pdo->query('SELECT COUNT(*) FROM ecom_orders')->fetchColumn(),
        'pending_orders' => (int) $pdo->query("SELECT COUNT(*) FROM ecom_orders WHERE status IN ('pending','paid')")->fetchColumn(),
        'customers' => (int) $pdo->query('SELECT COUNT(*) FROM ecom_customers')->fetchColumn(),
        'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'revenue' => (int) $pdo->query("SELECT COALESCE(SUM(total),0) FROM ecom_orders WHERE status NOT IN ('cancelled','refunded')")->fetchColumn(),
        'collections' => (int) $pdo->query('SELECT COUNT(*) FROM ecom_collections')->fetchColumn(),
    ];
}

function orderStatusBadge(string $status): string
{
    $classes = match ($status) {
        'delivered' => 'bg-green-100 text-green-700',
        'shipped' => 'bg-blue-100 text-blue-700',
        'paid' => 'bg-yellow-100 text-yellow-800',
        'cancelled', 'refunded' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-600',
    };
    return '<span class="text-xs font-medium px-2.5 py-1 rounded capitalize ' . $classes . '">' . e($status) . '</span>';
}
