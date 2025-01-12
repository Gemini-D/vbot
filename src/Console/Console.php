<?php

declare(strict_types=1);

namespace Hanson\Vbot\Console;

use Carbon\Carbon;
use Hanson\Vbot\Foundation\Vbot;
use Hyperf\Collection\Arr;

class Console
{
    public const INFO = 'INFO';

    public const WARNING = 'WARNING';

    public const ERROR = 'ERROR';

    public const MESSAGE = 'MESSAGE';

    /**
     * console config.
     */
    protected array $config;

    public function __construct(protected Vbot $vbot)
    {
        $this->config = $this->vbot->config['console'] ?? [];
    }

    /**
     * determine the console is windows or linux.
     *
     * @return bool
     */
    public static function isWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * print in terminal.
     *
     * @param string $level
     * @param bool $log
     * @param mixed $str
     */
    public function log($str, $level = 'INFO', $log = false)
    {
        if ($this->isOutput()) {
            if ($log) {
                $this->vbot->log->log($level, $str);
            }
            echo '[' . Carbon::now()->toDateTimeString() . ']' . "[{$level}] " . $str . PHP_EOL;
        }
    }

    /**
     * print message.
     * @param mixed $str
     */
    public function message($str)
    {
        if (Arr::get($this->config, 'message', true)) {
            $this->log($str, self::MESSAGE);
        }
    }

    public function isOutput()
    {
        return Arr::get($this->config, 'output', true);
    }
}
