<?php

declare(strict_types=1);

namespace Diversen;

use \Exception;

class Spinner
{

    private bool $running = true;
    private array $spinner = [];

    public function __construct(string $spinner = 'dots')
    {

        // Keyboard interrupts. If these are not handled
        // the process will terminate when pressing e.g. ctrl-c
        pcntl_signal(SIGINT, function () {});
        pcntl_signal(SIGTSTP, function () {});
        pcntl_signal(SIGQUIT, function () {});
        pcntl_async_signals(true);

        $spinner_json = file_get_contents(__DIR__ . '/spinners.json');
        $spinner_ary = json_decode($spinner_json, true);
        $this->spinner = $spinner_ary[$spinner];
    }

    private function display()
    {

        foreach ($this->spinner["frames"] as $char) {
            echo $char . "\r";
            usleep($this->spinner["interval"] * 1000);
        }
    }

    private function start($callback = null)
    {
        echo "\e[?25l";
        while (true) {
            if (!$this->running) {
                break;
            }
            $this->display();
        }
    }

    private function interrupt()
    {
        $this->running = false;
        echo "\r";
        echo "\e[?25h";
    }

    public function callback(callable $callback)
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new Exception('Could not fork process');
        } else if ($pid) {
            // Parent
            $res = $callback();
            $this->interrupt();
            posix_kill($pid, SIGTERM);
            return $res;
        } else {
            // Child
            $this->start();
            exit(0);
        }
    }
}
