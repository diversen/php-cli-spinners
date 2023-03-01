<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Diversen\Spinner;

final class SimpleTest extends TestCase
{
    public function test_simple(): void
    {
        $spinner = new Spinner(spinner: 'dots');
        $res = $spinner->callback(function () {
            return 42;
        });

        $this->assertEquals(42, $res);
    }
}