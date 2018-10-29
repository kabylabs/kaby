<?php

declare(strict_types=1);

namespace App\Component\Http\Controller;

use App\Component\Http\Request\RequestContainerTrait;
use App\Component\Http\Response\ApiResponse;
use App\Component\Message\AbstractMessage;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractController extends BaseController
{
    use RequestContainerTrait;

    /**
     * @var ApiResponse
     */
    private $apiResponse;

    /**
     * @var MessageBusInterface
     */
    private $handle;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * AbstractController constructor.
     *
     * @param ApiResponse         $apiResponse
     * @param MessageBusInterface $handle
     * @param ValidatorInterface  $validator
     */
    public function __construct(ApiResponse $apiResponse, MessageBusInterface $handle, ValidatorInterface $validator)
    {
        $this->apiResponse = $apiResponse;
        $this->handle = $handle;
        $this->validator = $validator;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param AbstractMessage     $message
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     */
    protected function handle(AbstractMessage $message, NormalizerInterface $normalizer)
    {
        $violations = $this->validate($message);
        if (count($violations) > 0) {
            return $this->apiResponse->error($violations);
        }

        return $this->apiResponse->success($normalizer->normalize($this->dispatch($message)));
    }

    /**
     * @param AbstractMessage $message
     *
     * @return mixed
     */
    protected function dispatch(AbstractMessage $message)
    {
        return $this->handle->dispatch($message);
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