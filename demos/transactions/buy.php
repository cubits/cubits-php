<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

try {
    $temp = $cubits->buy("EUR", "10.50");
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo $temp->id . "<br />";