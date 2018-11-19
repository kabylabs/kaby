<?php

declare(strict_types=1);

namespace Kaby\Component\Event;

use function Funct\Strings\dasherize;
use Kaby\Component\Message\PayloadTrait;
use ReflectionClass;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractEvent extends Event
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

    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function getClassName()
    {
        $reflection = new ReflectionClass(get_called_class());
        $name = dasherize($reflection->getShortName());

        return $name;
    }
}