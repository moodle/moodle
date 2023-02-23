<?php
/*
 * Copyright 2020-present MongoDB, Inc.
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

use Iterator;
use MongoDB\Command\ListCollections as ListCollectionsCommand;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\CallbackIterator;

/**
 * Operation for the listCollectionNames helper.
 *
 * @api
 * @see \MongoDB\Database::listCollectionNames()
 * @see https://mongodb.com/docs/manual/reference/command/listCollections/
 */
class ListCollectionNames implements Executable
{
    /** @var ListCollectionsCommand */
    private $listCollections;

    /**
     * Constructs a listCollections command.
     *
     * Supported options:
     *
     *  * authorizedCollections (boolean): Determines which collections are
     *    returned based on the user privileges.
     *
     *    For servers < 4.0, this option is ignored.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * filter (document): Query by which to filter collections.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     * @param string $databaseName Database name
     * @param array  $options      Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, array $options = [])
    {
        $this->listCollections = new ListCollectionsCommand($databaseName, ['nameOnly' => true] + $options);
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return Iterator
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): Iterator
    {
        return new CallbackIterator(
            $this->listCollections->execute($server),
            function (array $collectionInfo) {
                return $collectionInfo['name'];
            }
        );
    }
}
