<?php

declare(strict_types=1);

namespace DennisCuijpers\Input;

use ArrayAccess;
use ArrayIterator;
use DennisCuijpers\Input\Casts\BooleanCast;
use DennisCuijpers\Input\Casts\FloatCast;
use DennisCuijpers\Input\Casts\IntegerCast;
use DennisCuijpers\Input\Casts\LowerCast;
use DennisCuijpers\Input\Casts\StringCast;
use DennisCuijpers\Input\Casts\TrimCast;
use DennisCuijpers\Input\Casts\UpperCast;
use DennisCuijpers\Input\Exceptions\InputException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use IteratorAggregate;
use stdClass;

class Input implements Arrayable, ArrayAccess, IteratorAggregate
{
    protected static array $casts = [
        'boolean' => BooleanCast::class,
        'integer' => IntegerCast::class,
        'float'   => FloatCast::class,
        'string'  => StringCast::class,
        'lower'   => LowerCast::class,
        'upper'   => UpperCast::class,
        'trim'    => TrimCast::class,
    ];

    protected array $callbacks = [];

    public function __construct(protected array $data = [])
    {
    }

    public static function make(array $data): self
    {
        return new static($data);
    }

    public static function extend(string $cast, $callback): void
    {
        static::$casts[$cast] = $callback;
    }

    public function register(string $cast, $callback): void
    {
        $this->callbacks[$cast] = $callback;
    }

    public function validate(array $rules, array $messages = [], array $attributes = []): self
    {
        $validated = Validator::validate($this->data, $rules, $messages, $attributes);

        return new static($validated);
    }

    public function has(string $key): bool
    {
        $placeholder = new stdClass();

        return $this->get($key, $placeholder) !== $placeholder;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        data_set($this->data, $key, $value);
    }

    public function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            if (!$this->has($key)) {
                $this->set($key, $value);
            }
        }
    }

    public function forget(string $key): void
    {
        Arr::forget($this->data, $key);
    }

    public function only(array $keys): array
    {
        $output = new static();

        foreach ($keys as $key) {
            if ($this->has($key)) {
                $output->set($key, $this->get($key));
            }
        }

        return $output->all();
    }

    public function except(array $keys): array
    {
        $output = new static($this->all());

        foreach ($keys as $key) {
            $output->forget($key);
        }

        return $output->all();
    }

    public function all(): array
    {
        return $this->data;
    }

    public function assemble(array $mapping, array $fill = []): array
    {
        $output = new static();

        foreach ($mapping as $target => $casts) {
            if (!is_array($casts)) {
                $casts = explode('|', $casts);
            }

            $source = array_shift($casts);

            if (!$this->has($source)) {
                continue;
            }

            $value = $this->get($source);

            foreach ($casts as $cast) {
                $value = $this->cast($cast, $value);
            }

            $output->set($target, $value);
        }

        $output->fill($fill);

        return $output->all();
    }

    public function toArray(): array
    {
        return $this->all();
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset !== null) {
            $this->set($offset, $value);
        } else {
            $this->data[] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->forget($offset);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    protected function cast(callable|string $cast, mixed $value): mixed
    {
        if (is_callable($cast)) {
            return $cast($value);
        }

        $callback = $this->callbacks[$cast] ?? static::$casts[$cast] ?? null;

        if ($callback === null) {
            throw new InputException("Invalid cast: {$cast}");
        }

        if (is_string($callback)) {
            $callback = new $callback();
        }

        return call_user_func_array($callback, [$value]);
    }
}
