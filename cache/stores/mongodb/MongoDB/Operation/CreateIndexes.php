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

namespace MongoDB\Operation;

use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\Command;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\IndexInput;

/**
 * Operation for the createIndexes command.
 *
 * @api
 * @see \MongoDB\Collection::createIndex()
 * @see \MongoDB\Collection::createIndexes()
 * @see http://docs.mongodb.org/manual/reference/command/createIndexes/
 */
class CreateIndexes implements Executable
{
    private static $wireVersionForCollation = 5;
    private static $wireVersionForWriteConcern = 5;

    private $databaseName;
    private $collectionName;
    private $indexes = [];
    private $isCollationUsed = false;
    private $options = [];

    /**
     * Constructs a createIndexes command.
     *
     * Supported options:
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     * @param string  $databaseName   Database name
     * @param string  $collectionName Collection name
     * @param array[] $indexes        List of index specifications
     * @param array   $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, array $indexes, array $options = [])
    {
        if (empty($indexes)) {
            throw new InvalidArgumentException('$indexes is empty');
        }

        $expectedIndex = 0;

        foreach ($indexes as $i => $index) {
            if ($i !== $expectedIndex) {
                throw new InvalidArgumentException(sprintf('$indexes is not a list (unexpected index: "%s")', $i));
            }

            if ( ! is_array($index)) {
                throw InvalidArgumentException::invalidType(sprintf('$index[%d]', $i), $index, 'array');
            }

            if ( ! isset($index['ns'])) {
                $index['ns'] = $databaseName . '.' . $collectionName;
            }

            if (isset($index['collation'])) {
                $this->isCollationUsed = true;
            }

            $this->indexes[] = new IndexInput($index);

            $expectedIndex += 1;
        }

        if (isset($options['maxTimeMS']) && !is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], 'MongoDB\Driver\Session');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return string[] The names of the created indexes
     * @throws UnsupportedException if collation or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        if ($this->isCollationUsed && ! \MongoDB\server_supports_feature($server, self::$wireVersionForCollation)) {
            throw UnsupportedException::collationNotSupported();
        }

        if (isset($this->options['writeConcern']) && ! \MongoDB\server_supports_feature($server, self::$wireVersionForWriteConcern)) {
            throw UnsupportedException::writeConcernNotSupported();
        }

        $this->executeCommand($server);

        return array_map(function(IndexInput $index) { return (string) $index; }, $this->indexes);
    }

    /**
     * Create options for executing the command.
     *
     * @see http://php.net/manual/en/mongodb-driver-server.executewritecommand.php
     * @return array
     */
    private function createOptions()
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
     * @param Server $server
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    private function executeCommand(Server $server)
    {
        $cmd = [
            'createIndexes' => $this->collectionName,
            'indexes' => $this->indexes,
        ];

        if (isset($this->options['maxTimeMS'])) {
            $cmd['maxTimeMS'] = $this->options['maxTimeMS'];
        }

        $server->executeWriteCommand($this->databaseName, new Command($cmd), $this->createOptions());
    }
}
