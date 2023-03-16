<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

$spinner = new Spinner(spinner: 'material');
$res = $spinner->callback(function () {
    for($i = 0; $i < 100; $i++) {
        echo "Doing something important: " . $i . "\n";
        sleep(1);
    }
    sleep(2);
    return 42;
});

echo "$res\n";
