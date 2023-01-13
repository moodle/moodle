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

use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

use function is_array;
use function is_bool;
use function is_integer;
use function is_object;
use function is_string;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Operation for the find command.
 *
 * @api
 * @see \MongoDB\Collection::find()
 * @see https://mongodb.com/docs/manual/tutorial/query-documents/
 * @see https://mongodb.com/docs/manual/reference/operator/query-modifier/
 */
class Find implements Executable, Explainable
{
    public const NON_TAILABLE = 1;
    public const TAILABLE = 2;
    public const TAILABLE_AWAIT = 3;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array|object */
    private $filter;

    /** @var array */
    private $options;

    /**
     * Constructs a find command.
     *
     * Supported options:
     *
     *  * allowDiskUse (boolean): Enables writing to temporary files. When set
     *    to true, queries can write data to the _tmp sub-directory in the
     *    dbPath directory.
     *
     *  * allowPartialResults (boolean): Get partial results from a mongos if
     *    some shards are inaccessible (instead of throwing an error).
     *
     *  * batchSize (integer): The number of documents to return per batch.
     *
     *  * collation (document): Collation specification.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    Only string values are supported for server versions < 4.4.
     *
     *  * cursorType (enum): Indicates the type of cursor to use. Must be either
     *    NON_TAILABLE, TAILABLE, or TAILABLE_AWAIT. The default is
     *    NON_TAILABLE.
     *
     *  * hint (string|document): The index to use. Specify either the index
     *    name as a string or the index key pattern as a document. If specified,
     *    then the query system will only consider plans using the hinted index.
     *
     *  * limit (integer): The maximum number of documents to return.
     *
     *  * max (document): The exclusive upper bound for a specific index.
     *
     *  * maxAwaitTimeMS (integer): The maxium amount of time for the server to wait
     *    on new documents to satisfy a query, if cursorType is TAILABLE_AWAIT.
     *
     *  * maxScan (integer): Maximum number of documents or index keys to scan
     *    when executing the query.
     *
     *    This option has been deprecated since version 1.4.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run. If "$maxTimeMS" also exists in the modifiers document, this
     *    option will take precedence.
     *
     *  * min (document): The inclusive upper bound for a specific index.
     *
     *  * modifiers (document): Meta operators that modify the output or
     *    behavior of a query. Use of these operators is deprecated in favor of
     *    named options.
     *
     *  * noCursorTimeout (boolean): The server normally times out idle cursors
     *    after an inactivity period (10 minutes) to prevent excess memory use.
     *    Set this option to prevent that.
     *
     *  * oplogReplay (boolean): Internal replication use only. The driver
     *    should not set this. This option is deprecated as of MongoDB 4.4.
     *
     *  * projection (document): Limits the fields to return for the matching
     *    document.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * returnKey (boolean): If true, returns only the index keys in the
     *    resulting documents.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * showRecordId (boolean): Determines whether to return the record
     *    identifier for each document. If true, adds a field $recordId to the
     *    returned documents.
     *
     *  * skip (integer): The number of documents to skip before returning.
     *
     *  * snapshot (boolean): Prevents the cursor from returning a document more
     *    than once because of an intervening write operation.
     *
     *    This options has been deprecated since version 1.4.
     *
     *  * sort (document): The order in which to return matching documents. If
     *    "$orderby" also exists in the modifiers document, this option will
     *    take precedence.
     *
     *  * let (document): Map of parameter names and values. Values must be
     *    constant or closed expressions that do not reference document fields.
     *    Parameters can then be accessed as variables in an aggregate
     *    expression context (e.g. "$$var").
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $filter         Query by which to filter documents
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, $filter, array $options = [])
    {
        if (! is_array($filter) && ! is_object($filter)) {
            throw InvalidArgumentException::invalidType('$filter', $filter, 'array or object');
        }

        if (isset($options['allowDiskUse']) && ! is_bool($options['allowDiskUse'])) {
            throw InvalidArgumentException::invalidType('"allowDiskUse" option', $options['allowDiskUse'], 'boolean');
        }

        if (isset($options['allowPartialResults']) && ! is_bool($options['allowPartialResults'])) {
            throw InvalidArgumentException::invalidType('"allowPartialResults" option', $options['allowPartialResults'], 'boolean');
        }

        if (isset($options['batchSize']) && ! is_integer($options['batchSize'])) {
            throw InvalidArgumentException::invalidType('"batchSize" option', $options['batchSize'], 'integer');
        }

        if (isset($options['collation']) && ! is_array($options['collation']) && ! is_object($options['collation'])) {
            throw InvalidArgumentException::invalidType('"collation" option', $options['collation'], 'array or object');
        }

        if (isset($options['cursorType'])) {
            if (! is_integer($options['cursorType'])) {
                throw InvalidArgumentException::invalidType('"cursorType" option', $options['cursorType'], 'integer');
            }

            if (
                $options['cursorType'] !== self::NON_TAILABLE &&
                $options['cursorType'] !== self::TAILABLE &&
                $options['cursorType'] !== self::TAILABLE_AWAIT
            ) {
                throw new InvalidArgumentException('Invalid value for "cursorType" option: ' . $options['cursorType']);
            }
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_array($options['hint']) && ! is_object($options['hint'])) {
            throw InvalidArgumentException::invalidType('"hint" option', $options['hint'], 'string or array or object');
        }

        if (isset($options['limit']) && ! is_integer($options['limit'])) {
            throw InvalidArgumentException::invalidType('"limit" option', $options['limit'], 'integer');
        }

        if (isset($options['max']) && ! is_array($options['max']) && ! is_object($options['max'])) {
            throw InvalidArgumentException::invalidType('"max" option', $options['max'], 'array or object');
        }

        if (isset($options['maxAwaitTimeMS']) && ! is_integer($options['maxAwaitTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxAwaitTimeMS" option', $options['maxAwaitTimeMS'], 'integer');
        }

        if (isset($options['maxScan']) && ! is_integer($options['maxScan'])) {
            throw InvalidArgumentException::invalidType('"maxScan" option', $options['maxScan'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['min']) && ! is_array($options['min']) && ! is_object($options['min'])) {
            throw InvalidArgumentException::invalidType('"min" option', $options['min'], 'array or object');
        }

        if (isset($options['modifiers']) && ! is_array($options['modifiers']) && ! is_object($options['modifiers'])) {
            throw InvalidArgumentException::invalidType('"modifiers" option', $options['modifiers'], 'array or object');
        }

        if (isset($options['noCursorTimeout']) && ! is_bool($options['noCursorTimeout'])) {
            throw InvalidArgumentException::invalidType('"noCursorTimeout" option', $options['noCursorTimeout'], 'boolean');
        }

        if (isset($options['oplogReplay']) && ! is_bool($options['oplogReplay'])) {
            throw InvalidArgumentException::invalidType('"oplogReplay" option', $options['oplogReplay'], 'boolean');
        }

        if (isset($options['projection']) && ! is_array($options['projection']) && ! is_object($options['projection'])) {
            throw InvalidArgumentException::invalidType('"projection" option', $options['projection'], 'array or object');
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['returnKey']) && ! is_bool($options['returnKey'])) {
            throw InvalidArgumentException::invalidType('"returnKey" option', $options['returnKey'], 'boolean');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['showRecordId']) && ! is_bool($options['showRecordId'])) {
            throw InvalidArgumentException::invalidType('"showRecordId" option', $options['showRecordId'], 'boolean');
        }

        if (isset($options['skip']) && ! is_integer($options['skip'])) {
            throw InvalidArgumentException::invalidType('"skip" option', $options['skip'], 'integer');
        }

        if (isset($options['snapshot']) && ! is_bool($options['snapshot'])) {
            throw InvalidArgumentException::invalidType('"snapshot" option', $options['snapshot'], 'boolean');
        }

        if (isset($options['sort']) && ! is_array($options['sort']) && ! is_object($options['sort'])) {
            throw InvalidArgumentException::invalidType('"sort" option', $options['sort'], 'array or object');
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['let']) && ! is_array($options['let']) && ! is_object($options['let'])) {
            throw InvalidArgumentException::invalidType('"let" option', $options['let'], 'array or object');
        }

        if (isset($options['readConcern']) && $options['readConcern']->isDefault()) {
            unset($options['readConcern']);
        }

        if (isset($options['snapshot'])) {
            trigger_error('The "snapshot" option is deprecated and will be removed in a future release', E_USER_DEPRECATED);
        }

        if (isset($options['maxScan'])) {
            trigger_error('The "maxScan" option is deprecated and will be removed in a future release', E_USER_DEPRECATED);
        }

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->filter = $filter;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return Cursor
     * @throws UnsupportedException if read concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['readConcern'])) {
            throw UnsupportedException::readConcernNotSupportedInTransaction();
        }

        $cursor = $server->executeQuery($this->databaseName . '.' . $this->collectionName, new Query($this->filter, $this->createQueryOptions()), $this->createExecuteOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return $cursor;
    }

    /**
     * Returns the command document for this operation.
     *
     * @see Explainable::getCommandDocument()
     * @return array
     */
    public function getCommandDocument(Server $server)
    {
        return $this->createCommandDocument();
    }

    /**
     * Construct a command document for Find
     */
    private function createCommandDocument(): array
    {
        $cmd = ['find' => $this->collectionName, 'filter' => (object) $this->filter];

        $options = $this->createQueryOptions();

        if (empty($options)) {
            return $cmd;
        }

        // maxAwaitTimeMS is a Query level option so should not be considered here
        unset($options['maxAwaitTimeMS']);

        $modifierFallback = [
            ['allowPartialResults', 'partial'],
            ['comment', '$comment'],
            ['hint', '$hint'],
            ['maxScan', '$maxScan'],
            ['max', '$max'],
            ['maxTimeMS', '$maxTimeMS'],
            ['min', '$min'],
            ['returnKey', '$returnKey'],
            ['showRecordId', '$showDiskLoc'],
            ['sort', '$orderby'],
            ['snapshot', '$snapshot'],
        ];

        foreach ($modifierFallback as $modifier) {
            if (! isset($options[$modifier[0]]) && isset($options['modifiers'][$modifier[1]])) {
                $options[$modifier[0]] = $options['modifiers'][$modifier[1]];
            }
        }

        unset($options['modifiers']);

        return $cmd + $options;
    }

    /**
     * Create options for executing the command.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executequery.php
     */
    private function createExecuteOptions(): array
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

    /**
     * Create options for the find query.
     *
     * Note that these are separate from the options for executing the command,
     * which are created in createExecuteOptions().
     */
    private function createQueryOptions(): array
    {
        $options = [];

        if (isset($this->options['cursorType'])) {
            if ($this->options['cursorType'] === self::TAILABLE) {
                $options['tailable'] = true;
            }

            if ($this->options['cursorType'] === self::TAILABLE_AWAIT) {
                $options['tailable'] = true;
                $options['awaitData'] = true;
            }
        }

        foreach (['allowDiskUse', 'allowPartialResults', 'batchSize', 'comment', 'hint', 'limit', 'maxAwaitTimeMS', 'maxScan', 'maxTimeMS', 'noCursorTimeout', 'oplogReplay', 'projection', 'readConcern', 'returnKey', 'showRecordId', 'skip', 'snapshot', 'sort'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = $this->options[$option];
            }
        }

        foreach (['collation', 'let', 'max', 'min'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = (object) $this->options[$option];
            }
        }

        $modifiers = empty($this->options['modifiers']) ? [] : (array) $this->options['modifiers'];

        if (! empty($modifiers)) {
            $options['modifiers'] = $modifiers;
        }

        return $options;
    }
}
