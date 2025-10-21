<?php
  use Illuminate\Database\Capsule\Manager as Capsule;
  use Dotenv\Dotenv;

  require_once __DIR__ . '/vendor/autoload.php';

  $dotenv = Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  $capsule = new Capsule;

  $capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci'
  ]);

  $capsule->setAsGlobal();
  $capsule->bootEloquent();