<?php

declare(strict_types=1);

namespace Kaby\Component\Http\Controller;

use Hateoas\Representation\PaginatedRepresentation;
use Kaby\Component\Http\Request\RequestContainerTrait;
use Kaby\Component\Http\Response\ApiResponse;
use Kaby\Component\Message\AbstractMessage;
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
     * @param AbstractMessage          $message
     * @param NormalizerInterface|null $normalizer
     *
     * @return JsonResponse
     */
    protected function handle(AbstractMessage $message, NormalizerInterface $normalizer = null): JsonResponse
    {
        $violations = $this->validate($message);
        if (count($violations) > 0) {
            return $this->apiResponse->error($violations);
        }

        $data = $this->dispatch($message);

        return $this->response($data, $normalizer);
    }

    /**
     * @param                          $data
     * @param NormalizerInterface|null $normalizer
     *
     * @return JsonResponse
     */
    protected function response($data, NormalizerInterface $normalizer = null): JsonResponse
    {
        dump($data);
        if ($data instanceof PaginatedRepresentation) {
            $arr = [];
            $arr['pagination'] = [
                'page'  => $data->getPage(),
                'limit' => $data->getLimit(),
                'pages' => $data->getPages(),
                'total' => $data->getTotal(),
            ];

            $arr['data'] = $data->getInline()->getResources();

            dump($arr);

            $data = $arr;

            dump($data);
        }

        if ($normalizer) {
            $data = $normalizer->normalize($data);
        }

        dump($data);

        return $this->apiResponse->success($data);
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