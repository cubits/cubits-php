<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

$sender = array(
    'currency' => 'EUR'
);

$receiver = array(
    'currency' => 'BTC',
    'amount' => '10'
);

$params = array(
    'operation' => 'buy',
    'sender' => $sender,
    'receiver' => $receiver,
);

try {
    $temp = $cubits->post('quotes', $params);
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

echo  $temp . "<br />";