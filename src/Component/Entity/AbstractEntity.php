<?php

declare(strict_types=1);

namespace Kaby\Component\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var UuidInterface
     */
    protected $id;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        if (is_string($this->id)) {
            $this->id = Uuid::fromString($this->id);
        }

        return $this->id;
    }
}