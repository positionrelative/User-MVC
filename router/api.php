<?php

declare(strict_types=1);

use App\Controllers\Auth\ForgotPasswordController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\UserApiController;

$router->post('/api/register', [RegisterController::class, 'register']);
$router->post('/api/login', [LoginController::class, 'login']);
$router->post('/api/forgot-password', [ForgotPasswordController::class, 'requestReset']);

$router->get('/api/users', [UserApiController::class, 'list']);
$router->get('/api/users/{id}', [UserApiController::class, 'show']);
$router->put('/api/users/{id}', [UserApiController::class, 'update']);
$router->delete('/api/users/{id}', [UserApiController::class, 'delete']);
