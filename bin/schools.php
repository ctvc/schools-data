<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    (new Dotenv(__DIR__ . '/..'))->load();
}

(new ContainerBuilder)
    ->addDefinitions(__DIR__.'/../src/di-config.cli.php')
    ->build()
    ->get(Application::class)
    ->run();
