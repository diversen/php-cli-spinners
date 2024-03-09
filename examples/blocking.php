<?php

// Test blocking calls
// E.g. if you make a blocking IO call in the callback function, then you may not be able to
// Abort the spinner with ctrl-c.

require_once "vendor/autoload.php";

use Diversen\Spinner;

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Test of blocking calls. 
// If you make a blocking IO call in the callback function, then you may not be able to
// Abort the spinner with ctrl-c.
//
// Start a php server in base directory
// php -S localhost:8000 -t examples/
// 
// Curl example that will not block
function request($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $mh = curl_multi_init();
    curl_multi_add_handle($mh, $ch);

    do {
        $status = curl_multi_exec($mh, $active);
    } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

    while ($active && $status === CURLM_OK) {
        if (curl_multi_select($mh) === -1) {
            usleep(100);
        }
        do {
            $status = curl_multi_exec($mh, $active);
        } while ($status === CURLM_CALL_MULTI_PERFORM);
    }

    if ($status !== CURLM_OK) {
        throw new Exception(curl_multi_strerror($status));
    }

    $result = curl_multi_getcontent($ch);

    curl_multi_remove_handle($mh, $ch);
    curl_multi_close($mh);
    curl_close($ch);

    return $result;
}

// Stream context example that will block
function stream_context () {

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: MyAgent/1.0\r\n"
        ]
    ]);

    // Set a timeout
    stream_context_set_option($context, 'http', 'timeout', 5);
    try {
        $url = 'http://localhost:8000';
        $res = file_get_contents($url, false, $context);
        return $res;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

$spinner = new Spinner(spinner: 'dots');
$res = $spinner->callback(function () {

    // This will NOT block
    // try {
    //     return request('http://localhost:8000');
    // } catch (Exception $e) {
    //     return $e->getMessage();
    // }

    // This will block. You can not escape using ctrl-c
    try {
        return stream_context();
    } catch (Exception $e) {
        return $e->getMessage();
    }


});

echo $res . "\n";

