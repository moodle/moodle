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

namespace MongoDB\Operation;

use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\InsertOneResult;

use function is_array;
use function is_bool;
use function is_object;

/**
 * Operation for inserting a single document with the insert command.
 *
 * @api
 * @see \MongoDB\Collection::insertOne()
 * @see https://mongodb.com/docs/manual/reference/command/insert/
 */
class InsertOne implements Executable
{
    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array|object */
    private $document;

    /** @var array */
    private $options;

    /**
     * Constructs an insert command.
     *
     * Supported options:
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $document       Document to insert
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, $document, array $options = [])
    {
        if (! is_array($document) && ! is_object($document)) {
            throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
        }

        if (isset($options['bypassDocumentValidation']) && ! is_bool($options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $options['bypassDocumentValidation'], 'boolean');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['bypassDocumentValidation']) && ! $options['bypassDocumentValidation']) {
            unset($options['bypassDocumentValidation']);
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->document = $document;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return InsertOneResult
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if (isset($this->options['writeConcern']) && $inTransaction) {
            throw UnsupportedException::writeConcernNotSupportedInTransaction();
        }

        $bulk = new Bulk($this->createBulkWriteOptions());
        $insertedId = $bulk->insert($this->document);

        $writeResult = $server->executeBulkWrite($this->databaseName . '.' . $this->collectionName, $bulk, $this->createExecuteOptions());

        return new InsertOneResult($writeResult, $insertedId);
    }

    /**
     * Create options for constructing the bulk write.
     *
     * @see https://php.net/manual/en/mongodb-driver-bulkwrite.construct.php
     */
    private function createBulkWriteOptions(): array
    {
        $options = [];

        foreach (['bypassDocumentValidation', 'comment'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = $this->options[$option];
            }
        }

        return $options;
    }

    /**
     * Create options for executing the bulk write.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executebulkwrite.php
     */
    private function createExecuteOptions(): array
    {
        $options = [];

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        if (isset($this->options['writeConcern'])) {
            $options['writeConcern'] = $this->options['writeConcern'];
        }

        return $options;
    }
}
