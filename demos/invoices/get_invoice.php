<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

try {
    $temp = $cubits->getInvoice("ef73a6ed61a8c97427eaae2073b9127b");
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo $temp->id . "<br />";