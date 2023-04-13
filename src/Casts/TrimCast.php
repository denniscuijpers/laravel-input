<?php

declare(strict_types=1);

namespace DennisCuijpers\Input\Casts;

class TrimCast
{
    public function __invoke(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }
}
