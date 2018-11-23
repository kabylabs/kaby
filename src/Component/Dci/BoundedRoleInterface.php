<?php

declare(strict_types=1);

namespace Kaby\Component\Dci;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface BoundedRoleInterface
{
    /**
     * Extract data that bounded with role
     *
     * @return mixed
     */
    public function extract();
}