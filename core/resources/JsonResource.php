<?php

namespace App\Core\Resources;

abstract class JsonResource
{
    public function __construct(protected object|array $resource)
    {
    }

    abstract public function toArray(): array;

    public static function collection(iterable $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = (new static($item))->toArray();
        }

        return $result;
    }
}