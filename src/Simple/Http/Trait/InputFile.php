<?php

namespace Simple\Http\Trait;

use Simple\Exceptions\InvalidArgumentException;

trait InputFile
{
    /**
     * @var string|null
     */
    public $filename;

    /**
     * @var int|null
     */
    public $size;

    /**
     * @var int|null
     */
    public $type;

    /**
     * @var int
     */
    public $errors;

    /**
     * @var string|null
     */
    public $tmpName;

    /**
     * Create from array
     *
     * @param array $values
     * @throws InvalidArgumentException
     * @return static
     */
    public static function createFromArray(array $values): self
    {
        if (isset($values['index']) === false) {
            throw new InvalidArgumentException('Index key is required');
        }

        /* Easy way of ensuring that all indexes-are set and not filling the screen with isset() */

        $values += [
            'tmp_name' => null,
            'type'     => null,
            'size'     => null,
            'name'     => null,
            'error'    => null,
        ];

        return (new static($values['index']))
            ->setSize((int)$values['size'])
            ->setError((int)$values['error'])
            ->setType($values['type'])
            ->setTmpName($values['tmp_name'])
            ->setFilename($values['name']);
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * Set file size
     * @param int $size
     * @return static
     */
    public function setSize(int $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get mime-type of file
     * @return string
     */
    public function getMime(): string
    {
        return $this->getType();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set type
     * @param string $type
     * @return static
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns extension without "."
     *
     * @return string
     */
    public function getExtension(): string
    {
        return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * Set filename
     *
     * @param string $name
     * @return static
     */
    public function setFilename(string $name)
    {
        $this->filename = $name;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string mixed
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Move the uploaded temporary file to it's new home
     *
     * @param string $destination
     * @return bool
     */
    public function move(string $destination): bool
    {
        return move_uploaded_file($this->tmpName, $destination);
    }

    /**
     * Get file contents
     *
     * @return string
     */
    public function getContents(): string
    {
        return file_get_contents($this->tmpName);
    }

    /**
     * Return true if an upload error occurred.
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return ($this->getError() !== 0);
    }

    /**
     * Get upload-error code.
     *
     * @return int|null
     */
    public function getError(): ?int
    {
        return $this->errors;
    }

    /**
     * Set error
     *
     * @param int|null $error
     * @return static
     */
    public function setError(?int $error)
    {
        $this->errors = (int)$error;

        return $this;
    }

    /**
     * @return string
     */
    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    /**
     * Set file temp. name
     * @param string $name
     * @return static
     */
    public function setTmpName(string $name)
    {
        $this->tmpName = $name;

        return $this;
    }

    public function getValueFile(): string
    {
        return $this->getFilename();
    }

    /**
     * @param mixed $value
     * @return static
     */
    public function setValueFile($value)
    {
        $this->filename = $value;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'tmp_name' => $this->tmpName,
            'type'     => $this->type,
            'size'     => $this->size,
            'name'     => $this->name,
            'error'    => $this->errors,
            'filename' => $this->filename,
        ];
    }
}
