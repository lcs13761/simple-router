<?php

namespace Simple\Http\Trait;

use ArrayIterator;

trait InputItem
{
    public $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set input value
     * @param mixed $value
     * @return static
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }


    public function offsetExists($offset): bool
    {
        return isset($this->value[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        if ($this->offsetExists($offset) === true) {
            return $this->value[$offset];
        }

        return null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->value[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->value[$offset]);
    }

    public function __toString(): string
    {
        $value = $this->getValue();

        return (is_array($value) === true) ? json_encode($value) : $value;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getValue());
    }
}
