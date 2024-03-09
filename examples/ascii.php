<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

// Non utf-8 system you may need a ascii spinner
$spinner = new Spinner(spinner: 'simpleDots', message: "Loading");
$res = $spinner->callback(function () {
    sleep(2);
    return 42;
});

echo "$res\n";
