<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

$receiver_currency = "EUR";
$txs_callback_url = "http://example.com/callback/tx";
$name = "Alpaca Socks";

try {
    $temp = $cubits->createChannel($receiver_currency, null, null, null, null, null, $txs_callback_url);
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo  $temp->id . "<br />";