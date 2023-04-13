<?php

declare(strict_types=1);

namespace DennisCuijpers\Input\Casts;

class BooleanCast
{
    public function __invoke(mixed $value): ?bool
    {
        return $value !== null ? in_array($value, [true, 1, '1', 'true', 'yes', 'on'], true) : null;
    }
}
