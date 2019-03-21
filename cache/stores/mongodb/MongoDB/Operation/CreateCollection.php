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
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for the create command.
 *
 * @api
 * @see \MongoDB\Database::createCollection()
 * @see http://docs.mongodb.org/manual/reference/command/create/
 */
class CreateCollection implements Executable
{
    const USE_POWER_OF_2_SIZES = 1;
    const NO_PADDING = 2;

    private static $wireVersionForCollation = 5;
    private static $wireVersionForWriteConcern = 5;

    private $databaseName;
    private $collectionName;
    private $options = [];

    /**
     * Constructs a create command.
     *
     * Supported options:
     *
     *  * autoIndexId (boolean): Specify false to disable the automatic creation
     *    of an index on the _id field. For replica sets, this option cannot be
     *    false. The default is true.
     *
     *    This option has been deprecated since MongoDB 3.2. As of MongoDB 4.0,
     *    this option cannot be false when creating a replicated collection
     *    (i.e. a collection outside of the local database in any mongod mode).
     *
     *  * capped (boolean): Specify true to create a capped collection. If set,
     *    the size option must also be specified. The default is false.
     *
     *  * collation (document): Collation specification.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     *  * flags (integer): Options for the MMAPv1 storage engine only. Must be a
     *    bitwise combination CreateCollection::USE_POWER_OF_2_SIZES and
     *    CreateCollection::NO_PADDING. The default is
     *    CreateCollection::USE_POWER_OF_2_SIZES.
     *
     *  * indexOptionDefaults (document): Default configuration for indexes when
     *    creating the collection.
     *
     *  * max (integer): The maximum number of documents allowed in the capped
     *    collection. The size option takes precedence over this limit.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * size (integer): The maximum number of bytes for a capped collection.
     *
     *  * storageEngine (document): Storage engine options.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will only be
     *    used for the returned command result document.
     *
     *  * validationAction (string): Validation action.
     *
     *  * validationLevel (string): Validation level.
     *
     *  * validator (document): Validation rules or expressions.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     * @see http://source.wiredtiger.com/2.4.1/struct_w_t___s_e_s_s_i_o_n.html#a358ca4141d59c345f401c58501276bbb
     * @see https://docs.mongodb.org/manual/core/document-validation/
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, array $options = [])
    {
        if (isset($options['autoIndexId']) && ! is_bool($options['autoIndexId'])) {
            throw InvalidArgumentException::invalidType('"autoIndexId" option', $options['autoIndexId'], 'boolean');
        }

        if (isset($options['capped']) && ! is_bool($options['capped'])) {
            throw InvalidArgumentException::invalidType('"capped" option', $options['capped'], 'boolean');
        }

        if (isset($options['collation']) && ! is_array($options['collation']) && ! is_object($options['collation'])) {
            throw InvalidArgumentException::invalidType('"collation" option', $options['collation'], 'array or object');
        }

        if (isset($options['flags']) && ! is_integer($options['flags'])) {
            throw InvalidArgumentException::invalidType('"flags" option', $options['flags'], 'integer');
        }

        if (isset($options['indexOptionDefaults']) && ! is_array($options['indexOptionDefaults']) && ! is_object($options['indexOptionDefaults'])) {
            throw InvalidArgumentException::invalidType('"indexOptionDefaults" option', $options['indexOptionDefaults'], 'array or object');
        }

        if (isset($options['max']) && ! is_integer($options['max'])) {
            throw InvalidArgumentException::invalidType('"max" option', $options['max'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], 'MongoDB\Driver\Session');
        }

        if (isset($options['size']) && ! is_integer($options['size'])) {
            throw InvalidArgumentException::invalidType('"size" option', $options['size'], 'integer');
        }

        if (isset($options['storageEngine']) && ! is_array($options['storageEngine']) && ! is_object($options['storageEngine'])) {
            throw InvalidArgumentException::invalidType('"storageEngine" option', $options['storageEngine'], 'array or object');
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['validationAction']) && ! is_string($options['validationAction'])) {
            throw InvalidArgumentException::invalidType('"validationAction" option', $options['validationAction'], 'string');
        }

        if (isset($options['validationLevel']) && ! is_string($options['validationLevel'])) {
            throw InvalidArgumentException::invalidType('"validationLevel" option', $options['validationLevel'], 'string');
        }

        if (isset($options['validator']) && ! is_array($options['validator']) && ! is_object($options['validator'])) {
            throw InvalidArgumentException::invalidType('"validator" option', $options['validator'], 'array or object');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        if (isset($options['autoIndexId'])) {
            trigger_error('The "autoIndexId" option is deprecated and will be removed in a future release', E_USER_DEPRECATED);
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
     * @return array|object Command result document
     * @throws UnsupportedException if collation or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        if (isset($this->options['collation']) && ! \MongoDB\server_supports_feature($server, self::$wireVersionForCollation)) {
            throw UnsupportedException::collationNotSupported();
        }

        if (isset($this->options['writeConcern']) && ! \MongoDB\server_supports_feature($server, self::$wireVersionForWriteConcern)) {
            throw UnsupportedException::writeConcernNotSupported();
        }

        $cursor = $server->executeWriteCommand($this->databaseName, $this->createCommand(), $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    /**
     * Create the create command.
     *
     * @return Command
     */
    private function createCommand()
    {
        $cmd = ['create' => $this->collectionName];

        foreach (['autoIndexId', 'capped', 'flags', 'max', 'maxTimeMS', 'size', 'validationAction', 'validationLevel'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        foreach (['collation', 'indexOptionDefaults', 'storageEngine', 'validator'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = (object) $this->options[$option];
            }
        }

        return new Command($cmd);
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
}
