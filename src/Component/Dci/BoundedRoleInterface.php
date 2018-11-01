<?php

declare(strict_types=1);

namespace Kaby\Component\Dci;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface BoundedRoleInterface
{
    /**
     * Extract the data that bounded to role.
     *
     * @return object
     */
    public function extract(): object;
}