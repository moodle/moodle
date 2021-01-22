<?php
/*
 * Copyright 2020-present MongoDB, Inc.
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

namespace MongoDB\Command;

use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Operation\Executable;
use function current;
use function is_array;
use function is_bool;
use function is_integer;
use function is_object;

/**
 * Wrapper for the ListDatabases command.
 *
 * @internal
 * @see http://docs.mongodb.org/manual/reference/command/listDatabases/
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
     *  * authorizedDatabases (boolean): Determines which databases are returned
     *    based on the user privileges.
     *
     *    For servers < 4.0.5, this option is ignored.
     *
     *  * filter (document): Query by which to filter databases.
     *
     *    For servers < 3.6, this option is ignored.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * nameOnly (boolean): A flag to indicate whether the command should
     *    return just the database names, or return both database names and size
     *    information.
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
        if (isset($options['authorizedDatabases']) && ! is_bool($options['authorizedDatabases'])) {
            throw InvalidArgumentException::invalidType('"authorizedDatabases" option', $options['authorizedDatabases'], 'boolean');
        }

        if (isset($options['filter']) && ! is_array($options['filter']) && ! is_object($options['filter'])) {
            throw InvalidArgumentException::invalidType('"filter" option', $options['filter'], ['array', 'object']);
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['nameOnly']) && ! is_bool($options['nameOnly'])) {
            throw InvalidArgumentException::invalidType('"nameOnly" option', $options['nameOnly'], 'boolean');
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
     * @return array An array of database info structures
     * @throws UnexpectedValueException if the command response was malformed
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $cmd = ['listDatabases' => 1];

        if (isset($this->options['authorizedDatabases'])) {
            $cmd['authorizedDatabases'] = $this->options['authorizedDatabases'];
        }

        if (! empty($this->options['filter'])) {
            $cmd['filter'] = (object) $this->options['filter'];
        }

        if (isset($this->options['maxTimeMS'])) {
            $cmd['maxTimeMS'] = $this->options['maxTimeMS'];
        }

        if (isset($this->options['nameOnly'])) {
            $cmd['nameOnly'] = $this->options['nameOnly'];
        }

        $cursor = $server->executeReadCommand('admin', new Command($cmd), $this->createOptions());
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $result = current($cursor->toArray());

        if (! isset($result['databases']) || ! is_array($result['databases'])) {
            throw new UnexpectedValueException('listDatabases command did not return a "databases" array');
        }

        return $result['databases'];
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
