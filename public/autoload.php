<?php

$baseDir = __DIR__ . '/vendor/autoload.php';
$classFile = str_replace('public/', '', $baseDir);

if (file_exists($classFile)) {
    require_once $classFile;
}
