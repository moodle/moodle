<?php
/*
 * Copyright 2017 MongoDB, Inc.
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

use MongoDB\ChangeStream;
use MongoDB\BSON\TimestampInterface;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;

/**
 * Operation for creating a change stream with the aggregate command.
 *
 * Note: the implementation of CommandSubscriber is an internal implementation
 * detail and should not be considered part of the public API.
 *
 * @api
 * @see \MongoDB\Collection::watch()
 * @see https://docs.mongodb.com/manual/changeStreams/
 */
class Watch implements Executable, /* @internal */ CommandSubscriber
{
    private static $wireVersionForOperationTime = 7;

    const FULL_DOCUMENT_DEFAULT = 'default';
    const FULL_DOCUMENT_UPDATE_LOOKUP = 'updateLookup';

    private $aggregate;
    private $aggregateOptions;
    private $changeStreamOptions;
    private $collectionName;
    private $databaseName;
    private $operationTime;
    private $pipeline;
    private $resumeCallable;

    /**
     * Constructs an aggregate command for creating a change stream.
     *
     * Supported options:
     *
     *  * batchSize (integer): The number of documents to return per batch.
     *
     *  * collation (document): Specifies a collation.
     *
     *  * fullDocument (string): Determines whether the "fullDocument" field
     *    will be populated for update operations. By default, change streams
     *    only return the delta of fields during the update operation (via the
     *    "updateDescription" field). To additionally return the most current
     *    majority-committed version of the updated document, specify
     *    "updateLookup" for this option. Defaults to "default".
     *
     *    Insert and replace operations always include the "fullDocument" field
     *    and delete operations omit the field as the document no longer exists.
     *
     *  * maxAwaitTimeMS (integer): The maximum amount of time for the server to
     *    wait on new documents to satisfy a change stream query.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference. This
     *    will be used to select a new server when resuming. Defaults to a
     *    "primary" read preference.
     *
     *  * resumeAfter (document): Specifies the logical starting point for the
     *    new change stream.
     *
     *    Using this option in conjunction with "startAtOperationTime" will
     *    result in a server error. The options are mutually exclusive.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *    Sessions are not supported for server versions < 3.6.
     *
     *  * startAtOperationTime (MongoDB\BSON\TimestampInterface): If specified,
     *    the change stream will only provide changes that occurred at or after
     *    the specified timestamp. Any command run against the server will
     *    return an operation time that can be used here. Alternatively, an
     *    operation time may be obtained from MongoDB\Driver\Server::getInfo().
     *
     *    Using this option in conjunction with "resumeAfter" will result in a
     *    server error. The options are mutually exclusive.
     *
     *    This option is not supported for server versions < 4.0.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     * Note: A database-level change stream may be created by specifying null
     * for the collection name. A cluster-level change stream may be created by
     * specifying null for both the database and collection name.
     *
     * @param Manager        $manager        Manager instance from the driver
     * @param string|null    $databaseName   Database name
     * @param string|null    $collectionName Collection name
     * @param array          $pipeline       List of pipeline operations
     * @param array          $options        Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(Manager $manager, $databaseName, $collectionName, array $pipeline, array $options = [])
    {
        if (isset($collectionName) && ! isset($databaseName)) {
            throw new InvalidArgumentException('$collectionName should also be null if $databaseName is null');
        }

        $options += [
            'fullDocument' => self::FULL_DOCUMENT_DEFAULT,
            'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
        ];

        if (isset($options['fullDocument']) && ! is_string($options['fullDocument'])) {
            throw InvalidArgumentException::invalidType('"fullDocument" option', $options['fullDocument'], 'string');
        }

        if (isset($options['resumeAfter']) && ! is_array($options['resumeAfter']) && ! is_object($options['resumeAfter'])) {
            throw InvalidArgumentException::invalidType('"resumeAfter" option', $options['resumeAfter'], 'array or object');
        }

        if (isset($options['startAtOperationTime']) && ! $options['startAtOperationTime'] instanceof TimestampInterface) {
            throw InvalidArgumentException::invalidType('"startAtOperationTime" option', $options['startAtOperationTime'], TimestampInterface::class);
        }

        /* In the absence of an explicit session, create one to ensure that the
         * initial aggregation and any resume attempts can use the same session
         * ("implicit from the user's perspective" per PHPLIB-342). Since this
         * is filling in for an implicit session, we default "causalConsistency"
         * to false. */
        if ( ! isset($options['session'])) {
            try {
                $options['session'] = $manager->startSession(['causalConsistency' => false]);
            } catch (RuntimeException $e) {
                /* We can ignore the exception, as libmongoc likely cannot
                 * create its own session and there is no risk of a mismatch. */
            }
        }

        $this->aggregateOptions = array_intersect_key($options, ['batchSize' => 1, 'collation' => 1, 'maxAwaitTimeMS' => 1, 'readConcern' => 1, 'readPreference' => 1, 'session' => 1, 'typeMap' => 1]);
        $this->changeStreamOptions = array_intersect_key($options, ['fullDocument' => 1, 'resumeAfter' => 1, 'startAtOperationTime' => 1]);

        // Null database name implies a cluster-wide change stream
        if ($databaseName === null) {
            $databaseName = 'admin';
            $this->changeStreamOptions['allChangesForCluster'] = true;
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = isset($collectionName) ? (string) $collectionName : null;
        $this->pipeline = $pipeline;

        $this->aggregate = $this->createAggregate();
        $this->resumeCallable = $this->createResumeCallable($manager);
    }

    /** @internal */
    final public function commandFailed(CommandFailedEvent $event)
    {
    }

    /** @internal */
    final public function commandStarted(CommandStartedEvent $event)
    {
    }

    /** @internal */
    final public function commandSucceeded(CommandSucceededEvent $event)
    {
        if ($event->getCommandName() !== 'aggregate') {
            return;
        }

        $reply = $event->getReply();

        if (isset($reply->operationTime) && $reply->operationTime instanceof TimestampInterface) {
            $this->operationTime = $reply->operationTime;
        }
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return ChangeStream
     * @throws UnsupportedException if collation or read concern is used and unsupported
     * @throws RuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server)
    {
        return new ChangeStream($this->executeAggregate($server), $this->resumeCallable);
    }

    /**
     * Create the aggregate command for creating a change stream.
     *
     * This method is also used to recreate the aggregate command when resuming.
     *
     * @return Aggregate
     */
    private function createAggregate()
    {
        $pipeline = $this->pipeline;
        array_unshift($pipeline, ['$changeStream' => (object) $this->changeStreamOptions]);

        return new Aggregate($this->databaseName, $this->collectionName, $pipeline, $this->aggregateOptions);
    }

    private function createResumeCallable(Manager $manager)
    {
        return function($resumeToken = null) use ($manager) {
            /* If a resume token was provided, update the "resumeAfter" option
             * and ensure that "startAtOperationTime" is no longer set. */
            if ($resumeToken !== null) {
                $this->changeStreamOptions['resumeAfter'] = $resumeToken;
                unset($this->changeStreamOptions['startAtOperationTime']);
            }

            /* If we captured an operation time from the first aggregate command
             * and there is no "resumeAfter" option, set "startAtOperationTime"
             * so that we can resume from the original aggregate's time. */
            if ($this->operationTime !== null && ! isset($this->changeStreamOptions['resumeAfter'])) {
                $this->changeStreamOptions['startAtOperationTime'] = $this->operationTime;
            }

            $this->aggregate = $this->createAggregate();

            /* Select a new server using the read preference, execute this
             * operation on it, and return the new ChangeStream. */
            $server = $manager->selectServer($this->aggregateOptions['readPreference']);

            return $this->execute($server);
        };
    }

    /**
     * Execute the aggregate command and optionally capture its operation time.
     *
     * @param Server $server
     * @return Cursor
     */
    private function executeAggregate(Server $server)
    {
        /* If we've already captured an operation time or the server does not
         * support returning an operation time (e.g. MongoDB 3.6), execute the
         * aggregation directly and return its cursor. */
        if ($this->operationTime !== null || ! \MongoDB\server_supports_feature($server, self::$wireVersionForOperationTime)) {
            return $this->aggregate->execute($server);
        }

        /* Otherwise, execute the aggregation using command monitoring so that
         * we can capture its operation time with commandSucceeded(). */
        \MongoDB\Driver\Monitoring\addSubscriber($this);

        try {
            return $this->aggregate->execute($server);
        } finally {
            \MongoDB\Driver\Monitoring\removeSubscriber($this);
        }
    }
}
