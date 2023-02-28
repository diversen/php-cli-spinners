# php-cli-spinners

Simple PHP library for displaying spinners in the terminal while running a callback function.

It uses this nice collection of spinners:

https://github.com/sindresorhus/cli-spinners

They can be seen in action here:

https://jsfiddle.net/sindresorhus/2eLtsbey/embedded/result/

The constructor just takes take the name of the spinner.

Is you use a normal Windows PHP install you might not see the spinner, 
as the library uses the [pcntl](https://www.php.net/manual/en/book.pcntl.php) extension.

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