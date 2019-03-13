<?php

declare(strict_types=1);

namespace Kaby\Component\Http\Controller;

use Hateoas\Representation\PaginatedRepresentation;
use Kaby\Component\Message\MessageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var MessageInterface
     */
    private $message;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @param $data
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function response($data): JsonResponse
    {
        return $this->success($data);
    }

    /**
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    protected function send(): JsonResponse
    {
        $this->messageBus = $this->container->get('message_bus');
        $this->message->setPayload($this->getRequestAll());
        $violations = $this->validate($this->message);

        if ($violations->count() > 0) {
            return $this->error($violations);
        }

        $envelope = $this->messageBus->dispatch($this->message);

        /** @var HandledStamp $stamp */
        if ($stamp = $envelope->last(HandledStamp::class)) {
            $data = $stamp->getResult();
        }

        return $this->success($data ?? [], $this->normalizer);
    }

    /**
     * @param MessageInterface $message
     *
     * @return Controller
     */
    protected function message(MessageInterface $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param NormalizerInterface $normalizer
     *
     * @return Controller
     */
    protected function normalizer(NormalizerInterface $normalizer): self
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    /**
     * @return array
     */
    protected function getRequestAll(): array
    {
        $request = $this->container->get('request_stack');

        return array_merge(
            $request->getCurrentRequest()->request->all(),
            $request->getCurrentRequest()->query->all(),
            $request->getCurrentRequest()->attributes->get('_route_params'),
            $request->getCurrentRequest()->files->all()
        );
    }

    /**
     * @param MessageInterface $message
     *
     * @return ConstraintViolationListInterface
     */
    private function validate(MessageInterface $message): ConstraintViolationListInterface
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->container->get('validation');

        return $validator->validate($message);
    }

    /**
     * @param                          $data
     * @param NormalizerInterface|null $normalizer
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    private function success($data, NormalizerInterface $normalizer = null): JsonResponse
    {
        $this->params['meta']['hostname'] = gethostname();

        if ($data instanceof PaginatedRepresentation) {
            $this->params['pagination'] = [
                'page'  => $data->getPage(),
                'limit' => $data->getLimit(),
                'pages' => $data->getPages(),
                'total' => $data->getTotal(),
            ];

            $this->params['data'] = $data->getInline()->getResources();
        } else {
            $this->params['data'] = $data;
        }

        if ($normalizer) {
            $this->params['data'] = $normalizer->normalize($this->params['data']);
        }

        return JsonResponse::create($this->params);
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return JsonResponse
     */
    private function error(ConstraintViolationListInterface $violations): JsonResponse
    {
        $this->params['meta']['hostname'] = gethostname();

        foreach ($violations as $violation) {
            $this->params['errors'][] = [
                'field'   => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return JsonResponse::create($this->params, JsonResponse::HTTP_BAD_REQUEST, ['Content-Type' => 'application/problem+json']);
    }

    /**
     * @return array
     */
    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(), [
                'validation' => ValidatorInterface::class
            ]
        );
    }
}