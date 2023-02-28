<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

$spinner = new Spinner(spinner: 'dots');
$res = $spinner->callback(function () {
    // Do something important
    sleep(2);
    return 42;
});

echo $res . "\n";