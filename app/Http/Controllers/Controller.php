<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

abstract class Controller
{
        /**
     * Send a success response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccess($data, $message, $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'status' => 'success',
            'code' => $statusCode,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Send an error response
     *
     * @param string $message
     * @param array $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendError($message, $data = [], $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
