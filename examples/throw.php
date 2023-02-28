<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

$spinner = new Spinner(spinner: 'soccerHeader');
$spinner->callback(function () {
    sleep(2);

    // You should throw and catch inside callback 
    try {
        throw new Exception('Something went wrong');
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
});
    