<?php

declare(strict_types=1);

namespace DennisCuijpers\Input\Casts;

class IntegerCast
{
    public function __invoke(mixed $value): ?int
    {
        return $value !== null ? (int) $value : null;
    }
}
