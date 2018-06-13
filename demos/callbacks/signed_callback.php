<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

$receiver_currency = "EUR";
$name = "Alpaca Socks";
$callback_url = "http://example.com:8888/cubits-php/demos/callbacks/test_callback.php";

try {
    $temp = $cubits->createChannel($receiver_currency, $name, null, null, $callback_url, null);
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo $temp->callback_url;