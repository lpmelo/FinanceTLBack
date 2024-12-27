<?php
require_once 'autoload.php';

use Config\Application;
use Config\Cors;

$baseDir = str_replace("\public", '', __DIR__);
$baseDir = str_replace("/public", '', __DIR__);
$dotenv = Dotenv\Dotenv::createImmutable($baseDir);
$dotenv->load();

$origin = $_ENV['CROSS_ORIGIN_ACCEPTED_URL'];

Cors::initCors([
    "origin" => $origin,
    "methods" => "GET, POST, PUT, PATCH, DELETE, OPTIONS",
    "headers.allow" => "Content-Type, Authorization",
    "headers.expose" => "",
    "credentials" => false,
    "cache" => "0",
]);


date_default_timezone_set('America/Sao_Paulo');

$app = new Application();

$app->initApplication();
