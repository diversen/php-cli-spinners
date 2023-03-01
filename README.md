# php-cli-spinners

Simple PHP library for displaying spinners in the terminal while running a callback function.

You will need to have the [pcntl](https://www.php.net/manual/en/book.pcntl.php) extension installed.

The library uses this [collection of spinners](https://github.com/sindresorhus/cli-spinners):

The spinners can be seen [in action here](https://jsfiddle.net/sindresorhus/2eLtsbey/embedded/result/)

If you don't have the `pcntl` extension installed, you can still use the library, but you will not see the spinner.

On windows use WSL and you will be fine.

## Installation

```bash
composer require diversen/php-cli-spinners
```

## Usage

```php
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

```

## Examples

Clone:

    git clone https://github.com/diversen/php-cli-spinners.git

See [examples](examples) folder. E.g. run `php examples/simple.php`

## License

MIT © [Dennis Iversen](https://github.com/diversen)