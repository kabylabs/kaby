<?php

declare(strict_types=1);

namespace Kaby\Component\Bundle;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
trait BundleTrait
{
    /**
     * @return string
     */
    protected function getBundleName(): string
    {
        return __DIR__;
    }
}