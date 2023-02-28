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

// Run this command to see the output:
// 
// php examples/redirected_output.php  > test.txt
// 
// The output will be: 42
// 
// The spinner will not be displayed.I have not found a way to make it work.