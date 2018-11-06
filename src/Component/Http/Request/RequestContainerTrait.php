<?php

declare(strict_types=1);

namespace Kaby\Component\Http\Request;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
trait RequestContainerTrait
{
    /**
     * @return RequestStack
     */
    public function getRequestStack(): RequestStack
    {
        return $this->getContainer()->get('request_stack');
    }

    /**
     * @return array
     */
    public function getRequestAll(): array
    {
        return array_merge(
            $this->getRequestStack()->getCurrentRequest()->request->all(),
            $this->getRequestStack()->getCurrentRequest()->query->all()
        );
    }

    /**
     * @return ContainerInterface
     */
    abstract function getContainer(): ContainerInterface;
}