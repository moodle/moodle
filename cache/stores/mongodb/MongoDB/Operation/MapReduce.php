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

use ArrayIterator;
use MongoDB\BSON\JavascriptInterface;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\MapReduceResult;
use stdClass;
use function current;
use function is_array;
use function is_bool;
use function is_integer;
use function is_object;
use function is_string;
use function MongoDB\create_field_path_type_map;
use function MongoDB\is_mapreduce_output_inline;
use function MongoDB\server_supports_feature;
use function trigger_error;
use const E_USER_DEPRECATED;

/**
 * Operation for the mapReduce command.
 *
 * @api
 * @see \MongoDB\Collection::mapReduce()
 * @see https://docs.mongodb.com/manual/reference/command/mapReduce/
 */
class MapReduce implements Executable
{
    /** @var integer */
    private static $wireVersionForCollation = 5;

    /** @var integer */
    private static $wireVersionForDocumentLevelValidation = 4;

    /** @var integer */
    private static $wireVersionForReadConcern = 4;

    /** @var integer */
    private static $wireVersionForWriteConcern = 4;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var JavascriptInterface */
    private $map;

    /** @var JavascriptInterface */
    private $reduce;

    /** @var array|object|string */
    private $out;

    /** @var array */
    private $options;

    /**
     * Constructs a mapReduce command.
     *
     * Required arguments:
     *
     *  * map (MongoDB\BSON\Javascript): A JavaScript function that associates
     *    or "maps" a value with a key and emits the key and value pair.
     *
     *    Passing a Javascript instance with a scope is deprecated. Put all
     *    scope variables in the "scope" option of the MapReduce operation.
     *
     *  * reduce (MongoDB\BSON\Javascript): A JavaScript function that "reduces"
     *    to a single object all the values associated with a particular key.
     *
     *    Passing a Javascript instance with a scope is deprecated. Put all
     *    scope variables in the "scope" option of the MapReduce operation.
     *
     *  * out (string|document): Specifies where to output the result of the
     *    map-reduce operation. You can either output to a collection or return
     *    the result inline. On a primary member of a replica set you can output
     *    either to a collection or inline, but on a secondary, only inline
     *    output is possible.
     *
     * Supported options:
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation. This only applies when results
     *    are output to a collection.
     *
     *    For servers < 3.2, this option is ignored as document level validation
     *    is not available.
     *
     *  * collation (document): Collation specification.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     *  * finalize (MongoDB\BSON\JavascriptInterface): Follows the reduce method
     *    and modifies the output.
     *
     *    Passing a Javascript instance with a scope is deprecated. Put all
     *    scope variables in the "scope" option of the MapReduce operation.
     *
     *  * jsMode (boolean): Specifies whether to convert intermediate data into
     *    BSON format between the execution of the map and reduce functions.
     *
     *  * limit (integer): Specifies a maximum number of documents for the input
     *    into the map function.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * query (document): Specifies the selection criteria using query
     *    operators for determining the documents input to the map function.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern. This is not
     *    supported when results are returned inline.
     *
     *    This is not supported for server versions < 3.2 and will result in an
     *    exception at execution time if used.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *    This option is ignored if results are output to a collection.
     *
     *  * scope (document): Specifies global variables that are accessible in
     *    the map, reduce and finalize functions.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * sort (document): Sorts the input documents. This option is useful for
     *    optimization. For example, specify the sort key to be the same as the
     *    emit key so that there are fewer reduce operations. The sort key must
     *    be in an existing index for this collection.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     *  * verbose (boolean): Specifies whether to include the timing information
     *    in the result information.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern. This only
     *    applies when results are output to a collection.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     * @param string              $databaseName   Database name
     * @param string              $collectionName Collection name
     * @param JavascriptInterface $map            Map function
     * @param JavascriptInterface $reduce         Reduce function
     * @param string|array|object $out            Output specification
     * @param array               $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, JavascriptInterface $map, JavascriptInterface $reduce, $out, array $options = [])
    {
        if (! is_string($out) && ! is_array($out) && ! is_object($out)) {
            throw InvalidArgumentException::invalidType('$out', $out, 'string or array or object');
        }

        if (isset($options['bypassDocumentValidation']) && ! is_bool($options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $options['bypassDocumentValidation'], 'boolean');
        }

        if (isset($options['collation']) && ! is_array($options['collation']) && ! is_object($options['collation'])) {
            throw InvalidArgumentException::invalidType('"collation" option', $options['collation'], 'array or object');
        }

        if (isset($options['finalize']) && ! $options['finalize'] instanceof JavascriptInterface) {
            throw InvalidArgumentException::invalidType('"finalize" option', $options['finalize'], JavascriptInterface::class);
        }

        if (isset($options['jsMode']) && ! is_bool($options['jsMode'])) {
            throw InvalidArgumentException::invalidType('"jsMode" option', $options['jsMode'], 'boolean');
        }

        if (isset($options['limit']) && ! is_integer($options['limit'])) {
            throw InvalidArgumentException::invalidType('"limit" option', $options['limit'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['query']) && ! is_array($options['query']) && ! is_object($options['query'])) {
            throw InvalidArgumentException::invalidType('"query" option', $options['query'], 'array or object');
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['scope']) && ! is_array($options['scope']) && ! is_object($options['scope'])) {
            throw InvalidArgumentException::invalidType('"scope" option', $options['scope'], 'array or object');
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

        if (isset($options['verbose']) && ! is_bool($options['verbose'])) {
            throw InvalidArgumentException::invalidType('"verbose" option', $options['verbose'], 'boolean');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['readConcern']) && $options['readConcern']->isDefault()) {
            unset($options['readConcern']);
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        // Handle deprecation of CodeWScope
        if ($map->getScope() !== null) {
            @trigger_error('Use of Javascript with scope in "$map" argument for MapReduce is deprecated. Put all scope variables in the "scope" option of the MapReduce operation.', E_USER_DEPRECATED);
        }

        if ($reduce->getScope() !== null) {
            @trigger_error('Use of Javascript with scope in "$reduce" argument for MapReduce is deprecated. Put all scope variables in the "scope" option of the MapReduce operation.', E_USER_DEPRECATED);
        }

        if (isset($options['finalize']) && $options['finalize']->getScope() !== null) {
            @trigger_error('Use of Javascript with scope in "finalize" option for MapReduce is deprecated. Put all scope variables in the "scope" option of the MapReduce operation.', E_USER_DEPRECATED);
        }

        $this->checkOutDeprecations($out);

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->map = $map;
        $this->reduce = $reduce;
        $this->out = $out;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return MapReduceResult
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if collation, read concern, or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        if (isset($this->options['collation']) && ! server_supports_feature($server, self::$wireVersionForCollation)) {
            throw UnsupportedException::collationNotSupported();
        }

        if (isset($this->options['readConcern']) && ! server_supports_feature($server, self::$wireVersionForReadConcern)) {
            throw UnsupportedException::readConcernNotSupported();
        }

        if (isset($this->options['writeConcern']) && ! server_supports_feature($server, self::$wireVersionForWriteConcern)) {
            throw UnsupportedException::writeConcernNotSupported();
        }

        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction) {
            if (isset($this->options['readConcern'])) {
                throw UnsupportedException::readConcernNotSupportedInTransaction();
            }
            if (isset($this->options['writeConcern'])) {
                throw UnsupportedException::writeConcernNotSupportedInTransaction();
            }
        }

        $hasOutputCollection = ! is_mapreduce_output_inline($this->out);

        $command = $this->createCommand($server);
        $options = $this->createOptions($hasOutputCollection);

        /* If the mapReduce operation results in a write, use
         * executeReadWriteCommand to ensure we're handling the writeConcern
         * option.
         * In other cases, we use executeCommand as this will prevent the
         * mapReduce operation from being retried when retryReads is enabled.
         * See https://github.com/mongodb/specifications/blob/master/source/retryable-reads/retryable-reads.rst#unsupported-read-operations. */
        $cursor = $hasOutputCollection
            ? $server->executeReadWriteCommand($this->databaseName, $command, $options)
            : $server->executeCommand($this->databaseName, $command, $options);

        if (isset($this->options['typeMap']) && ! $hasOutputCollection) {
            $cursor->setTypeMap(create_field_path_type_map($this->options['typeMap'], 'results.$'));
        }

        $result = current($cursor->toArray());

        $getIterator = $this->createGetIteratorCallable($result, $server);

        return new MapReduceResult($getIterator, $result);
    }

    /**
     * @param string|array|object $out
     * @return void
     */
    private function checkOutDeprecations($out)
    {
        if (is_string($out)) {
            return;
        }

        $out = (array) $out;

        if (isset($out['nonAtomic']) && ! $out['nonAtomic']) {
            @trigger_error('Specifying false for "out.nonAtomic" is deprecated.', E_USER_DEPRECATED);
        }

        if (isset($out['sharded']) && ! $out['sharded']) {
            @trigger_error('Specifying false for "out.sharded" is deprecated.', E_USER_DEPRECATED);
        }
    }

    /**
     * Create the mapReduce command.
     *
     * @param Server $server
     * @return Command
     */
    private function createCommand(Server $server)
    {
        $cmd = [
            'mapReduce' => $this->collectionName,
            'map' => $this->map,
            'reduce' => $this->reduce,
            'out' => $this->out,
        ];

        foreach (['finalize', 'jsMode', 'limit', 'maxTimeMS', 'verbose'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        foreach (['collation', 'query', 'scope', 'sort'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = (object) $this->options[$option];
            }
        }

        if (! empty($this->options['bypassDocumentValidation']) &&
            server_supports_feature($server, self::$wireVersionForDocumentLevelValidation)
        ) {
            $cmd['bypassDocumentValidation'] = $this->options['bypassDocumentValidation'];
        }

        return new Command($cmd);
    }

    /**
     * Creates a callable for MapReduceResult::getIterator().
     *
     * @param stdClass $result
     * @param Server   $server
     * @return callable
     * @throws UnexpectedValueException if the command response was malformed
     */
    private function createGetIteratorCallable(stdClass $result, Server $server)
    {
        // Inline results can be wrapped with an ArrayIterator
        if (isset($result->results) && is_array($result->results)) {
            $results = $result->results;

            return function () use ($results) {
                return new ArrayIterator($results);
            };
        }

        if (isset($result->result) && (is_string($result->result) || is_object($result->result))) {
            $options = isset($this->options['typeMap']) ? ['typeMap' => $this->options['typeMap']] : [];

            $find = is_string($result->result)
                ? new Find($this->databaseName, $result->result, [], $options)
                : new Find($result->result->db, $result->result->collection, [], $options);

            return function () use ($find, $server) {
                return $find->execute($server);
            };
        }

        throw new UnexpectedValueException('mapReduce command did not return inline results or an output collection');
    }

    /**
     * Create options for executing the command.
     *
     * @see http://php.net/manual/en/mongodb-driver-server.executereadcommand.php
     * @see http://php.net/manual/en/mongodb-driver-server.executereadwritecommand.php
     * @param boolean $hasOutputCollection
     * @return array
     */
    private function createOptions($hasOutputCollection)
    {
        $options = [];

        if (isset($this->options['readConcern'])) {
            $options['readConcern'] = $this->options['readConcern'];
        }

        if (! $hasOutputCollection && isset($this->options['readPreference'])) {
            $options['readPreference'] = $this->options['readPreference'];
        }

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        if ($hasOutputCollection && isset($this->options['writeConcern'])) {
            $options['writeConcern'] = $this->options['writeConcern'];
        }

        return $options;
    }
}
