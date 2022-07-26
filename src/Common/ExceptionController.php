<?php

declare(strict_types=1);

namespace App\Common;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionController
{
    public function __invoke(\Throwable $exception): Response
    {
        return $this->show($exception);
    }

    public function show(Throwable $exception): Response
    {
        if ($exception instanceof HttpException) {
            foreach ($exception->getHeaders() as $header => $value) {
                if (strtolower($header) === 'content-type' && strtolower($value) === 'application/json') {
                    return new Response(
                        $exception->getMessage(),
                        $exception->getStatusCode(),
                        $exception->getHeaders()
                    );
                }
            }
        }

        $data = $this->getSerializedError($exception);

        return new JsonResponse($data, $this->getStatusCode($exception));
    }

    private function getSerializedError(Throwable $throwable): array
    {
        $data = [
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
        ];

        if ($throwable instanceof HttpException) {
            $data = [
                ...$data,
                'status' => $throwable->getStatusCode(),
                'headers' => $throwable->getHeaders(),
            ];
        }

        return $data;
    }

    private function getStatusCode(Throwable $throwable): int
    {
        if ($throwable instanceof HttpException) {
            return $throwable->getStatusCode();
        }

        return 500;
    }
}
