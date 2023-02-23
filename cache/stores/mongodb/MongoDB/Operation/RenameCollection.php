<?php
/*
 * Copyright 2021-present MongoDB, Inc.
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

use function current;
use function is_array;
use function is_bool;

/**
 * Operation for the renameCollection command.
 *
 * @api
 * @see \MongoDB\Collection::rename()
 * @see \MongoDB\Database::renameCollection()
 * @see https://mongodb.com/docs/manual/reference/command/renameCollection/
 */
class RenameCollection implements Executable
{
    /** @var string */
    private $fromNamespace;

    /** @var string */
    private $toNamespace;

    /** @var array */
    private $options;

    /**
     * Constructs a renameCollection command.
     *
     * Supported options:
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be used
     *    for the returned command result document.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     *  * dropTarget (boolean): If true, MongoDB will drop the target before
     *    renaming the collection.
     *
     * @param string $fromDatabaseName   Database name
     * @param string $fromCollectionName Collection name
     * @param string $toDatabaseName     New database name
     * @param string $toCollectionName   New collection name
     * @param array  $options            Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $fromDatabaseName, string $fromCollectionName, string $toDatabaseName, string $toCollectionName, array $options = [])
    {
        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        if (isset($options['dropTarget']) && ! is_bool($options['dropTarget'])) {
            throw InvalidArgumentException::invalidType('"dropTarget" option', $options['dropTarget'], 'boolean');
        }

        $this->fromNamespace = $fromDatabaseName . '.' . $fromCollectionName;
        $this->toNamespace = $toDatabaseName . '.' . $toCollectionName;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return array|object Command result document
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['writeConcern'])) {
            throw UnsupportedException::writeConcernNotSupportedInTransaction();
        }

        $cursor = $server->executeWriteCommand('admin', $this->createCommand(), $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    /**
     * Create the renameCollection command.
     */
    private function createCommand(): Command
    {
        $cmd = [
            'renameCollection' => $this->fromNamespace,
            'to' => $this->toNamespace,
        ];

        foreach (['comment', 'dropTarget'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        return new Command($cmd);
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
}
