<?php

declare(strict_types=1);

namespace Kaby\Component\Dci;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractRole
{
    use DelegatorTrait;

    final public function __construct()
    {
    }

    public function supports(object $data): bool
    {
        return true;
    }
}