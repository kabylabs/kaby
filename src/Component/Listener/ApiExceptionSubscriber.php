<?php

declare(strict_types=1);

namespace App\Component\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return JsonResponse
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $errors = [
            'code'    => $event->getException()->getCode(),
            'message' => $event->getException()->getMessage(),
            'file'    => $event->getException()->getFile(),
            'line'    => $event->getException()->getLine(),
        ];

        $this->errors['errors'] = $errors;

        return JsonResponse::create(
            $this->errors,
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            ['Content-Type' => 'application/problem+json']
        );
    }
}