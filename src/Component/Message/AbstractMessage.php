<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

use Kaby\Component\Http\Request\RequestContainerTrait;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractMessage
{
    use PayloadTrait, RequestContainerTrait;

    /**
     * AbstractMessage constructor.
     *
     * @param array $payload
     *
     * @throws \ReflectionException
     */
    final public function __construct(array $payload = [])
    {
        $this->setPayload(array_merge($payload, $this->getRequestAll()));
    }
}