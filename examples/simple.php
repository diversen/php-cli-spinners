<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

$spinner = new Spinner(spinner: 'material');
$res = $spinner->callback(function () {
    sleep(2);
    return 42;
});

echo "$res\n";
