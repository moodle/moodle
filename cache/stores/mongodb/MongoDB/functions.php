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

namespace MongoDB;

use Exception;
use MongoDB\BSON\Serializable;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Operation\ListCollections;
use MongoDB\Operation\WithTransaction;
use ReflectionClass;
use ReflectionException;

use function assert;
use function end;
use function get_object_vars;
use function in_array;
use function is_array;
use function is_object;
use function is_string;
use function key;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toPHP;
use function reset;
use function substr;

/**
 * Check whether all servers support executing a write stage on a secondary.
 *
 * @internal
 * @param Server[] $servers
 */
function all_servers_support_write_stage_on_secondary(array $servers): bool
{
    /* Write stages on secondaries are technically supported by FCV 4.4, but the
     * CRUD spec requires all 5.0+ servers since FCV is not tracked by SDAM. */
    static $wireVersionForWriteStageOnSecondary = 13;

    foreach ($servers as $server) {
        // We can assume that load balancers only front 5.0+ servers
        if ($server->getType() === Server::TYPE_LOAD_BALANCER) {
            continue;
        }

        if (! server_supports_feature($server, $wireVersionForWriteStageOnSecondary)) {
            return false;
        }
    }

    return true;
}

/**
 * Applies a type map to a document.
 *
 * This function is used by operations where it is not possible to apply a type
 * map to the cursor directly because the root document is a command response
 * (e.g. findAndModify).
 *
 * @internal
 * @param array|object $document Document to which the type map will be applied
 * @param array        $typeMap  Type map for BSON deserialization.
 * @return array|object
 * @throws InvalidArgumentException
 */
function apply_type_map_to_document($document, array $typeMap)
{
    if (! is_array($document) && ! is_object($document)) {
        throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
    }

    return toPHP(fromPHP($document), $typeMap);
}

/**
 * Generate an index name from a key specification.
 *
 * @internal
 * @param array|object $document Document containing fields mapped to values,
 *                               which denote order or an index type
 * @throws InvalidArgumentException
 */
function generate_index_name($document): string
{
    if ($document instanceof Serializable) {
        $document = $document->bsonSerialize();
    }

    if (is_object($document)) {
        $document = get_object_vars($document);
    }

    if (! is_array($document)) {
        throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
    }

    $name = '';

    foreach ($document as $field => $type) {
        $name .= ($name != '' ? '_' : '') . $field . '_' . $type;
    }

    return $name;
}

/**
 * Return a collection's encryptedFields from the encryptedFieldsMap
 * autoEncryption driver option (if available).
 *
 * @internal
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.rst#drop-collection-helper
 * @see Collection::drop
 * @see Database::createCollection
 * @see Database::dropCollection
 * @return array|object|null
 */
function get_encrypted_fields_from_driver(string $databaseName, string $collectionName, Manager $manager)
{
    $encryptedFieldsMap = (array) $manager->getEncryptedFieldsMap();

    return $encryptedFieldsMap[$databaseName . '.' . $collectionName] ?? null;
}

/**
 * Return a collection's encryptedFields option from the server (if any).
 *
 * @internal
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.rst#drop-collection-helper
 * @see Collection::drop
 * @see Database::dropCollection
 * @return array|object|null
 */
function get_encrypted_fields_from_server(string $databaseName, string $collectionName, Manager $manager, Server $server)
{
    // No-op if the encryptedFieldsMap autoEncryption driver option was omitted
    if ($manager->getEncryptedFieldsMap() === null) {
        return null;
    }

    $collectionInfoIterator = (new ListCollections($databaseName, ['filter' => ['name' => $collectionName]]))->execute($server);

    foreach ($collectionInfoIterator as $collectionInfo) {
        /* Note: ListCollections applies a typeMap that converts BSON documents
         * to PHP arrays. This should not be problematic as encryptedFields here
         * is only used by drop helpers to obtain names of supporting encryption
         * collections. */
        return $collectionInfo['options']['encryptedFields'] ?? null;
    }

    return null;
}

/**
 * Return whether the first key in the document starts with a "$" character.
 *
 * This is used for differentiating update and replacement documents.
 *
 * @internal
 * @param array|object $document Update or replacement document
 * @throws InvalidArgumentException
 */
function is_first_key_operator($document): bool
{
    if ($document instanceof Serializable) {
        $document = $document->bsonSerialize();
    }

    if (is_object($document)) {
        $document = get_object_vars($document);
    }

    if (! is_array($document)) {
        throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
    }

    reset($document);
    $firstKey = (string) key($document);

    return isset($firstKey[0]) && $firstKey[0] === '$';
}

/**
 * Returns whether an update specification is a valid aggregation pipeline.
 *
 * @internal
 * @param mixed $pipeline
 */
function is_pipeline($pipeline): bool
{
    if (! is_array($pipeline)) {
        return false;
    }

    if ($pipeline === []) {
        return false;
    }

    $expectedKey = 0;

    foreach ($pipeline as $key => $stage) {
        if (! is_array($stage) && ! is_object($stage)) {
            return false;
        }

        if ($expectedKey !== $key) {
            return false;
        }

        $expectedKey++;
        $stage = (array) $stage;
        reset($stage);
        $key = key($stage);

        if (! is_string($key) || substr($key, 0, 1) !== '$') {
            return false;
        }
    }

    return true;
}

/**
 * Returns whether we are currently in a transaction.
 *
 * @internal
 * @param array $options Command options
 */
function is_in_transaction(array $options): bool
{
    if (isset($options['session']) && $options['session'] instanceof Session && $options['session']->isInTransaction()) {
        return true;
    }

    return false;
}

/**
 * Return whether the aggregation pipeline ends with an $out or $merge operator.
 *
 * This is used for determining whether the aggregation pipeline must be
 * executed against a primary server.
 *
 * @internal
 * @param array $pipeline List of pipeline operations
 */
function is_last_pipeline_operator_write(array $pipeline): bool
{
    $lastOp = end($pipeline);

    if ($lastOp === false) {
        return false;
    }

    $lastOp = (array) $lastOp;

    return in_array(key($lastOp), ['$out', '$merge'], true);
}

/**
 * Return whether the "out" option for a mapReduce operation is "inline".
 *
 * This is used to determine if a mapReduce command requires a primary.
 *
 * @internal
 * @see https://mongodb.com/docs/manual/reference/command/mapReduce/#output-inline
 * @param string|array|object $out Output specification
 * @throws InvalidArgumentException
 */
function is_mapreduce_output_inline($out): bool
{
    if (! is_array($out) && ! is_object($out)) {
        return false;
    }

    if ($out instanceof Serializable) {
        $out = $out->bsonSerialize();
    }

    if (is_object($out)) {
        $out = get_object_vars($out);
    }

    if (! is_array($out)) {
        throw InvalidArgumentException::invalidType('$out', $out, 'array or object');
    }

    reset($out);

    return key($out) === 'inline';
}

/**
 * Return whether the write concern is acknowledged.
 *
 * This function is similar to mongoc_write_concern_is_acknowledged but does not
 * check the fsync option since that was never supported in the PHP driver.
 *
 * @internal
 * @see https://mongodb.com/docs/manual/reference/write-concern/
 */
function is_write_concern_acknowledged(WriteConcern $writeConcern): bool
{
    /* Note: -1 corresponds to MONGOC_WRITE_CONCERN_W_ERRORS_IGNORED, which is
     * deprecated synonym of MONGOC_WRITE_CONCERN_W_UNACKNOWLEDGED and slated
     * for removal in libmongoc 2.0. */
    return ($writeConcern->getW() !== 0 && $writeConcern->getW() !== -1) || $writeConcern->getJournal() === true;
}

/**
 * Return whether the server supports a particular feature.
 *
 * @internal
 * @param Server  $server  Server to check
 * @param integer $feature Feature constant (i.e. wire protocol version)
 */
function server_supports_feature(Server $server, int $feature): bool
{
    $info = $server->getInfo();
    $maxWireVersion = isset($info['maxWireVersion']) ? (integer) $info['maxWireVersion'] : 0;
    $minWireVersion = isset($info['minWireVersion']) ? (integer) $info['minWireVersion'] : 0;

    return $minWireVersion <= $feature && $maxWireVersion >= $feature;
}

/**
 * Return whether the input is an array of strings.
 *
 * @internal
 * @param mixed $input
 */
function is_string_array($input): bool
{
    if (! is_array($input)) {
        return false;
    }

    foreach ($input as $item) {
        if (! is_string($item)) {
            return false;
        }
    }

    return true;
}

/**
 * Performs a deep copy of a value.
 *
 * This function will clone objects and recursively copy values within arrays.
 *
 * @internal
 * @see https://bugs.php.net/bug.php?id=49664
 * @param mixed $element Value to be copied
 * @return mixed
 * @throws ReflectionException
 */
function recursive_copy($element)
{
    if (is_array($element)) {
        foreach ($element as $key => $value) {
            $element[$key] = recursive_copy($value);
        }

        return $element;
    }

    if (! is_object($element)) {
        return $element;
    }

    if (! (new ReflectionClass($element))->isCloneable()) {
        return $element;
    }

    return clone $element;
}

/**
 * Creates a type map to apply to a field type
 *
 * This is used in the Aggregate, Distinct, and FindAndModify operations to
 * apply the root-level type map to the document that will be returned. It also
 * replaces the root type with object for consistency within these operations
 *
 * An existing type map for the given field path will not be overwritten
 *
 * @internal
 * @param array  $typeMap   The existing typeMap
 * @param string $fieldPath The field path to apply the root type to
 */
function create_field_path_type_map(array $typeMap, string $fieldPath): array
{
    // If some field paths already exist, we prefix them with the field path we are assuming as the new root
    if (isset($typeMap['fieldPaths']) && is_array($typeMap['fieldPaths'])) {
        $fieldPaths = $typeMap['fieldPaths'];

        $typeMap['fieldPaths'] = [];
        foreach ($fieldPaths as $existingFieldPath => $type) {
            $typeMap['fieldPaths'][$fieldPath . '.' . $existingFieldPath] = $type;
        }
    }

    // If a root typemap was set, apply this to the field object
    if (isset($typeMap['root'])) {
        $typeMap['fieldPaths'][$fieldPath] = $typeMap['root'];
    }

    /* Special case if we want to convert an array, in which case we need to
     * ensure that the field containing the array is exposed as an array,
     * instead of the type given in the type map's array key. */
    if (substr($fieldPath, -2, 2) === '.$') {
        $typeMap['fieldPaths'][substr($fieldPath, 0, -2)] = 'array';
    }

    $typeMap['root'] = 'object';

    return $typeMap;
}

/**
 * Execute a callback within a transaction in the given session
 *
 * This helper takes care of retrying the commit operation or the entire
 * transaction if an error occurs.
 *
 * If the commit fails because of an UnknownTransactionCommitResult error, the
 * commit is retried without re-invoking the callback.
 * If the commit fails because of a TransientTransactionError, the entire
 * transaction will be retried. In this case, the callback will be invoked
 * again. It is important that the logic inside the callback is idempotent.
 *
 * In case of failures, the commit or transaction are retried until 120 seconds
 * from the initial call have elapsed. After that, no retries will happen and
 * the helper will throw the last exception received from the driver.
 *
 * @see Client::startSession
 * @see Session::startTransaction for supported transaction options
 *
 * @param Session  $session            A session object as retrieved by Client::startSession
 * @param callable $callback           A callback that will be invoked within the transaction
 * @param array    $transactionOptions Additional options that are passed to Session::startTransaction
 * @throws RuntimeException for driver errors while committing the transaction
 * @throws Exception for any other errors, including those thrown in the callback
 */
function with_transaction(Session $session, callable $callback, array $transactionOptions = []): void
{
    $operation = new WithTransaction($callback, $transactionOptions);
    $operation->execute($session);
}

/**
 * Returns the session option if it is set and valid.
 *
 * @internal
 */
function extract_session_from_options(array $options): ?Session
{
    if (! isset($options['session']) || ! $options['session'] instanceof Session) {
        return null;
    }

    return $options['session'];
}

/**
 * Returns the readPreference option if it is set and valid.
 *
 * @internal
 */
function extract_read_preference_from_options(array $options): ?ReadPreference
{
    if (! isset($options['readPreference']) || ! $options['readPreference'] instanceof ReadPreference) {
        return null;
    }

    return $options['readPreference'];
}

/**
 * Performs server selection, respecting the readPreference and session options
 * (if given)
 *
 * @internal
 */
function select_server(Manager $manager, array $options): Server
{
    $session = extract_session_from_options($options);
    $server = $session instanceof Session ? $session->getServer() : null;
    if ($server !== null) {
        return $server;
    }

    $readPreference = extract_read_preference_from_options($options);
    if (! $readPreference instanceof ReadPreference) {
        // TODO: PHPLIB-476: Read transaction read preference once PHPC-1439 is implemented
        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
    }

    return $manager->selectServer($readPreference);
}

/**
 * Performs server selection for an aggregate operation with a write stage. The
 * $options parameter may be modified by reference if a primary read preference
 * must be forced due to the existence of pre-5.0 servers in the topology.
 *
 * @internal
 * @see https://github.com/mongodb/specifications/blob/master/source/crud/crud.rst#aggregation-pipelines-with-write-stages
 */
function select_server_for_aggregate_write_stage(Manager $manager, array &$options): Server
{
    $readPreference = extract_read_preference_from_options($options);

    /* If there is either no read preference or a primary read preference, there
     * is no special server selection logic to apply. */
    if ($readPreference === null || $readPreference->getMode() === ReadPreference::RP_PRIMARY) {
        return select_server($manager, $options);
    }

    $server = null;
    $serverSelectionError = null;

    try {
        $server = select_server($manager, $options);
    } catch (DriverRuntimeException $serverSelectionError) {
    }

    /* If any pre-5.0 servers exist in the topology, force a primary read
     * preference and repeat server selection if it previously failed or
     * selected a secondary. */
    if (! all_servers_support_write_stage_on_secondary($manager->getServers())) {
        $options['readPreference'] = new ReadPreference(ReadPreference::RP_PRIMARY);

        if ($server === null || $server->isSecondary()) {
            return select_server($manager, $options);
        }
    }

    /* If the topology only contains 5.0+ servers, we should either return the
     * previously selected server or propagate the server selection error. */
    if ($serverSelectionError !== null) {
        throw $serverSelectionError;
    }

    assert($server instanceof Server);

    return $server;
}
