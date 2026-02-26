<?php

namespace App\Controllers;

use App\Core\Controllers\ApiController;
use App\Models\User;
use App\Resources\UserResource;

class UserApiController extends ApiController
{

    public function __construct()
    {
        $this->requireAuthUser();
    }

    public function list()
    {
        $users = User::query()->get();

        $this->resourceCollectionResponse($users, UserResource::class);
    }

    public function show(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            $this->errorResponse('User not found', 404);
        }

        $this->resourceResponse($user, UserResource::class);
    }

    public function update(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            $this->errorResponse('User not found', 404);
        }

        $data = $this->getJsonInput();

        if (!empty($data['name'])) {
            $user->name = $data['name'];
        }

        if (!empty($data['email'])) {
            $existing = User::findByEmail($data['email']);
            if ($existing && $existing->id !== $user->id) {
                $this->errorResponse('Email already in use', 422);
            }
            $user->email = $data['email'];
        }

        if (!empty($data['password'])) {
            $user->password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (!$user->save()) {
            $this->errorResponse('Unable to update user', 500);
        }

        $this->resourceResponse($user, UserResource::class);
    }

    public function delete(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            $this->errorResponse('User not found', 404);
        }

        if (!$user->delete()) {
            $this->errorResponse('Unable to delete user', 500);
        }

        $this->jsonResponse(['message' => 'User deleted successfully']);
    }
}