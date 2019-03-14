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

class BadMethodCallException extends \BadMethodCallException implements Exception
{
    /**
     * Thrown when a mutable method is invoked on an immutable object.
     *
     * @param string $class Class name
     * @return self
     */
    public static function classIsImmutable($class)
    {
        return new static(sprintf('%s is immutable', $class));
    }

    /**
     * Thrown when accessing a result field on an unacknowledged write result.
     *
     * @param string $method Method name
     * @return self
     */
    public static function unacknowledgedWriteResultAccess($method)
    {
        return new static(sprintf('%s should not be called for an unacknowledged write result', $method));
    }
}
