<?php

declare(strict_types=1);

namespace Kaby\Component\Entity;

use Ramsey\Uuid\UuidInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface EntityInterface
{
    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface;
}