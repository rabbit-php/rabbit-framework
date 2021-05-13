<?php

declare(strict_types=1);

namespace Rabbit\Base\Core;

use Swoole\Coroutine;

final class LoopControl
{

    public static array $loopArr = [];

    public int $sleep = 1;

    private int $cid = 0;

    private string $name;

    private bool $run = true;

    public bool $loop = true;

    private bool $yielded = false;

    public function __construct(int $sleep, string $name = null)
    {
        $this->sleep = $sleep;
        $this->name = $name ?? uniqid();
        self::$loopArr[] = $this;
    }

    public function shutdown(): void
    {
        $this->loop = false;
    }

    public static function shutdownAll(): void
    {
        foreach (self::$loopArr as $loop) {
            $loop->loop = false;
        }
    }

    public function getRun(): bool
    {
        return $this->run;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCid(): int
    {
        return $this->cid;
    }

    public function setCid(int $cid): void
    {
        if ($this->cid === 0) {
            $this->cid = $cid;
        }
    }

    public function check(): void
    {
        if ($this->run === false && $this->yielded === false && $this->loop) {
            $this->yielded = true;
            Coroutine::yield();
        }
    }

    public function stop(): bool
    {
        if ($this->run === true && $this->loop) {
            $this->run = false;
            if ($this->yielded === false && Coroutine::getCid() === $this->cid) {
                $this->yielded = true;
                Coroutine::yield();
            }
            return true;
        }
        return false;
    }

    public function start(): bool
    {
        if ($this->run === false && $this->yielded === true && $this->loop) {
            $this->run = true;
            $this->yielded = false;
            Coroutine::resume($this->cid);
            return true;
        }
        return false;
    }
}
