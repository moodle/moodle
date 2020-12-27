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
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Model\DatabaseInfoIterator;
use MongoDB\Model\DatabaseInfoLegacyIterator;
use function current;
use function is_array;
use function is_integer;
use function is_object;

/**
 * Operation for the ListDatabases command.
 *
 * @api
 * @see \MongoDB\Client::listDatabases()
 * @see http://docs.mongodb.org/manual/reference/command/ListDatabases/
 */
class ListDatabases implements Executable
{
    /** @var array */
    private $options;

    /**
     * Constructs a listDatabases command.
     *
     * Supported options:
     *
     *  * filter (document): Query by which to filter databases.
     *
     *    For servers < 3.6, this option is ignored.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     * @param array $options Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(array $options = [])
    {
        if (isset($options['filter']) && ! is_array($options['filter']) && ! is_object($options['filter'])) {
            throw InvalidArgumentException::invalidType('"filter" option', $options['filter'], 'array or object');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return DatabaseInfoIterator
     * @throws UnexpectedValueException if the command response was malformed
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $cmd = ['listDatabases' => 1];

        if (! empty($this->options['filter'])) {
            $cmd['filter'] = (object) $this->options['filter'];
        }

        if (isset($this->options['maxTimeMS'])) {
            $cmd['maxTimeMS'] = $this->options['maxTimeMS'];
        }

        $cursor = $server->executeReadCommand('admin', new Command($cmd), $this->createOptions());
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $result = current($cursor->toArray());

        if (! isset($result['databases']) || ! is_array($result['databases'])) {
            throw new UnexpectedValueException('listDatabases command did not return a "databases" array');
        }

        /* Return an Iterator instead of an array in case listDatabases is
         * eventually changed to return a command cursor, like the collection
         * and index enumeration commands. This makes the "totalSize" command
         * field inaccessible, but users can manually invoke the command if they
         * need that value.
         */
        return new DatabaseInfoLegacyIterator($result['databases']);
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
}
