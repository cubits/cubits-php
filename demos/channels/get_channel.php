<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

try {
    $temp = $cubits->getChannel("7ff31a5843887cbaffb9adb3fcb2aebd");
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo var_dump($temp) . "<br />";