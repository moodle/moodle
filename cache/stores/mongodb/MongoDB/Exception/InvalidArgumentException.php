<?php
/*
 * Copyright 2015-2017 MongoDB, Inc.
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

namespace MongoDB\Exception;

class InvalidArgumentException extends \MongoDB\Driver\Exception\InvalidArgumentException implements Exception
{
    /**
     * Thrown when an argument or option has an invalid type.
     *
     * @param string $name         Name of the argument or option
     * @param mixed  $value        Actual value (used to derive the type)
     * @param string $expectedType Expected type
     * @return self
     */
    public static function invalidType($name, $value, $expectedType)
    {
        return new static(sprintf('Expected %s to have type "%s" but found "%s"', $name, $expectedType, is_object($value) ? get_class($value) : gettype($value)));
    }
}
