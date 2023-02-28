<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

$spinner = new Spinner(spinner: 'mindblown');
$res = $spinner->callback(function () {
    sleep(2);

    // Do something important and return result
    return 42;
});

echo $res . "\n";
// -> 42
