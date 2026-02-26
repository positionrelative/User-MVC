<?php

namespace App\Core\Controllers;

use App\Core\Resources\JsonResource;
use App\Models\User;

abstract class ApiController
{
    protected function jsonResponse(mixed $data, int $status = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function successResponse(array $data = [], string $message = '', int $status = 200): void
    {
        $this->jsonResponse(['data' => $data, 'message' => $message], $status);
    }

    protected function errorResponse(string $message, int $status = 400): void
    {
        $this->jsonResponse(['error' => $message], $status);
    }

    /**
     * Send a JSON response using a resource class to transform the data.
      * 
      * @param object|array $resource
      * @param string $resourceClass
     */
    protected function resourceResponse(object|array $resource, string $resourceClass, int $status = 200): void
    {
        if (!is_subclass_of($resourceClass, JsonResource::class)) {
            $this->errorResponse('Invalid resource class', 500);
        }

        $this->jsonResponse((new $resourceClass($resource))->toArray(), $status);
    }

    /**
     * Send a JSON response for a collection of resources using a resource class to transform the data.
      * 
      * @param iterable $items
      * @param string $resourceClass
     */
    protected function resourceCollectionResponse(iterable $items, string $resourceClass, int $status = 200): void
    {
        if (!is_subclass_of($resourceClass, JsonResource::class)) {
            $this->errorResponse('Invalid resource class', 500);
        }

        $this->jsonResponse($resourceClass::collection($items), $status);
    }

    protected function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    protected function getBearerToken(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !preg_match('/^Bearer\s+(\S+)$/i', trim($authHeader), $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }

    protected function requireAuthUser(): User
    {
        $token = $this->getBearerToken();

        if (!$token) {
            $this->errorResponse('Unauthorized', 401);
        }

        $user = User::findByToken($token);

        if (!$user) {
            $this->errorResponse('Unauthorized', 401);
        }

        return $user;
    }
}
