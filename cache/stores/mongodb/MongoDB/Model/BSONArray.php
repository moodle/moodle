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

use function array_values;
use function MongoDB\recursive_copy;

/**
 * Model class for a BSON array.
 *
 * The internal data will be filtered through array_values() during BSON
 * serialization to ensure that it becomes a BSON array.
 *
 * @api
 */
class BSONArray extends ArrayObject implements JsonSerializable, Serializable, Unserializable
{
    /**
     * Clone this BSONArray.
     */
    public function __clone()
    {
        foreach ($this as $key => $value) {
            $this[$key] = recursive_copy($value);
        }
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
        $array = new static();
        $array->exchangeArray($properties);

        return $array;
    }

    /**
     * Serialize the array to BSON.
     *
     * The array data will be numerically reindexed to ensure that it is stored
     * as a BSON array.
     *
     * @see https://php.net/mongodb-bson-serializable.bsonserialize
     * @return array
     */
    #[ReturnTypeWillChange]
    public function bsonSerialize()
    {
        return array_values($this->getArrayCopy());
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
        self::__construct($data);
    }

    /**
     * Serialize the array to JSON.
     *
     * The array data will be numerically reindexed to ensure that it is stored
     * as a JSON array.
     *
     * @see https://php.net/jsonserializable.jsonserialize
     * @return array
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_values($this->getArrayCopy());
    }
}
