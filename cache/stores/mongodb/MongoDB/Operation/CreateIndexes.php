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

use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\IndexInput;

use function array_map;
use function is_array;
use function is_integer;
use function is_string;
use function MongoDB\server_supports_feature;
use function sprintf;

/**
 * Operation for the createIndexes command.
 *
 * @api
 * @see \MongoDB\Collection::createIndex()
 * @see \MongoDB\Collection::createIndexes()
 * @see https://mongodb.com/docs/manual/reference/command/createIndexes/
 */
class CreateIndexes implements Executable
{
    /** @var integer */
    private static $wireVersionForCommitQuorum = 9;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array */
    private $indexes = [];

    /** @var array */
    private $options = [];

    /**
     * Constructs a createIndexes command.
     *
     * Supported options:
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * commitQuorum (integer|string): Specifies how many data-bearing members
     *    of a replica set, including the primary, must complete the index
     *    builds successfully before the primary marks the indexes as ready.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string  $databaseName   Database name
     * @param string  $collectionName Collection name
     * @param array[] $indexes        List of index specifications
     * @param array   $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, array $indexes, array $options = [])
    {
        if (empty($indexes)) {
            throw new InvalidArgumentException('$indexes is empty');
        }

        $expectedIndex = 0;

        foreach ($indexes as $i => $index) {
            if ($i !== $expectedIndex) {
                throw new InvalidArgumentException(sprintf('$indexes is not a list (unexpected index: "%s")', $i));
            }

            if (! is_array($index)) {
                throw InvalidArgumentException::invalidType(sprintf('$index[%d]', $i), $index, 'array');
            }

            $this->indexes[] = new IndexInput($index);

            $expectedIndex += 1;
        }

        if (isset($options['commitQuorum']) && ! is_string($options['commitQuorum']) && ! is_integer($options['commitQuorum'])) {
            throw InvalidArgumentException::invalidType('"commitQuorum" option', $options['commitQuorum'], ['integer', 'string']);
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return string[] The names of the created indexes
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['writeConcern'])) {
            throw UnsupportedException::writeConcernNotSupportedInTransaction();
        }

        $this->executeCommand($server);

        return array_map(function (IndexInput $index) {
            return (string) $index;
        }, $this->indexes);
    }

    /**
     * Create options for executing the command.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executewritecommand.php
     */
    private function createOptions(): array
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

    /**
     * Create one or more indexes for the collection using the createIndexes
     * command.
     *
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    private function executeCommand(Server $server): void
    {
        $cmd = [
            'createIndexes' => $this->collectionName,
            'indexes' => $this->indexes,
        ];

        if (isset($this->options['commitQuorum'])) {
            /* Drivers MUST manually raise an error if this option is specified
             * when creating an index on a pre 4.4 server. */
            if (! server_supports_feature($server, self::$wireVersionForCommitQuorum)) {
                throw UnsupportedException::commitQuorumNotSupported();
            }

            $cmd['commitQuorum'] = $this->options['commitQuorum'];
        }

        foreach (['comment', 'maxTimeMS'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        $server->executeWriteCommand($this->databaseName, new Command($cmd), $this->createOptions());
    }
}
