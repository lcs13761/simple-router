<?php

namespace Simple\Http\Trait;

use Simple\Exceptions\InvalidArgumentException;
use Simple\Http\Input\IInputItem;
use Simple\Http\Request;
use Simple\Http\Input\InputFile;

trait InputHandler
{
    /**
     * @var array
     */
    protected $get = [];

    /**
     * @var array
     */
    protected $post = [];

    /**
     * @var array
     */
    protected $file = [];

    /**
     * Original post variables
     * @var array
     */
    protected $originalPost = [];

    /**
     * Original get/params variables
     * @var array
     */
    protected $params = [];

    /**
     * Get original file variables
     * @var array
     */
    protected $originalFile = [];

    /**
     * Parse input values
     *
     */
    public function parseInputs(): void
    {
        /* Parse get requests */
        if (count($_GET) !== 0) {
            $this->params = $_GET;
            $this->get = $this->parseInputItem($this->params);
        }

        /* Parse post requests */
        $this->originalPost = $_POST;

        if ($this->isPostBack() === true) {

            $contents = file_get_contents('php://input');

            // Append any PHP-input json
            if (strpos(trim($contents), '{') === 0) {
                $post = json_decode($contents, true);

                if ($post !== false) {
                    $this->originalPost += $post;
                }
            }
        }

        if (count($this->originalPost) !== 0) {
            $this->post = $this->parseInputItem($this->originalPost);
        }

        /* Parse get requests */
        if (count($_FILES) !== 0) {
            $this->originalFile = $_FILES;
            $this->file = $this->parseFiles($this->originalFile);
        }
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
            $list[$key] = $value;
        }

        return $list;
    }

    /**
     * @param array $files Array with files to parse
     * @param string|null $parentKey Key from parent (used when parsing nested array).
     * @return array
     */
    public function parseFiles(array $files, ?string $parentKey = null): array
    {
        $list = [];

        foreach ($files as $key => $value) {

            // Parse multi dept file array
            if (isset($value['name']) === false && is_array($value) === true) {
                $list[$key] = $this->parseFiles($value, $key);
                continue;
            }

            // Handle array input
            if (is_array($value['name']) === false) {
                $values = ['index' => $parentKey ?? $key];

                try {
                    $list[$key] = self::createFromArray($values + $value);
                } catch (InvalidArgumentException $e) {
                }
                continue;
            }

            $keys = [$key];
            $files = $this->rearrangeFile($value['name'], $keys, $value);

            if (isset($list[$key]) === true) {
                $list[$key][] = $files;
            } else {
                $list[$key] = $files;
            }
        }

        return $list;
    }

    /**
     * Rearrange multi-dimensional file object created by PHP.
     *
     * @param array $values
     * @param array $index
     * @param array|null $original
     * @return array
     */
    protected function rearrangeFile(array $values, array &$index, ?array $original): array
    {
        $originalIndex = $index[0];
        array_shift($index);

        $output = [];

        foreach ($values as $key => $value) {

            if (is_array($original['name'][$key]) === false) {

                try {

                    $file = self::createFromArray([
                        'index'    => ($key === '' && $originalIndex !== '') ? $originalIndex : $key,
                        'name'     => $original['name'][$key],
                        'error'    => $original['error'][$key],
                        'tmp_name' => $original['tmp_name'][$key],
                        'type'     => $original['type'][$key],
                        'size'     => $original['size'][$key],
                    ]);

                    if (isset($output[$key]) === true) {
                        $output[$key][] = $file;
                        continue;
                    }

                    $output[$key] = $file;
                    continue;
                } catch (InvalidArgumentException $e) {
                }
            }

            $index[] = $key;

            $files = $this->rearrangeFile($value, $index, $original);

            if (isset($output[$key]) === true) {
                $output[$key][] = $files;
            } else {
                $output[$key] = $files;
            }
        }

        return $output;
    }

    /**
     * Find input object
     *
     * @param string $index
     * @param array ...$methods
     * @return IInputItem|array|null
     */
    public function find(string $index, ...$methods)
    {
        $element = null;

        if (count($methods) > 0) {
            $methods = is_array(...$methods) ? array_values(...$methods) : $methods;
        }

        if (count($methods) === 0 || in_array(Request::REQUEST_TYPE_GET, $methods, true) === true) {
            $element = $this->get($index);
        }

        if (($element === null && count($methods) === 0) || (count($methods) !== 0 && in_array(Request::REQUEST_TYPE_POST, $methods, true) === true)) {
            $element = $this->post($index);
        }

        if (($element === null && count($methods) === 0) || (count($methods) !== 0 && in_array('file', $methods, true) === true)) {
            $element = $this->file($index);
        }

        return $element;
    }

    protected function getValueFromArray(array $array): array
    {
        $output = [];
        /* @var $item InputItem */
        foreach ($array as $key => $item) {

            if ($item instanceof IInputItem) {
                $item = $item->getValue();
            }

            $output[$key] = is_array($item) ? $this->getValueFromArray($item) : $item;
        }

        return $output;
    }

    /**
     * Get input element value matching index
     *
     * @param string $index
     * @param string|mixed|null $defaultValue
     * @param array ...$methods
     * @return string|array
     */
    public function value(string $index, $defaultValue = null, ...$methods)
    {
        $input = $this->find($index, ...$methods);

        /* Handle collection */
        if (is_array($input) === true) {
            $output = $this->getValueFromArray($input);
            return (count($output) === 0) ? $defaultValue : $output;
        }

        return ($input === null || (is_string($input) && trim($input) === '')) ? $defaultValue : $input;
    }

    /**
     * Check if a input-item exist.
     * If an array is as $index parameter the method returns true if all elements exist.
     *
     * @param string|array $index
     * @param array ...$methods
     * @return bool
     */
    public function exists($index, ...$methods): bool
    {
        // Check array
        if (is_array($index) === true) {
            foreach ($index as $key) {
                if ($this->value($key, null, ...$methods) === null) {
                    return false;
                }
            }

            return true;
        }

        return $this->value($index, null, ...$methods) !== null;
    }

    /**
     * Find post-value by index or return default value.
     *
     * @param string $index
     * @param mixed|null $defaultValue
     * @return InputItem|array|string|null
     */
    public function post(string $index, $defaultValue = null)
    {
        return $this->post[$index] ?? $defaultValue;
    }


    public function get(string|array $value, mixed $return = null): string | array | null
    {

        $output = $this->data;
        if (is_string($value)) {
            return $output[$value] ?? $return;
        }

        if (is_array($value)) {
            $values = [];
            foreach ($values as $value) {
                $value[$value] = $output[$value] ?? $return;
            }

            return $values;
        }
    }

    /**
     * Find file by index or return default value.
     *
     * @param string $index
     * @param mixed|null $defaultValue
     * @return InputFile|array|string|null
     */
    public function file(string $index, $defaultValue = null)
    {
        return $this->file[$index] ?? $defaultValue;
    }


    /**
     * Get all get/post items
     * @param array $filter Only take items in filter
     * @return array
     */
    public function all(array $filter = []): array
    {
        $output = $this->data;
        $output = (count($filter) > 0) ? array_intersect_key($output, array_flip($filter)) : $output;

        foreach ($filter as $filterKey) {
            if (array_key_exists($filterKey, $output) === false) {
                $output[$filterKey] = null;
            }
        }

        return $output;
    }

    public function except(string|array $filter)
    {
        $output = $this->data;

        if (is_string($filter)) {
            unset($output[$filter]);
            return $output;
        }

        foreach ($filter as $filterKey) {
            unset($output[$filterKey]);
        }

        return $output;
    }

    public function only(string|array $filter)
    {
        $output = $this->data;

        if (is_string($filter)) {
            return $output[$filter];
        }

        $output = (count($filter) > 0) ? array_intersect_key($output, array_flip($filter)) : $output;

        foreach ($filter as $filterKey) {
            if (array_key_exists($filterKey, $output) === false) {
                $output[$filterKey] = null;
            }
        }

        return $output;
    }

    /**
     * Get original get variables
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set original get-variables
     * @param array $params
     * @return static $this
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get original file variables
     * @return array
     */
    public function getOriginalFile(): array
    {
        return $this->originalFile;
    }

    /**
     * Set original file posts variables
     * @param array $file
     * @return static $this
     */
    public function setOriginalFile(array $file): self
    {
        $this->originalFile = $file;

        return $this;
    }
}
