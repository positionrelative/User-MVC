<?php

namespace App\Resources;

use App\Core\Resources\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(): array
    {
        $user = is_array($this->resource)
            ? (object) $this->resource
            : $this->resource;

        return [
            'id' => $user->id ?? null,
            'name' => $user->name ?? null,
            'email' => $user->email ?? null,
            'created_at' => $user->created_at ?? null,
            'updated_at' => $user->updated_at ?? null,
            'deleted_at' => $user->deleted_at ?? null,
        ];
    }
}