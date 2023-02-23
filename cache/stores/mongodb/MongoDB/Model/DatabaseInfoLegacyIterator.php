<?php
/*
 * Copyright 2015-present MongoDB, Inc.
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

use function current;
use function key;
use function next;
use function reset;

/**
 * DatabaseInfoIterator for inline listDatabases command results.
 *
 * This iterator may be used to wrap the array returned within the listDatabases
 * command's single-document result.
 *
 * @internal
 * @see \MongoDB\Client::listDatabases()
 * @see https://mongodb.com/docs/manual/reference/command/listDatabases/
 */
class DatabaseInfoLegacyIterator implements DatabaseInfoIterator
{
    /** @var array */
    private $databases;

    public function __construct(array $databases)
    {
        $this->databases = $databases;
    }

    /**
     * Return the current element as a DatabaseInfo instance.
     *
     * @see DatabaseInfoIterator::current()
     * @see https://php.net/iterator.current
     */
    public function current(): DatabaseInfo
    {
        return new DatabaseInfo(current($this->databases));
    }

    /**
     * Return the key of the current element.
     *
     * @see https://php.net/iterator.key
     */
    public function key(): int
    {
        return key($this->databases);
    }

    /**
     * Move forward to next element.
     *
     * @see https://php.net/iterator.next
     */
    public function next(): void
    {
        next($this->databases);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see https://php.net/iterator.rewind
     */
    public function rewind(): void
    {
        reset($this->databases);
    }

    /**
     * Checks if current position is valid.
     *
     * @see https://php.net/iterator.valid
     */
    public function valid(): bool
    {
        return key($this->databases) !== null;
    }
}
