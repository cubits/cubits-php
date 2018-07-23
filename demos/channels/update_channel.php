<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

$channelId = "7ff31a5843887cbaffb9adb3fcb2aebd";
$receiver_currency = "USD";
$txs_callback_url = "http://example.com/callback/tx";
$name = "Alpaca Socks";


try {
    $temp = $cubits->updateChannel($channelId, $receiver_currency, $name, null, null, null, $txs_callback_url);
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo  $temp->channel_url . "<br />";