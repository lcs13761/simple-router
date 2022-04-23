<?php

namespace Simple\Http\Input;

interface IInputItem
{

    public function getIndex(): string;

    public function setIndex(string $index);

    public function getName(): ?string;

    public function setName(string $name);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    public function __toString(): string;

}