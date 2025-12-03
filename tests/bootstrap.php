<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = new Dotenv();

/**
 * CI GitHub Actions → SQLite → .env.test
 * Local (pas GitHub) → MySQL → .env
 */
if (getenv('GITHUB_ACTIONS') === 'true') {
    // CI: on charge .env.test (SQLite)
    $dotenv->bootEnv(dirname(__DIR__) . '/.env.test');
} else {
    // Local: on charge .env normalement (MySQL)
    $dotenv->bootEnv(dirname(__DIR__) . '/.env');
}

// Reproduit la config Symfony pour APP_DEBUG
if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}

