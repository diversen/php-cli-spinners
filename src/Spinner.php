<?php

declare(strict_types=1);

namespace Diversen;

use \Exception;

class Spinner
{

    private int $pid = 0;
    private bool $use_keyboard_interrupts = true;
    private bool $running = true;
    private array $spinner = [];
    private $blink_off = "\e[?25l";
    private $blink_on = "\e[?25h";
    private $clear_line = "\33[2K\r";
    private $position_zero = "\r";
    // Ignore these streams. No spinner.
    private array $ignore_streams = [STDERR, STDIN];

    public function __construct(
        string $spinner = 'dots',
        bool $use_keyboard_interrupts = true,
        array $ignore_streams = []
    ) {

        $this->ignore_streams = $ignore_streams;
        $this->use_keyboard_interrupts = $use_keyboard_interrupts;
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

    private function keyboardInterrupts()
    {
        // Keyboard interrupts. E.g. ctrl-c
        // Exit parent process 
        $keyboard_interrupts = function ($signo) {
            $this->interrupt();
            posix_kill($this->pid, SIGTERM);
            exit($signo);
        };

        pcntl_signal(SIGINT, $keyboard_interrupts);
        pcntl_signal(SIGTSTP, $keyboard_interrupts);
        pcntl_signal(SIGQUIT, $keyboard_interrupts);
        pcntl_async_signals(true);
    }

    public function callback(callable $callback)
    {

        if (!extension_loaded('pcntl')) {
            $res = $callback();
            return $res;
        }

        // Output is being displayed on the screen
        // Only start spinner if output is not redirected to a file
        if (!in_array(STDOUT, $this->ignore_streams)) $this->runCallback($callback);
        if (!in_array(STDERR, $this->ignore_streams)) $this->runCallBack($callback);

        if (!in_array(STDIN, $this->ignore_streams)) {
            $this->runCallback($callback);
        } else {
            $res = $callback();
            return $res;
        }
    }

    public function runCallBack(callable $callback)
    {
        if ($this->use_keyboard_interrupts) {
            $this->keyboardInterrupts();
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new Exception('Could not fork process');
        } else if ($pid) {
            // Parent process
            $this->pid = $pid;
            $res = $callback();
            $this->interrupt();
            posix_kill($this->pid, SIGTERM);
            return $res;
        } else {
            // Child process
            $this->start();
        }
    }
}
