<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class JsonErrorController
{
    public function showError(Throwable $exception)
    {
        return new JsonResponse([
            'error' => $exception->getMessage()
        ], $exception->getCode());
    }

    public function showDefaultError(){
        return new JsonResponse([
            'error' => "Internal Server Error"
        ], 500);
    }
}
