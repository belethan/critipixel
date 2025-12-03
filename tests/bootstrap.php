<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = new Dotenv();

/*
 * CI GitHub Actions → SQLite → .env.test
 * Local → MySQL → .env
 */
if ('true' === getenv('GITHUB_ACTIONS')) {
    $dotenv->bootEnv(dirname(__DIR__) . '/.env.test');
} else {
    $dotenv->bootEnv(dirname(__DIR__) . '/.env');
}

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}
