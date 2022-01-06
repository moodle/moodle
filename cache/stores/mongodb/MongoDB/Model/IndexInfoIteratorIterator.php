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

namespace MongoDB\Model;

use IteratorIterator;

/**
 * IndexInfoIterator for both listIndexes command and legacy query results.
 *
 * This common iterator may be used to wrap a Cursor returned by both the
 * listIndexes command and, for legacy servers, queries on the "system.indexes"
 * collection.
 *
 * @internal
 * @see \MongoDB\Collection::listIndexes()
 * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-indexes.rst
 * @see http://docs.mongodb.org/manual/reference/command/listIndexes/
 * @see http://docs.mongodb.org/manual/reference/system-collections/
 */
class IndexInfoIteratorIterator extends IteratorIterator implements IndexInfoIterator
{
    /**
     * Return the current element as an IndexInfo instance.
     *
     * @see IndexInfoIterator::current()
     * @see http://php.net/iterator.current
     * @return IndexInfo
     */
    public function current()
    {
        return new IndexInfo(parent::current());
    }
}
