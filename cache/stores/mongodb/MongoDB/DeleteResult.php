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

namespace MongoDB;

use MongoDB\Driver\WriteResult;
use MongoDB\Exception\BadMethodCallException;

/**
 * Result class for a delete operation.
 */
class DeleteResult
{
    /** @var WriteResult */
    private $writeResult;

    /** @var boolean */
    private $isAcknowledged;

    public function __construct(WriteResult $writeResult)
    {
        $this->writeResult = $writeResult;
        $this->isAcknowledged = $writeResult->isAcknowledged();
    }

    /**
     * Return the number of documents that were deleted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see DeleteResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getDeletedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getDeletedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return whether this delete was acknowledged by the server.
     *
     * If the delete was not acknowledged, other fields from the WriteResult
     * (e.g. deletedCount) will be undefined.
     *
     * @return boolean
     */
    public function isAcknowledged()
    {
        return $this->isAcknowledged;
    }
}
