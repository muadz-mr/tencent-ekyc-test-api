<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (!$request->expectsJson()) {
            return parent::render($request, $exception);
        }

        $apiResponse = new ApiResponse();

        if ($exception instanceof AuthenticationException) {
            return $apiResponse->error(JsonResponse::HTTP_UNAUTHORIZED, __('Unauthorized.'));
        } else if ($exception instanceof AuthorizationException) {
            return $apiResponse->error(JsonResponse::HTTP_FORBIDDEN, __('Unauthorized.'));
        } else if ($exception instanceof AccessDeniedHttpException) {
            return $apiResponse->error(JsonResponse::HTTP_FORBIDDEN, __('Forbidden access.'));
        } else if ($exception instanceof NotFoundHttpException) {
            return $apiResponse->error(JsonResponse::HTTP_NOT_FOUND, __('Resource not found.'));
        } else if ($exception instanceof ValidationException) {
            return $apiResponse->error(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $exception->getMessage(), $exception->errors());
        } else if ($exception instanceof BadRequestException) {
            return $apiResponse->error(JsonResponse::HTTP_BAD_REQUEST, $exception->getMessage());
        } else if ($exception instanceof ThrottleRequestsException) {
            return $apiResponse->error(JsonResponse::HTTP_TOO_MANY_REQUESTS, __('Too many attempts.'));
        } else if ($exception instanceof ServiceUnavailableHttpException) {
            return $apiResponse->error(JsonResponse::HTTP_SERVICE_UNAVAILABLE, __('Server busy.'));
        } else {
            return $apiResponse->error(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, App::environment('production') ? __('Server error.') : $exception->getMessage());
        }
    }
}
