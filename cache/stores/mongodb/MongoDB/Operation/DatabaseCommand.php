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
use MongoDB\Driver\Cursor;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use function is_array;
use function is_object;

/**
 * Operation for executing a database command.
 *
 * @api
 * @see \MongoDB\Database::command()
 */
class DatabaseCommand implements Executable
{
    /** @var string */
    private $databaseName;

    /** @var array|Command|object */
    private $command;

    /** @var array */
    private $options;

    /**
     * Constructs a command.
     *
     * Supported options:
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The read preference to
     *    use when executing the command. This may be used when issuing the
     *    command to a replica set or mongos node to ensure that the driver sets
     *    the wire protocol accordingly or adds the read preference to the
     *    command document, respectively.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     * @param string       $databaseName Database name
     * @param array|object $command      Command document
     * @param array        $options      Options for command execution
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $command, array $options = [])
    {
        if (! is_array($command) && ! is_object($command)) {
            throw InvalidArgumentException::invalidType('$command', $command, 'array or object');
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        $this->databaseName = (string) $databaseName;
        $this->command = $command instanceof Command ? $command : new Command($command);
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return Cursor
     */
    public function execute(Server $server)
    {
        $cursor = $server->executeCommand($this->databaseName, $this->command, $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return $cursor;
    }

    /**
     * Create options for executing the command.
     *
     * @see http://php.net/manual/en/mongodb-driver-server.executecommand.php
     * @return array
     */
    private function createOptions()
    {
        $options = [];

        if (isset($this->options['readPreference'])) {
            $options['readPreference'] = $this->options['readPreference'];
        }

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        return $options;
    }
}
