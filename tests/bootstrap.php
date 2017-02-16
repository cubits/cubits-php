<?php
/*
* This file is part of the cubits-php package.
*
* (c) Hannes Schulz <hannes.schulz@cubits.com>
*
* For the full copyright and LICENSE information, please view the LICENSE
* file that was distributed with this source code.
*/
call_user_func(function () {
    if (!is_file($autoloadFile = __DIR__ . '/../vendor/autoload.php')) {
        throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
    }
    $loader = require $autoloadFile;
});