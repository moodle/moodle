<?php

namespace Kevinrob\GuzzleCache;

class KeyValueHttpHeader implements \Iterator
{
    /**
     * Take from https://github.com/hapijs/wreck.
     */
    const REGEX_SPLIT = '/(?:^|(?:\s*\,\s*))([^\x00-\x20\(\)<>@\,;\:\\\\"\/\[\]\?\=\{\}\x7F]+)(?:\=(?:([^\x00-\x20\(\)<>@\,;\:\\\\"\/\[\]\?\=\{\}\x7F]+)|(?:\"((?:[^"\\\\]|\\\\.)*)\")))?/';

    /**
     * @var string[]
     */
    protected $values = [];

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $value) {
            $matches = [];
            if (preg_match_all(self::REGEX_SPLIT, $value, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $val = '';
                    if (count($match) == 3) {
                        $val = $match[2];
                    } elseif (count($match) > 3) {
                        $val = $match[3];
                    }

                    $this->values[$match[1]] = $val;
                }
            }
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        // For performance, we can use isset,
        // but it will not match if value == 0
        return isset($this->values[$key]) || array_key_exists($key, $this->values);
    }

    /**
     * @param string $key
     * @param string $default the value to return if don't exist
     * @return string
     */
    public function get($key, $default = '')
    {
        if ($this->has($key)) {
            return $this->values[$key];
        }

        return $default;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->values) === 0;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->values);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        next($this->values);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     *
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->values);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return key($this->values) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->values);
    }
}
