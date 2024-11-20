<?php

namespace App\Exceptions\Api\V1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptions
{
    public static array $handlers = [
        AccessDeniedHttpException::class => 'handleUnauthorizedException',
        AuthenticationException::class => 'handleAuthenticationException',
        ValidationException::class => 'handleValidationException',
        ModelNotFoundException::class => 'handleNotFoundException',
        NotFoundHttpException::class => 'handleNotFoundException',
    ];

    public static function handleUnauthorizedException(AccessDeniedHttpException $e, Request $req): JsonResponse
    {
        return response()->json([
            'error' => [
                'type' => basename(get_class($e)),
                'status' => 403,
                'message' => $e->getMessage()
            ]
        ]);
    }


    public static function handleAuthenticationException(AuthenticationException $e, Request $req): JsonResponse
    {
        // log that sensitive stuff
        // should move this out to custom logger
        $source = 'Line: ' . $e->getLine() . ', File: ' . $e->getFile();
        Log::notice(basename(get_class($e)) . ' - ' . $e->getMessage() . ' - ' . $source);

        return response()->json([
            'error' => [
                'type' => basename(get_class($e)),
                'status' => 401,
                'message' => $e->getMessage()
            ]
        ]);
    }

    public static function handleValidationException(ValidationException $e, Request $req): JsonResponse
    {
        foreach ($e->errors() as $key => $value)
            foreach ($value as $message) {
                $errors[] = [
                    'type' => basename(get_class($e)),
                    'status' => 422,
                    'message' => $message,
                ];
            }

        return response()->json([
            'errors' => $errors
        ]);
    }

    public static function handleNotFoundException(ModelNotFoundException|NotFoundHttpException $e, Request $req): JsonResponse
    {
        return response()->json([
            'error' => [
                'type' => basename(get_class($e)),
                'status' => 404,
                'message' => 'Not Found ' . $req->getRequestUri()
            ]
        ]);
    }
}
