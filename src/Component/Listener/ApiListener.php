<?php

declare(strict_types=1);

namespace Kaby\Component\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ApiListener implements EventSubscriberInterface
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $request->setLocale($request->headers->get('Accept-Language'));
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        $payload = array_merge(
            $request->request->all(),
            $request->query->all(),
            $request->attributes->get('_route_params') ?? [],
            $request->files->all()
        );

        $request->request->set('payload', $payload);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 32]],
            KernelEvents::CONTROLLER => [['onKernelController', 0]],
        ];
    }
}