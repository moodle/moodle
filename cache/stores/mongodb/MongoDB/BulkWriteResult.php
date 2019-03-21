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
 * Result class for a bulk write operation.
 */
class BulkWriteResult
{
    private $writeResult;
    private $insertedIds;
    private $isAcknowledged;

    /**
     * Constructor.
     *
     * @param WriteResult $writeResult
     * @param mixed[]     $insertedIds
     */
    public function __construct(WriteResult $writeResult, array $insertedIds)
    {
        $this->writeResult = $writeResult;
        $this->insertedIds = $insertedIds;
        $this->isAcknowledged = $writeResult->isAcknowledged();
    }

    /**
     * Return the number of documents that were deleted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
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
     * Return the number of documents that were inserted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getInsertedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getInsertedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return a map of the inserted documents' IDs.
     *
     * The index of each ID in the map corresponds to each document's position
     * in the bulk operation. If a document had an ID prior to inserting (i.e.
     * the driver did not generate an ID), the index will contain its "_id"
     * field value. Any driver-generated ID will be a MongoDB\BSON\ObjectId
     * instance.
     *
     * @return mixed[]
     */
    public function getInsertedIds()
    {
        return $this->insertedIds;
    }

    /**
     * Return the number of documents that were matched by the filter.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getMatchedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getMatchedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return the number of documents that were modified.
     *
     * This value is undefined (i.e. null) if the write executed as a legacy
     * operation instead of command.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
     * @return integer|null
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getModifiedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getModifiedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return the number of documents that were upserted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
     * @return integer
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getUpsertedCount()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getUpsertedCount();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return a map of the upserted documents' IDs.
     *
     * The index of each ID in the map corresponds to each document's position
     * in bulk operation. If a document had an ID prior to upserting (i.e. the
     * server did not need to generate an ID), this will contain its "_id". Any
     * server-generated ID will be a MongoDB\BSON\ObjectId instance.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see BulkWriteResult::isAcknowledged()
     * @return mixed[]
     * @throws BadMethodCallException is the write result is unacknowledged
     */
    public function getUpsertedIds()
    {
        if ($this->isAcknowledged) {
            return $this->writeResult->getUpsertedIds();
        }

        throw BadMethodCallException::unacknowledgedWriteResultAccess(__METHOD__);
    }

    /**
     * Return whether this update was acknowledged by the server.
     *
     * If the update was not acknowledged, other fields from the WriteResult
     * (e.g. matchedCount) will be undefined.
     *
     * @return boolean
     */
    public function isAcknowledged()
    {
        return $this->isAcknowledged;
    }
}
