<?php

declare(strict_types=1);

namespace Kaby\Component\Dci;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
trait DelegatorTrait
{
    /**
     * @var mixed
     */
    protected $delegated;

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    final public function __call($method, $args)
    {
        return call_user_func_array(array($this->delegated, $method), $args);
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    final public function __get($property)
    {
        return $this->delegated->{$property};
    }

    /**
     * @param string $property
     * @param mixed  $value
     */
    final public function __set($property, $value)
    {
        $this->delegated->{$property} = $value;
    }
}