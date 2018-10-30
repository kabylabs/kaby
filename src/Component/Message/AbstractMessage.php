<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractMessage
{
    use PayloadTrait;

    /**
     * AbstractMessage constructor.
     *
     * @param array $payload
     *
     * @throws \ReflectionException
     */
    final public function __construct(array $payload = [])
    {
        $this->setPayload($payload);
    }
}