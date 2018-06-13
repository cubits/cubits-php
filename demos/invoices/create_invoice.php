<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

$params = array(
    'reference' => '15',
    'description' => 'Order monkey'
);

try {
    $temp = $cubits->createInvoice("EUR", "10.00", "Alpaca socks", $params);
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo $temp->id . "<br />";