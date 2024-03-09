# php-cli-spinners

Simple PHP library for displaying spinners in the terminal while running a callback function.

You will need to have the [pcntl](https://www.php.net/manual/en/book.pcntl.php) extension installed.

The library uses this [collection of spinners](https://github.com/sindresorhus/cli-spinners):

The spinners can be seen [in action here](https://jsfiddle.net/sindresorhus/2eLtsbey/embedded/result/)

If you don't have the `pcntl` extension installed, you can still use the library, but you will not see the spinner.

On windows use WSL. There may be some trouble using UTF-8 characters in the terminal on WSL.

In this case you can use the `simpleDots` or `simpleDotsScrolling` spinner.

## How it works

The library will run a callback function in a separate process which displays the spinner. 
The main process will run the callback function and return the result.

## Installation

```bash
composer require diversen/php-cli-spinners
```

## Usage

```php
<?php

require_once "vendor/autoload.php";

use Diversen\Spinner;

$spinner = new Spinner(spinner: 'simpleDots', message: "Loading");
$res = $spinner->callback(function () {
    sleep(2);
    return 42;
});

echo "$res\n"; // 42



```

## Examples

Clone:

    git clone https://github.com/diversen/php-cli-spinners.git

See [examples](examples) folder. E.g. run `php examples/simple.php`

## License

MIT © [Dennis Iversen](https://github.com/diversen)