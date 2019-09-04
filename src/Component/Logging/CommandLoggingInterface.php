<?php

declare(strict_types=1);

namespace Kaby\Component\Logging;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface CommandLoggingInterface
{
    /**
     * @return string
     */
    public function logName(): string;

    /**
     * @return string
     */
    public function logDescription(): string;
}