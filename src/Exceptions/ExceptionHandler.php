<?php

namespace Svr\Raw\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Container;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;
use OpenAdminCore\Admin\Reporter\Reporter;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return function (Exceptions $exceptions) {
    $exceptions->render(function (Throwable $e, Request $request) {
        /** @var ResponseFactory $response */
        $response = Container::getInstance()->make(ResponseFactory::class);
        // Добавим стандартный вывод ошибки Reporter::report($e);
        if ($request->is('admin/*')) {
            return Reporter::report($e);
        }

        /**
         * определение кастомного Exceptions для api
         */
        if ($request->is('api/*')) {


            if ($e instanceof AuthenticationException &&
                '/user' === $request->json()->getPathInfo()
            ) {
                return $response->noContent();
            }

            if ($e instanceof InvalidSignatureException) {
                return $response->make(
                    content: ['message' => $e->getMessage()],
                    status: $e->getStatusCode(),
                );
            }

            if ($e instanceof NotFoundHttpException) {
                return $response->make(
                    content: ['message' => 'Not Found.'],
                    status: $e->getStatusCode(),
                );
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $response->make(
                    content: ['message' => $e->getMessage()],
                    status: $e->getStatusCode(),
                );
            }
            if ($e instanceof InvalidArgumentException) {
                return $response->make(
                    content: ['message' => 'Method Not Allowed.'],
                    status: $e->getStatusCode(),
                );
            }

            if ($e instanceof ValidationException) {
                $errors = [];
                foreach ($e->errors() as $attribute => $message) {
                    $errors[$attribute] = $message[0];
                }

                return $response->make(
                    content: ['errors' => $errors],
                    status: $e->status,
                );
            }
            return null;
        }
    });
};
