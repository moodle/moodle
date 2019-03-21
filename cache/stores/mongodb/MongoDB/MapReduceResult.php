<?php
/*
 * Copyright 2017 MongoDB, Inc.
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

namespace MongoDB;

use IteratorAggregate;
use stdClass;
use Traversable;

/**
 * Result class for mapReduce command results.
 *
 * This class allows for iteration of mapReduce results irrespective of the
 * output method (e.g. inline, collection) via the IteratorAggregate interface.
 * It also provides access to command statistics.
 *
 * @api
 * @see \MongoDB\Collection::mapReduce()
 * @see https://docs.mongodb.com/manual/reference/command/mapReduce/
 */
class MapReduceResult implements IteratorAggregate
{
    private $getIterator;
    private $executionTimeMS;
    private $counts;
    private $timing;

    /**
     * Constructor.
     *
     * @internal
     * @param callable $getIterator Callback that returns a Traversable for mapReduce results
     * @param stdClass $result      Result document from the mapReduce command
     */
    public function __construct(callable $getIterator, stdClass $result)
    {
        $this->getIterator = $getIterator;
        $this->executionTimeMS = (integer) $result->timeMillis;
        $this->counts = (array) $result->counts;
        $this->timing = isset($result->timing) ? (array) $result->timing : [];
    }

    /**
     * Returns various count statistics from the mapReduce command.
     *
     * @return array
     */
    public function getCounts()
    {
        return $this->counts;
    }

    /**
     * Return the command execution time in milliseconds.
     *
     * @return integer
     */
    public function getExecutionTimeMS()
    {
        return $this->executionTimeMS;
    }

    /**
     * Return the mapReduce results as a Traversable.
     *
     * @see http://php.net/iteratoraggregate.getiterator
     * @return Traversable
     */
    public function getIterator()
    {
        return call_user_func($this->getIterator);
    }

    /**
     * Returns various timing statistics from the mapReduce command.
     *
     * Note: timing statistics are only available if the mapReduce command's
     * "verbose" option was true; otherwise, an empty array will be returned.
     *
     * @return array
     */
    public function getTiming()
    {
        return $this->timing;
    }
}
