<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

use Kaby\Component\Http\Request\RequestContainerTrait;
use Psr\Container\ContainerInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractMessage
{
    use PayloadTrait, RequestContainerTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

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

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}