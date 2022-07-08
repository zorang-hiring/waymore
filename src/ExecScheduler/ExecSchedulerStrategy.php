<?php
declare(strict_types=1);

namespace App\ExecScheduler;

interface ExecSchedulerStrategy
{
    public function shouldLoop(): bool;
    public function shouldPause(): bool;
    public function pause(): void;
    public function incExecutedLoopCount(): void;
    public function getLoopCount(): int;
    public function getPauseSec(): int;
}