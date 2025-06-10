<?php

namespace Httpful\Response;

final class Headers implements \ArrayAccess, \Countable {

    private $headers;

    /**
     * @param array $headers
     */
    private function __construct($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $string
     * @return Headers
     */
    public static function fromString($string)
    {
        $headers = preg_split("/(\r|\n)+/", $string, -1, \PREG_SPLIT_NO_EMPTY);
        $parse_headers = array();
        for ($i = 1; $i < count($headers); $i++) {
            list($key, $raw_value) = explode(':', $headers[$i], 2);
            $key = trim($key);
            $value = trim($raw_value);
            if (array_key_exists($key, $parse_headers)) {
                // See HTTP RFC Sec 4.2 Paragraph 5
                // http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
                // If a header appears more than once, it must also be able to
                // be represented as a single header with a comma-separated
                // list of values.  We transform accordingly.
                $parse_headers[$key] .= ',' . $value;
            } else {
                $parse_headers[$key] = $value;
            }
        }
        return new self($parse_headers);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->getCaseInsensitive($offset) !== null;
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getCaseInsensitive($offset);
    }

    /**
     * @param string $offset
     * @param string $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception("Headers are read-only.");
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception("Headers are read-only.");
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->headers);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->headers;
    }

    private function getCaseInsensitive(string $key)
    {
        foreach ($this->headers as $header => $value) {
            if (strtolower($key) === strtolower($header)) {
                return $value;
            }
        }

        return null;
    }
}
