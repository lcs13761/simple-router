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

    public function offsetGet($offset)
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


    /**
     * Parse input item from array
     *
     * @param array $array
     * @return array
     */
    protected function parseInputItem(array $array): array
    {
        $list = [];

        foreach ($array as $key => $value) {

            // Handle array input
            if (is_array($value) === true) {
                $value = $this->parseInputItem($value);
            }

            $this->$key = $value;
            $this->value = $value;
            $list[$key] = $this;
        }

        return $list;
    }
}
