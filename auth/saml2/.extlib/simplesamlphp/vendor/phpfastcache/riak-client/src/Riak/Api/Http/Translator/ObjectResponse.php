<?php

namespace Basho\Riak\Api\Http\Translator;

use Basho\Riak\Api\Http;
use Basho\Riak\Command;
use Basho\Riak\DataObject as RObject;

class ObjectResponse
{
    /**
     * @var Command\KVObject
     */
    protected $command;

    protected $code;

    public function __construct(Command\KVObject $command, $code)
    {
        $this->command = $command;
        $this->code = $code;
    }

    /**
     * @param $response
     * @param array $headers
     * @return \Basho\Riak\DataObject[]
     */
    public function parseResponse($response, $headers = [])
    {
        $objects = [];

        if ($this->code == '300') {
            $position = strpos($headers[Http::CONTENT_TYPE_KEY], 'boundary=');
            $boundary = '--' . substr($headers[Http::CONTENT_TYPE_KEY], $position + 9);
            $objects = $this->parseSiblings($response, $boundary, $headers[Http::VCLOCK_KEY]);
        } elseif (in_array($this->code, ['200','201','204'])) {
            $objects[] = $this->parseObject($response, $headers);
        }

        return $objects;
    }

    public function parseSiblings($response, $boundary, $vclock = '')
    {
        $siblings = [];
        $parts = explode($boundary, $response);
        foreach ($parts as $part) {
            $headers = [Http::VCLOCK_KEY => $vclock];
            $slice_point = 0;
            $empties = 0;

            $lines = preg_split('/\n\r|\n|\r/', trim($part));
            foreach ($lines as $key => $line) {
                if (strpos($line, ':')) {
                    $empties = 0;
                    list ($key, $value) = explode(':', $line);

                    $value = trim($value);

                    if (!empty($value)) {
                        if (!isset($headers[$key])) {
                            $headers[$key] = $value;
                        } elseif (is_array($headers[$key])) {
                            $headers[$key] = array_merge($headers[$key], [$value]);
                        } else {
                            $headers[$key] = array_merge([$headers[$key]], [$value]);
                        }
                    }
                } elseif ($line == '') {
                    // if we have two line breaks in a row, then we have finished headers
                    if ($empties) {
                        $slice_point = $key + 1;
                        break;
                    } else {
                        $empties++;
                    }
                }
            }

            $data = implode(PHP_EOL, array_slice($lines, $slice_point));
            $siblings[] = $this->parseObject($data, $headers);
        }

        return $siblings;
    }

    public function parseObject($response, $headers = [])
    {
        $contentType = !empty($headers[Http::CONTENT_TYPE_KEY]) ? $headers[Http::CONTENT_TYPE_KEY] : '';
        $data = $this->command->getDecodedData($response, $contentType);

        return (new RObject($data, $headers))->setRawData($response);
    }
}
