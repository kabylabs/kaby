<?php

declare(strict_types=1);

namespace Kaby\Component\Http\Response;

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
     * @var int
     */
    private $code;

    /**
     * @param $code
     *
     * @return ApiResponse
     */
    public function setStatusCode($code): ApiResponse
    {
        $this->code = $code;
        $this->params['meta']['status'] = $code;
        $this->params['meta']['success'] = $code >= 200 && $code < 300;
        $this->params['meta']['hostname'] = gethostname();

        return $this;
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     */
    public function success($data): JsonResponse
    {
        $this->code = JsonResponse::HTTP_OK;
        $this->setStatusCode($this->code);
        $this->params['data'] = $data;

        return JsonResponse::create($this->params);
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return JsonResponse
     */
    public function error(ConstraintViolationListInterface $violations): JsonResponse
    {
        $this->code = JsonResponse::HTTP_BAD_REQUEST;
        $this->setStatusCode($this->code);

        foreach ($violations as $violation) {
            $this->params['errors'][] = [
                'field'   => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return JsonResponse::create($this->params, $this->code, ['Content-Type' => 'application/problem+json']);
    }
}