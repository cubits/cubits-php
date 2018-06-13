<?php

use Cubits\Cubits;

require_once '../vendor/autoload.php';
require_once './credentials.php';

try {
    $cubits = Cubits::withApiKey($_API_KEY, $_API_SECRET);
} catch (\Exception $e) {
    die($e->getMessage());
}