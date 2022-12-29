<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register standardized response formats according to JSend specification.
     * For more information about JSend visit https://github.com/omniti-labs/jsend
     */
    public function boot()
    {
        /**
         * Success
         *
         * response()->success();
         *
         * iterable $data
         * int $httpStatusCode
         */
        Response::macro('success', function (
            iterable|\ArrayAccess |null $data = null,
            int $httpStatusCode = HttpResponse::HTTP_OK
        ): JsonResponse {
            return response()->json([
                'status' => 'success',
                'data' => is_null($data) ? [] : $data,
            ], $httpStatusCode);
        });

        /**
         * Fail
         *
         * response()->fail();
         *
         * iterable $data
         * int $httpStatusCode
         */
        Response::macro('fail', function (
            iterable|\ArrayAccess |null $data = null,
            int $httpStatusCode = HttpResponse::HTTP_BAD_REQUEST
        ): JsonResponse {
            return response()->json([
                'status' => 'fail',
                'data' => is_null($data) ? [] : $data,
            ], $httpStatusCode);
        });

        /**
         * Error
         *
         * response()->error();
         *
         * string $message
         * iterable $data
         * int $httpStatusCode
         */
        Response::macro('error', function (
            string $message,
            iterable|\ArrayAccess |null $data = null,
            int $httpStatusCode = HttpResponse::HTTP_INTERNAL_SERVER_ERROR
        ): JsonResponse {
            $responseBody = [
                'status' => 'error',
                'message' => $message,
            ];

            if (!empty($data)) {
                $responseBody['data'] = $data;
            }

            return response()->json($responseBody, $httpStatusCode);
        });
    }
}
