<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

try {
    $temp = $cubits->listAccounts();
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

$accounts = $temp->accounts;

foreach ($accounts as $value) {
    echo $value->currency . ":&nbsp;" . $value->balance . "<br>";
}