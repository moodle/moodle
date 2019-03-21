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

namespace MongoDB\GridFS\Exception;

use MongoDB\Exception\RuntimeException;

class CorruptFileException extends RuntimeException
{
    /**
     * Thrown when a chunk is not found for an expected index.
     *
     * @param integer $expectedIndex Expected index number
     * @return self
     */
    public static function missingChunk($expectedIndex)
    {
        return new static(sprintf('Chunk not found for index "%d"', $expectedIndex));
    }

    /**
     * Thrown when a chunk has an unexpected index number.
     *
     * @param integer $index         Actual index number (i.e. "n" field)
     * @param integer $expectedIndex Expected index number
     * @return self
     */
    public static function unexpectedIndex($index, $expectedIndex)
    {
        return new static(sprintf('Expected chunk to have index "%d" but found "%d"', $expectedIndex, $index));
    }

    /**
     * Thrown when a chunk has an unexpected data size.
     *
     * @param integer $size         Actual size (i.e. "data" field length)
     * @param integer $expectedSize Expected size
     * @return self
     */
    public static function unexpectedSize($size, $expectedSize)
    {
        return new static(sprintf('Expected chunk to have size "%d" but found "%d"', $expectedSize, $size));
    }
}
