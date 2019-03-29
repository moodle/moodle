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
 * CollectionInfoIterator for listCollections command results.
 *
 * This iterator may be used to wrap a Cursor returned by the listCollections
 * command.
 *
 * @internal
 * @see \MongoDB\Database::listCollections()
 * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-collections.rst
 * @see http://docs.mongodb.org/manual/reference/command/listCollections/
 */
class CollectionInfoCommandIterator extends IteratorIterator implements CollectionInfoIterator
{
    /**
     * Return the current element as a CollectionInfo instance.
     *
     * @see CollectionInfoIterator::current()
     * @see http://php.net/iterator.current
     * @return CollectionInfo
     */
    public function current()
    {
        return new CollectionInfo(parent::current());
    }
}
