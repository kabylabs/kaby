<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

use function Funct\Strings\camelize;
use ReflectionException;
use ReflectionProperty;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
trait PayloadTrait
{
    /**
     * @param array $payload
     *
     * @throws ReflectionException
     */
    protected function setPayload(array $payload): void
    {
        foreach ($payload as $key => $value) {
            $property = camelize($key);
            if (property_exists($this, $property)) {
                $this->setPropValue($property, $value);
            }
        }
    }

    /**
     * @param $key
     * @param $value
     *
     * @throws ReflectionException
     */
    private function setPropValue($key, $value): void
    {
        $property = new ReflectionProperty(get_class($this), $key);
        $property->setAccessible(true);
        $property->setValue($this, $value);
    }
}