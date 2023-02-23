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

use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\UpdateResult;

use function is_array;
use function is_bool;
use function is_object;
use function is_string;
use function MongoDB\is_first_key_operator;
use function MongoDB\is_pipeline;
use function MongoDB\is_write_concern_acknowledged;
use function MongoDB\server_supports_feature;

/**
 * Operation for the update command.
 *
 * This class is used internally by the ReplaceOne, UpdateMany, and UpdateOne
 * operation classes.
 *
 * @internal
 * @see https://mongodb.com/docs/manual/reference/command/update/
 */
class Update implements Executable, Explainable
{
    /** @var integer */
    private static $wireVersionForHint = 8;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $collectionName;

    /** @var array|object */
    private $filter;

    /** @var array|object */
    private $update;

    /** @var array */
    private $options;

    /**
     * Constructs a update command.
     *
     * Supported options:
     *
     *  * arrayFilters (document array): A set of filters specifying to which
     *    array elements an update should apply.
     *
     *  * bypassDocumentValidation (boolean): If true, allows the write to
     *    circumvent document level validation.
     *
     *  * collation (document): Collation specification.
     *
     *  * comment (mixed): BSON value to attach as a comment to this command.
     *
     *    This is not supported for servers versions < 4.4.
     *
     *  * hint (string|document): The index to use. Specify either the index
     *    name as a string or the index key pattern as a document. If specified,
     *    then the query system will only consider plans using the hinted index.
     *
     *    This is not supported for server versions < 4.2 and will result in an
     *    exception at execution time if used.
     *
     *  * multi (boolean): When true, updates all documents matching the query.
     *    This option cannot be true if the $update argument is a replacement
     *    document (i.e. contains no update operators). The default is false.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * upsert (boolean): When true, a new document is created if no document
     *    matches the query. The default is false.
     *
     *  * let (document): Map of parameter names and values. Values must be
     *    constant or closed expressions that do not reference document fields.
     *    Parameters can then be accessed as variables in an aggregate
     *    expression context (e.g. "$$var").
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $filter         Query by which to delete documents
     * @param array|object $update         Update to apply to the matched
     *                                     document(s) or a replacement document
     * @param array        $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(string $databaseName, string $collectionName, $filter, $update, array $options = [])
    {
        if (! is_array($filter) && ! is_object($filter)) {
            throw InvalidArgumentException::invalidType('$filter', $filter, 'array or object');
        }

        if (! is_array($update) && ! is_object($update)) {
            throw InvalidArgumentException::invalidType('$update', $filter, 'array or object');
        }

        $options += [
            'multi' => false,
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

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_array($options['hint']) && ! is_object($options['hint'])) {
            throw InvalidArgumentException::invalidType('"hint" option', $options['hint'], ['string', 'array', 'object']);
        }

        if (! is_bool($options['multi'])) {
            throw InvalidArgumentException::invalidType('"multi" option', $options['multi'], 'boolean');
        }

        if ($options['multi'] && ! is_first_key_operator($update) && ! is_pipeline($update)) {
            throw new InvalidArgumentException('"multi" option cannot be true if $update is a replacement document');
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (! is_bool($options['upsert'])) {
            throw InvalidArgumentException::invalidType('"upsert" option', $options['upsert'], 'boolean');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['let']) && ! is_array($options['let']) && ! is_object($options['let'])) {
            throw InvalidArgumentException::invalidType('"let" option', $options['let'], 'array or object');
        }

        if (isset($options['bypassDocumentValidation']) && ! $options['bypassDocumentValidation']) {
            unset($options['bypassDocumentValidation']);
        }

        if (isset($options['writeConcern']) && $options['writeConcern']->isDefault()) {
            unset($options['writeConcern']);
        }

        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->filter = $filter;
        $this->update = $update;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @return UpdateResult
     * @throws UnsupportedException if hint or write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        /* CRUD spec requires a client-side error when using "hint" with an
         * unacknowledged write concern on an unsupported server. */
        if (
            isset($this->options['writeConcern']) && ! is_write_concern_acknowledged($this->options['writeConcern']) &&
            isset($this->options['hint']) && ! server_supports_feature($server, self::$wireVersionForHint)
        ) {
            throw UnsupportedException::hintNotSupported();
        }

        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['writeConcern'])) {
            throw UnsupportedException::writeConcernNotSupportedInTransaction();
        }

        $bulk = new Bulk($this->createBulkWriteOptions());
        $bulk->update($this->filter, $this->update, $this->createUpdateOptions());

        $writeResult = $server->executeBulkWrite($this->databaseName . '.' . $this->collectionName, $bulk, $this->createExecuteOptions());

        return new UpdateResult($writeResult);
    }

    /**
     * Returns the command document for this operation.
     *
     * @see Explainable::getCommandDocument()
     * @return array
     */
    public function getCommandDocument(Server $server)
    {
        $cmd = ['update' => $this->collectionName, 'updates' => [['q' => $this->filter, 'u' => $this->update] + $this->createUpdateOptions()]];

        if (isset($this->options['bypassDocumentValidation'])) {
            $cmd['bypassDocumentValidation'] = $this->options['bypassDocumentValidation'];
        }

        if (isset($this->options['writeConcern'])) {
            $cmd['writeConcern'] = $this->options['writeConcern'];
        }

        return $cmd;
    }

    /**
     * Create options for constructing the bulk write.
     *
     * @see https://php.net/manual/en/mongodb-driver-bulkwrite.construct.php
     */
    private function createBulkWriteOptions(): array
    {
        $options = [];

        foreach (['bypassDocumentValidation', 'comment'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = $this->options[$option];
            }
        }

        if (isset($this->options['let'])) {
            $options['let'] = (object) $this->options['let'];
        }

        return $options;
    }

    /**
     * Create options for executing the bulk write.
     *
     * @see https://php.net/manual/en/mongodb-driver-server.executebulkwrite.php
     */
    private function createExecuteOptions(): array
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

    /**
     * Create options for the update command.
     *
     * Note that these options are different from the bulk write options, which
     * are created in createExecuteOptions().
     */
    private function createUpdateOptions(): array
    {
        $updateOptions = [
            'multi' => $this->options['multi'],
            'upsert' => $this->options['upsert'],
        ];

        foreach (['arrayFilters', 'hint'] as $option) {
            if (isset($this->options[$option])) {
                $updateOptions[$option] = $this->options[$option];
            }
        }

        if (isset($this->options['collation'])) {
            $updateOptions['collation'] = (object) $this->options['collation'];
        }

        return $updateOptions;
    }
}
