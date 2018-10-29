<?php

declare(strict_types=1);

namespace App\Component\Http\Request;

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
        return $this->getRequestStack()->getCurrentRequest()->request->all();
    }

    /**
     * @return ContainerInterface
     */
    abstract function getContainer(): ContainerInterface;
}