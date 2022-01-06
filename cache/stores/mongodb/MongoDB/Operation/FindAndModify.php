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
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use function current;
use function is_array;
use function is_bool;
use function is_integer;
use function is_object;
use function MongoDB\create_field_path_type_map;
use function MongoDB\is_pipeline;
use function MongoDB\server_supports_feature;

/**
 * Operation for the findAndModify command.
 *
 * This class is used internally by the FindOneAndDelete, FindOneAndReplace, and
 * FindOneAndUpdate operation classes.
 *
 * @internal
 * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
 */
class FindAndModify implements Executable, Explainable
{
    /** @var integer */
    private static $wireVersionForArrayFilters = 6;

    /** @var integer */
    private static $wireVersionForCollation = 5;

    /** @var integer */
    private static $wireVersionForDocumentLevelValidation = 4;

    /** @var integer */
    private static $wireVersionForWriteConcern = 4;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array */
    private $options;

    /**
     * Constructs a findAndModify command.
     *
     * Supported options:
     *
     *  * arrayFilters (document array): A set of filters specifying to which
     *    array elements an update should apply.
     *
     *    This is not supported for server versions < 3.6 and will result in an
     *    exception at execution time if used.
     *
     *  * collation (document): Collation specification.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation.
     *
     *    For servers < 3.2, this option is ignored as document level validation
     *    is not available.
     *
     *  * fields (document): Limits the fields to return for the matching
     *    document.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * new (boolean): When true, returns the modified document rather than
     *    the original. This option is ignored for remove operations. The
     *    The default is false.
     *
     *  * query (document): Query by which to filter documents.
     *
     *  * remove (boolean): When true, removes the matched document. This option
     *    cannot be true if the update option is set. The default is false.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * sort (document): Determines which document the operation modifies if
     *    the query selects multiple documents.
     *
     *  * typeMap (array): Type map for BSON deserialization.
     *
     *  * update (document): Update or replacement to apply to the matched
     *    document. This option cannot be set if the remove option is true.
     *
     *  * upsert (boolean): When true, a new document is created if no document
     *    matches the query. This option is ignored for remove operations. The
     *    default is false.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     *    This is not supported for server versions < 3.2 and will result in an
     *    exception at execution time if used.
     *
     * @param string $databaseName   Database name
     * @param string $collectionName Collection name
     * @param array  $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, array $options)
    {
        $options += [
            'new' => false,
            'remove' => false,
            'upsert' => false,
        ];

        if (isset($options['arrayFilters']) && ! is_array($options['arrayFilters'])) {
            throw InvalidArgumentException::invalidType('"arrayFilters" option', $options['arrayFilters'], 'array');
        }

        if (isset($options['bypassDocumentValidation']) && ! is_bool($options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $options['bypassDocumentValidation'], 'boolean');
        }

        if (isset($options['collation']) && ! is_array($options['collation']) && ! is_object($options['collation'])) {
            throw InvalidArgumentException::invalidType('"collation" option', $options['collation'], 'array or object');
        }

        if (isset($options['fields']) && ! is_array($options['fields']) && ! is_object($options['fields'])) {
            throw InvalidArgumentException::invalidType('"fields" option', $options['fields'], 'array or object');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (! is_bool($options['new'])) {
            throw InvalidArgumentException::invalidType('"new" option', $options['new'], 'boolean');
        }

        if (isset($options['query']) && ! is_array($options['query']) && ! is_object($options['query'])) {
            throw InvalidArgumentException::invalidType('"query" option', $options['query'], 'array or object');
        }

        if (! is_bool($options['remove'])) {
            throw InvalidArgumentException::invalidType('"remove" option', $options['remove'], 'boolean');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['sort']) && ! is_array($options['sort']) && ! is_object($options['sort'])) {
            throw InvalidArgumentException::invalidType('"sort" option', $options['sort'], 'array or object');
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['update']) && ! is_array($options['update']) && ! is_object($options['update'])) {
            throw InvalidArgumentException::invalidType('"update" option', $options['update'], 'array or object');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (! is_bool($options['upsert'])) {
            throw InvalidArgumentException::invalidType('"upsert" option', $options['upsert'], 'boolean');
        }

        if (! (isset($options['update']) xor $options['remove'])) {
            throw new InvalidArgumentException('The "remove" option must be true or an "update" document must be specified, but not both');
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
     * @return array|object|null
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if array filters, collation, or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        if (isset($this->options['arrayFilters']) && ! server_supports_feature($server, self::$wireVersionForArrayFilters)) {
            throw UnsupportedException::arrayFiltersNotSupported();
        }

        if (isset($this->options['collation']) && ! server_supports_feature($server, self::$wireVersionForCollation)) {
            throw UnsupportedException::collationNotSupported();
        }

        if (isset($this->options['writeConcern']) && ! server_supports_feature($server, self::$wireVersionForWriteConcern)) {
            throw UnsupportedException::writeConcernNotSupported();
        }

        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['writeConcern'])) {
            throw UnsupportedException::writeConcernNotSupportedInTransaction();
        }

        $cursor = $server->executeWriteCommand($this->databaseName, new Command($this->createCommandDocument($server)), $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap(create_field_path_type_map($this->options['typeMap'], 'value'));
        }

        $result = current($cursor->toArray());

        return isset($result->value) ? $result->value : null;
    }

    public function getCommandDocument(Server $server)
    {
        return $this->createCommandDocument($server);
    }

    /**
     * Create the findAndModify command document.
     *
     * @param Server $server
     * @return array
     */
    private function createCommandDocument(Server $server)
    {
        $cmd = ['findAndModify' => $this->collectionName];

        if ($this->options['remove']) {
            $cmd['remove'] = true;
        } else {
            $cmd['new'] = $this->options['new'];
            $cmd['upsert'] = $this->options['upsert'];
        }

        foreach (['collation', 'fields', 'query', 'sort'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = (object) $this->options[$option];
            }
        }

        if (isset($this->options['update'])) {
            $cmd['update'] = is_pipeline($this->options['update'])
                ? $this->options['update']
                : (object) $this->options['update'];
        }

        if (isset($this->options['arrayFilters'])) {
            $cmd['arrayFilters'] = $this->options['arrayFilters'];
        }

        if (isset($this->options['maxTimeMS'])) {
            $cmd['maxTimeMS'] = $this->options['maxTimeMS'];
        }

        if (! empty($this->options['bypassDocumentValidation']) &&
            server_supports_feature($server, self::$wireVersionForDocumentLevelValidation)
        ) {
            $cmd['bypassDocumentValidation'] = $this->options['bypassDocumentValidation'];
        }

        return $cmd;
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
