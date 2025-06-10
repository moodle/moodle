<?php
/**
 * Mime Type: application/json
 * @author Nathan Good <me@nategood.com>
 */

namespace Httpful\Handlers;

use Httpful\Exception\JsonParseException;

class JsonHandler extends MimeHandlerAdapter
{
    private $decode_as_array = false;

    public function init(array $args)
    {
        $this->decode_as_array = !!(array_key_exists('decode_as_array', $args) ? $args['decode_as_array'] : false);
    }

    /**
     * @param string $body
     * @return mixed
     * @throws \Exception
     */
    public function parse($body)
    {
        $body = $this->stripBom($body);
        if (empty($body))
            return null;
        $parsed = json_decode($body, $this->decode_as_array);
        if (is_null($parsed) && 'null' !== strtolower($body))
            throw new JsonParseException('Unable to parse response as JSON: ' . json_last_error_msg());
        return $parsed;
    }

    /**
     * @param mixed $payload
     * @return string
     */
    public function serialize($payload)
    {
        return json_encode($payload);
    }
}
