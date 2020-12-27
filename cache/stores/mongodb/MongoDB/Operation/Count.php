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
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use function current;
use function is_array;
use function is_float;
use function is_integer;
use function is_object;
use function is_string;
use function MongoDB\server_supports_feature;

/**
 * Operation for the count command.
 *
 * @api
 * @see \MongoDB\Collection::count()
 * @see http://docs.mongodb.org/manual/reference/command/count/
 */
class Count implements Executable, Explainable
{
    /** @var integer */
    private static $wireVersionForCollation = 5;

    /** @var integer */
    private static $wireVersionForReadConcern = 4;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array|object */
    private $filter;

    /** @var array */
    private $options;

    /**
     * Constructs a count command.
     *
     * Supported options:
     *
     *  * collation (document): Collation specification.
     *
     *    This is not supported for server versions < 3.4 and will result in an
     *    exception at execution time if used.
     *
     *  * hint (string|document): The index to use. Specify either the index
     *    name as a string or the index key pattern as a document. If specified,
     *    then the query system will only consider plans using the hinted index.
     *
     *  * limit (integer): The maximum number of documents to count.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *    This is not supported for server versions < 3.2 and will result in an
     *    exception at execution time if used.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * skip (integer): The number of documents to skip before returning the
     *    documents.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $filter         Query by which to filter documents
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, $collectionName, $filter = [], array $options = [])
    {
        if (! is_array($filter) && ! is_object($filter)) {
            throw InvalidArgumentException::invalidType('$filter', $filter, 'array or object');
        }

        if (isset($options['collation']) && ! is_array($options['collation']) && ! is_object($options['collation'])) {
            throw InvalidArgumentException::invalidType('"collation" option', $options['collation'], 'array or object');
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_array($options['hint']) && ! is_object($options['hint'])) {
            throw InvalidArgumentException::invalidType('"hint" option', $options['hint'], 'string or array or object');
        }

        if (isset($options['limit']) && ! is_integer($options['limit'])) {
            throw InvalidArgumentException::invalidType('"limit" option', $options['limit'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw InvalidArgumentException::invalidType('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['skip']) && ! is_integer($options['skip'])) {
            throw InvalidArgumentException::invalidType('"skip" option', $options['skip'], 'integer');
        }

        if (isset($options['readConcern']) && $options['readConcern']->isDefault()) {
            unset($options['readConcern']);
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->filter = $filter;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return integer
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if collation or read concern is used and unsupported
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

        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['readConcern'])) {
            throw UnsupportedException::readConcernNotSupportedInTransaction();
        }

        $cursor = $server->executeReadCommand($this->databaseName, new Command($this->createCommandDocument()), $this->createOptions());
        $result = current($cursor->toArray());

        // Older server versions may return a float
        if (! isset($result->n) || ! (is_integer($result->n) || is_float($result->n))) {
            throw new UnexpectedValueException('count command did not return a numeric "n" value');
        }

        return (integer) $result->n;
    }

    public function getCommandDocument(Server $server)
    {
        return $this->createCommandDocument();
    }

    /**
     * Create the count command document.
     *
     * @return array
     */
    private function createCommandDocument()
    {
        $cmd = ['count' => $this->collectionName];

        if (! empty($this->filter)) {
            $cmd['query'] = (object) $this->filter;
        }

        if (isset($this->options['collation'])) {
            $cmd['collation'] = (object) $this->options['collation'];
        }

        if (isset($this->options['hint'])) {
            $cmd['hint'] = is_array($this->options['hint']) ? (object) $this->options['hint'] : $this->options['hint'];
        }

        foreach (['limit', 'maxTimeMS', 'skip'] as $option) {
            if (isset($this->options[$option])) {
                $cmd[$option] = $this->options[$option];
            }
        }

        return $cmd;
    }

    /**
     * Create options for executing the command.
     *
     * @see http://php.net/manual/en/mongodb-driver-server.executereadcommand.php
     * @return array
     */
    private function createOptions()
    {
        $options = [];

        if (isset($this->options['readConcern'])) {
            $options['readConcern'] = $this->options['readConcern'];
        }

        if (isset($this->options['readPreference'])) {
            $options['readPreference'] = $this->options['readPreference'];
        }

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        return $options;
    }
}
