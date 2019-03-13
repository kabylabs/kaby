<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface MessageInterface
{
    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void;
}