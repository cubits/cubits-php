<?php

use Cubits\Cubits;

/** @var Cubits $cubits */
$cubits = require_once '../bootstrap.php';

try {
    $temp = $cubits->sendMoney("3Pj4mJfK62n9mjMRcHYs96nd15UQLHHhPS", "0.1025");
} catch (\Cubits\ApiException $e) {
    die($e->getMessage());
} catch (\Cubits\ConnectionException $e) {
    die($e->getMessage());
}

var_dump($temp);