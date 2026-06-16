<?php

define('BASE_URL', '');
define('SITE_NAME', 'KigaliThreads');

function url(string $path = ''): string
{
    if ($path === '') {
        return BASE_URL . '/';
    }
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}
