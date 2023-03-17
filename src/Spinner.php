<?php

declare(strict_types=1);

namespace Diversen;

use \Exception;

class Spinner
{

    private int $child_pid = 0;
    private bool $use_keyboard_interrupts = true;
    private array $spinner = [];
    private $blink_off = "\e[?25l";
    private $blink_on = "\e[?25h";
    private $clear_line = "\33[2K\r";
    private $return_to_left = "\r";

    public function __construct(string $spinner = 'simpleDots', bool $use_keyboard_interrupts = true)
    {
        $this->use_keyboard_interrupts = $use_keyboard_interrupts;
        $spinner_json = file_get_contents(__DIR__ . '/spinners.json');
        $spinner_ary = json_decode($spinner_json, true);
        $this->spinner = $spinner_ary[$spinner];
    }

    private function start()
    {
        echo $this->blink_off;
        while (true) {
            foreach ($this->spinner["frames"] as $frame) {
                echo $frame . $this->return_to_left;
                usleep($this->spinner["interval"] * 1000);
            }
        }
    }

    private function resetTerminal()
    {
        echo $this->clear_line;
        echo $this->blink_on;
    }

    private function keyboardInterrupts()
    {
        // Keyboard interrupts. E.g. ctrl-c
        // First exit child process using posix_kill
        // Then exit parent process using exit

        $keyboard_interrupts = function ($signo) {
            posix_kill($this->child_pid, SIGTERM);
            $this->resetTerminal();
            exit($signo);
        };

        pcntl_signal(SIGINT, $keyboard_interrupts);
        pcntl_signal(SIGTSTP, $keyboard_interrupts);
        pcntl_signal(SIGQUIT, $keyboard_interrupts);
        pcntl_async_signals(true);
    }

    public function callback(callable $callback)
    {
        // Only start spinner if output is to STDOUT. 
        // But not if output is redirected to a file
        if (!extension_loaded('pcntl') || !posix_isatty(STDOUT)) {
            
            $res = $callback();
            return $res;
        }

        return $this->runCallBack($callback);

    }

    private function runCallBack(callable $callback)
    {

        $child_pid = pcntl_fork();
        if ($child_pid == -1) {
            throw new Exception('Could not fork process');
        } else if ($child_pid) {

            // Parent process
            if ($this->use_keyboard_interrupts) {
                $this->keyboardInterrupts();
            }

            $this->child_pid = $child_pid;
            $res = $callback();
            posix_kill($this->child_pid, SIGTERM);
            $this->resetTerminal();
            return $res;
        } else {
            // Child process
            // Child pid is 0 here
            $this->start();
        }
    }
}
