<?php

declare(strict_types=1);

namespace DennisCuijpers\Input\Casts;

class FloatCast
{
    public function __invoke(mixed $value): ?float
    {
        return $value !== null ? (float) $value : null;
    }
}
