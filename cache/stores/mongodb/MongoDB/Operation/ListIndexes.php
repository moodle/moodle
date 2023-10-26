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

use EmptyIterator;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\CachingIterator;
use MongoDB\Model\IndexInfoIterator;
use MongoDB\Model\IndexInfoIteratorIterator;

use function is_integer;

/**
 * Operation for the listIndexes command.
 *
 * @api
 * @see \MongoDB\Collection::listIndexes()
 * @see https://mongodb.com/docs/manual/reference/command/listIndexes/
 */
class ListIndexes implements Executable
{
    /** @var integer */
    private static $errorCodeDatabaseNotFound = 60;

    /** @var integer */
    private static $errorCodeNamespaceNotFound = 26;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array */
    private $options;

    /**
     * Constructs a listIndexes command.
     *
     * Supported options:
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, array $options = [])
    {
        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
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
     * @see https://php.net/manual/en/mongodb-driver-server.executecommand.php
     */
    private function createOptions(): array
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
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    private function executeCommand(Server $server): IndexInfoIteratorIterator
    {
        $cmd = ['listIndexes' => $this->collectionName];

        foreach (['comment', 'maxTimeMS'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        try {
            $cursor = $server->executeReadCommand($this->databaseName, new Command($cmd), $this->createOptions());
        } catch (CommandException $e) {
            /* The server may return an error if the collection does not exist.
             * Check for possible error codes (see: SERVER-20463) and return an
             * empty iterator instead of throwing.
             */
            if ($e->getCode() === self::$errorCodeNamespaceNotFound || $e->getCode() === self::$errorCodeDatabaseNotFound) {
                return new IndexInfoIteratorIterator(new EmptyIterator());
            }

            throw $e;
        }

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);

        return new IndexInfoIteratorIterator(new CachingIterator($cursor), $this->databaseName . '.' . $this->collectionName);
    }
}
