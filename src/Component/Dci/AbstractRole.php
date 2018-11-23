<?php

declare(strict_types=1);

namespace Kaby\Component\Dci;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractRole
{
    use DelegatorTrait;

    /**
     * Constructor.
     */
    final public function __construct()
    {
    }

    /**
     * Attach data to role
     *
     * @param mixed $data
     */
    protected function attach($data): void
    {
        $this->delegated = $data;
    }

    /**
     * Extract data
     *
     * @return mixed
     */
    protected function extract()
    {
        if ($this->delegated instanceof BoundedRoleInterface) {
            return $this->delegated->extract();
        }

        return $this->delegated;
    }
}