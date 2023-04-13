<?php

declare(strict_types=1);

namespace DennisCuijpers\Input\Casts;

class UpperCast
{
    public function __invoke(mixed $value): mixed
    {
        return is_string($value) ? strtoupper($value) : $value;
    }
}
