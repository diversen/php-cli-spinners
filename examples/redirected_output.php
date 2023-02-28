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

// This shows the spinner and the output of the callback function:
// 
// php examples/redirected_output.php
// 
// When redirecting the output to a file, the spinner will not be displayed
//
// php examples/redirected_output.php > /tmp/test.txt