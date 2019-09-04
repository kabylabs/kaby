<?php

declare(strict_types=1);

namespace Kaby\Component\Logging;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface CreateLoggingInterface
{
    /**
     * @param array $data
     */
    public function create(array $data): void;
}