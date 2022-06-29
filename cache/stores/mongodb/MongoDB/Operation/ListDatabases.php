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

use MongoDB\Command\ListDatabases as ListDatabasesCommand;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Model\DatabaseInfoIterator;
use MongoDB\Model\DatabaseInfoLegacyIterator;

/**
 * Operation for the ListDatabases command.
 *
 * @api
 * @see \MongoDB\Client::listDatabases()
 * @see http://docs.mongodb.org/manual/reference/command/ListDatabases/
 */
class ListDatabases implements Executable
{
    /** @var ListDatabasesCommand */
    private $listDatabases;

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
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     * @param array $options Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(array $options = [])
    {
        $this->listDatabases = new ListDatabasesCommand(['nameOnly' => false] + $options);
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
        return new DatabaseInfoLegacyIterator($this->listDatabases->execute($server));
    }
}
