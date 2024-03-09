<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

$spinner = new Spinner(spinner: 'soccerHeader', message: "Loading");
$res = $spinner->callback(function () use($spinner) {
    sleep(2);

    // You should always catch exceptions in the callback
    // If not the terminal may not be reset
    try {
        
        throw new Exception('Something went wrong');
    } catch (Exception $e) {
        
        // You may reset the terminal if needed 
        $spinner->resetTerminal();
        
        // And e.g. echo the error message
        echo $e->getMessage() . PHP_EOL;
    }

    return 42;
});

print($res . PHP_EOL);
    