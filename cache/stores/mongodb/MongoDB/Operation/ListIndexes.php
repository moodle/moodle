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

use MongoDB\Driver\Command;
use MongoDB\Driver\Query;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\CachingIterator;
use MongoDB\Model\IndexInfoIterator;
use MongoDB\Model\IndexInfoIteratorIterator;
use EmptyIterator;

/**
 * Operation for the listIndexes command.
 *
 * @api
 * @see \MongoDB\Collection::listIndexes()
 * @see http://docs.mongodb.org/manual/reference/command/listIndexes/
 */
class ListIndexes implements Executable
{
    private static $errorCodeDatabaseNotFound = 60;
    private static $errorCodeNamespaceNotFound = 26;

    private $databaseName;
    private $collectionName;
    private $options;

    /**
     * Constructs a listIndexes command.
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
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, array $options = [])
    {
        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], 'MongoDB\Driver\Session');
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
     * @return IndexInfoIterator
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        return $this->executeCommand($server);
    }

    /**
     * Create options for executing the command.
     *
     * Note: read preference is intentionally omitted, as the spec requires that
     * the command be executed on the primary.
     *
     * @see http://php.net/manual/en/mongodb-driver-server.executecommand.php
     * @return array
     */
    private function createOptions()
    {
        $options = [];

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        return $options;
    }

    /**
     * Returns information for all indexes for this collection using the
     * listIndexes command.
     *
     * @param Server $server
     * @return IndexInfoIteratorIterator
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    private function executeCommand(Server $server)
    {
        $cmd = ['listIndexes' => $this->collectionName];

        if (isset($this->options['maxTimeMS'])) {
            $cmd['maxTimeMS'] = $this->options['maxTimeMS'];
        }

        try {
            $cursor = $server->executeCommand($this->databaseName, new Command($cmd), $this->createOptions());
        } catch (DriverRuntimeException $e) {
            /* The server may return an error if the collection does not exist.
             * Check for possible error codes (see: SERVER-20463) and return an
             * empty iterator instead of throwing.
             */
            if ($e->getCode() === self::$errorCodeNamespaceNotFound || $e->getCode() === self::$errorCodeDatabaseNotFound) {
                return new IndexInfoIteratorIterator(new EmptyIterator);
            }

            throw $e;
        }

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);

        return new IndexInfoIteratorIterator(new CachingIterator($cursor));
    }
}
