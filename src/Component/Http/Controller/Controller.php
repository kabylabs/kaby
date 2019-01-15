<?php

declare(strict_types=1);

namespace Kaby\Component\Http\Controller;

use Kaby\Component\Http\Response\ApiResponse;
use Kaby\Component\Message\AbstractMessage;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class Controller extends BaseController
{
    use HandleTrait;

    /**
     * @var ApiResponse
     */
    private $apiResponse;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Controller constructor.
     *
     * @param ApiResponse         $apiResponse
     * @param RequestStack        $request
     * @param MessageBusInterface $messageBus
     * @param ValidatorInterface  $validator
     */
    public function __construct(
        ApiResponse $apiResponse,
        RequestStack $request,
        MessageBusInterface $messageBus,
        ValidatorInterface $validator
    ) {
        $this->apiResponse = $apiResponse;
        $this->request = $request;
        $this->messageBus = $messageBus;
        $this->validator = $validator;
    }

    /**
     * @param AbstractMessage          $message
     * @param NormalizerInterface|null $normalizer
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    protected function commit(AbstractMessage $message, NormalizerInterface $normalizer = null): JsonResponse
    {
        $message->setPayload($this->getRequestAll());

        $violations = $this->validate($message);

        if ($violations->count() > 0) {
            return $this->apiResponse->error($violations);
        }

        return $this->apiResponse->success($this->handle($message), $normalizer);
    }

    /**
     * @return array
     */
    protected function getRequestAll(): array
    {
        return array_merge(
            $this->request->getCurrentRequest()->request->all(),
            $this->request->getCurrentRequest()->query->all(),
            $this->request->getCurrentRequest()->attributes->get('_route_params'),
            $this->request->getCurrentRequest()->files->all()
        );
    }

    /**
     * @param AbstractMessage $message
     *
     * @return ConstraintViolationListInterface
     */
    private function validate(AbstractMessage $message): ConstraintViolationListInterface
    {
        return $this->validator->validate($message);
    }
}