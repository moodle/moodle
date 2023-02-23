<?php
/*
 * Copyright 2016-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Model;

use ArrayObject;
use JsonSerializable;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Unserializable;
use ReturnTypeWillChange;

use function MongoDB\recursive_copy;

/**
 * Model class for a BSON document.
 *
 * The internal data will be cast to an object during BSON serialization to
 * ensure that it becomes a BSON document.
 *
 * @api
 */
class BSONDocument extends ArrayObject implements JsonSerializable, Serializable, Unserializable
{
    /**
     * Deep clone this BSONDocument.
     */
    public function __clone()
    {
        foreach ($this as $key => $value) {
            $this[$key] = recursive_copy($value);
        }
    }

    /**
     * This overrides the parent constructor to allow property access of entries
     * by default.
     *
     * @see https://php.net/arrayobject.construct
     */
    public function __construct(array $input = [], int $flags = ArrayObject::ARRAY_AS_PROPS, string $iteratorClass = 'ArrayIterator')
    {
        parent::__construct($input, $flags, $iteratorClass);
    }

    /**
     * Factory method for var_export().
     *
     * @see https://php.net/oop5.magic#object.set-state
     * @see https://php.net/var-export
     * @return self
     */
    public static function __set_state(array $properties)
    {
        $document = new static();
        $document->exchangeArray($properties);

        return $document;
    }

    /**
     * Serialize the document to BSON.
     *
     * @see https://php.net/mongodb-bson-serializable.bsonserialize
     * @return object
     */
    #[ReturnTypeWillChange]
    public function bsonSerialize()
    {
        return (object) $this->getArrayCopy();
    }

    /**
     * Unserialize the document to BSON.
     *
     * @see https://php.net/mongodb-bson-unserializable.bsonunserialize
     * @param array $data Array data
     */
    #[ReturnTypeWillChange]
    public function bsonUnserialize(array $data)
    {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Serialize the array to JSON.
     *
     * @see https://php.net/jsonserializable.jsonserialize
     * @return object
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (object) $this->getArrayCopy();
    }
}
