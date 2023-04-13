<?php

declare(strict_types=1);

namespace DennisCuijpers\Input\Casts;

class StringCast
{
    public function __invoke(mixed $value): ?string
    {
        return $value !== null ? (string) $value : null;
    }
}
