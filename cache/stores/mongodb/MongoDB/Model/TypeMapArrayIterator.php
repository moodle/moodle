<?php
/*
 * Copyright 2016-2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Model;

use ArrayIterator;
use MongoDB\Exception\BadMethodCallException;

/**
 * Iterator for applying a type map to documents in inline command results.
 *
 * @internal
 */
class TypeMapArrayIterator extends ArrayIterator
{
    private $typeMap;

    /**
     * Constructor.
     *
     * @param array $documents
     * @param array $typeMap
     */
    public function __construct(array $documents = [], array $typeMap)
    {
        parent::__construct($documents);

        $this->typeMap = $typeMap;
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.append
     * @throws BadMethodCallException
     */
    public function append($value)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.asort
     * @throws BadMethodCallException
     */
    public function asort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Return the current element with the type map applied to it.
     *
     * @see http://php.net/arrayiterator.current
     * @return array|object
     */
    public function current()
    {
        return \MongoDB\apply_type_map_to_document(parent::current(), $this->typeMap);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.ksort
     * @throws BadMethodCallException
     */
    public function ksort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.natcasesort
     * @throws BadMethodCallException
     */
    public function natcasesort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.natsort
     * @throws BadMethodCallException
     */
    public function natsort()
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Return the value from the provided offset with the type map applied.
     *
     * @see http://php.net/arrayiterator.offsetget
     * @param mixed $offset
     * @return array|object
     */
    public function offsetGet($offset)
    {
        return \MongoDB\apply_type_map_to_document(parent::offsetGet($offset), $this->typeMap);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.offsetset
     * @throws BadMethodCallException
     */
    public function offsetSet($index, $newval)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.offsetunset
     * @throws BadMethodCallException
     */
    public function offsetUnset($index)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.uasort
     * @throws BadMethodCallException
     */
    public function uasort($cmp_function)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayiterator.uksort
     * @throws BadMethodCallException
     */
    public function uksort($cmp_function)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }
}
