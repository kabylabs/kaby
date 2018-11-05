<?php

declare(strict_types=1);

namespace Kaby\Component\Http\Response;

use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param $data
     *
     * @return JsonResponse
     */
    public function success($data): JsonResponse
    {
        $this->params['meta']['hostname'] = gethostname();
        $this->params['data'] = $data;

        dump($data);

        if ($data instanceof PaginatedRepresentation) {
            $this->params['pagination'] = [
                'page'  => $data->getPage(),
                'limit' => $data->getLimit(),
                'pages' => $data->getPages(),
                'total' => $data->getTotal(),
            ];

            $this->params['data'] = $data->getInline()->getResources();
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