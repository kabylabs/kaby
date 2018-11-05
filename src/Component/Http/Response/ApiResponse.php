<?php

declare(strict_types=1);

namespace Kaby\Component\Http\Response;

use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
final class ApiResponse
{
    /**
     * @var array
     */
    private $params;

    /**
     * @param                          $data
     * @param NormalizerInterface|null $normalizer
     *
     * @return JsonResponse
     */
    public function success($data, NormalizerInterface $normalizer = null): JsonResponse
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
            $this->params = $normalizer->normalize($this->params);
        }

        return JsonResponse::create($this->params);
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return JsonResponse
     */
    public function error(ConstraintViolationListInterface $violations): JsonResponse
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
}