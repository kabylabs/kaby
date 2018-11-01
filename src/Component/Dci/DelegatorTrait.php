<?php

declare(strict_types=1);

namespace Kaby\Component\Dci;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
trait DelegatorTrait
{
    /**
     * @var object
     */
    private $instance;

    final public function __call(string $method, array $args)
    {
        return call_user_func_array([$this->instance, $method], $args);
    }

    final public function __get(string $property)
    {
        return $this->instance->{$property};
    }

    final public function __set(string $property, $value)
    {
        $this->instance->{$property} = $value;
    }

    final protected function attach(object $data)
    {
        $this->instance = $data;

        return $this;
    }

    /**
     * @return object
     */
    final protected function extract()
    {
        if ($this->instance instanceof BoundedRoleInterface
            || $this->instance instanceof AbstractRole
        ) {
            return $this->instance->extract();
        }

        return $this->instance;
    }

    /**
     * @return object|mixed
     */
    protected function getInstance(): object
    {
        return $this->instance;
    }
}