<?php

declare(strict_types=1);

namespace Diversen;

use \Exception;

class Spinner
{

    private bool $running = true;
    private array $spinner = [];
    private $blink_off = "\e[?25l";
    private $blink_on = "\e[?25h";
    private $clear_line = "\33[2K\r";
    private $position_zero = "\r";

    public function __construct(string $spinner = 'dots')
    {
        $spinner_json = file_get_contents(__DIR__ . '/spinners.json');
        $spinner_ary = json_decode($spinner_json, true);
        $this->spinner = $spinner_ary[$spinner];
    }

    private function display()
    {
        foreach ($this->spinner["frames"] as $frame) {
            echo $frame . $this->position_zero;
            usleep($this->spinner["interval"] * 1000);
        }
    }

    private function start($callback = null)
    {
        echo $this->blink_off;
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

        echo $this->clear_line;
        echo $this->blink_on;
    }

    public function callback(callable $callback)
    {

        if (!extension_loaded('pcntl')) {
            $res = $callback();
            return $res;
        }


        // Keyboard interrupts. If these are not handled
        // the process will terminate when pressing e.g. ctrl-c
        $interrupt = function ($signo) {};
        pcntl_signal(SIGINT, $interrupt);
        pcntl_signal(SIGTSTP, $interrupt);
        pcntl_signal(SIGQUIT, $interrupt);
        pcntl_async_signals(true);

        if (posix_isatty(STDOUT)) {

            // Output is being displayed on the screen
            // Only start spinner if output is not redirected to a file
            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new Exception('Could not fork process');
            } else if ($pid) {
                // Parent process
                $res = $callback();
                $this->interrupt();
                posix_kill($pid, SIGTERM);
                return $res;
            } else {
                $this->start();
            }
        } else {
            $res = $callback();
            return $res;
        }
    }
}
