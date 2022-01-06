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

namespace MongoDB;

use Exception;
use MongoDB\BSON\Serializable;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Operation\WithTransaction;
use ReflectionClass;
use ReflectionException;
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
 * @return string
 * @throws InvalidArgumentException
 */
function generate_index_name($document)
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
 * Return whether the first key in the document starts with a "$" character.
 *
 * This is used for differentiating update and replacement documents.
 *
 * @internal
 * @param array|object $document Update or replacement document
 * @return boolean
 * @throws InvalidArgumentException
 */
function is_first_key_operator($document)
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
 * @return boolean
 */
function is_pipeline($pipeline)
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

        if (! isset($key[0]) || $key[0] !== '$') {
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
 * @return boolean
 */
function is_in_transaction(array $options)
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
 * @return boolean
 */
function is_last_pipeline_operator_write(array $pipeline)
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
 * @see https://docs.mongodb.com/manual/reference/command/mapReduce/#output-inline
 * @param string|array|object $out Output specification
 * @return boolean
 * @throws InvalidArgumentException
 */
function is_mapreduce_output_inline($out)
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
 * Return whether the server supports a particular feature.
 *
 * @internal
 * @param Server  $server  Server to check
 * @param integer $feature Feature constant (i.e. wire protocol version)
 * @return boolean
 */
function server_supports_feature(Server $server, $feature)
{
    $info = $server->getInfo();
    $maxWireVersion = isset($info['maxWireVersion']) ? (integer) $info['maxWireVersion'] : 0;
    $minWireVersion = isset($info['minWireVersion']) ? (integer) $info['minWireVersion'] : 0;

    return $minWireVersion <= $feature && $maxWireVersion >= $feature;
}

function is_string_array($input)
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
 * @return array
 */
function create_field_path_type_map(array $typeMap, $fieldPath)
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
 * @return void
 * @throws RuntimeException for driver errors while committing the transaction
 * @throws Exception for any other errors, including those thrown in the callback
 */
function with_transaction(Session $session, callable $callback, array $transactionOptions = [])
{
    $operation = new WithTransaction($callback, $transactionOptions);
    $operation->execute($session);
}

/**
 * Returns the session option if it is set and valid.
 *
 * @internal
 * @param array $options
 * @return Session|null
 */
function extract_session_from_options(array $options)
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
 * @param array $options
 * @return ReadPreference|null
 */
function extract_read_preference_from_options(array $options)
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
 * @return Server
 */
function select_server(Manager $manager, array $options)
{
    $session = extract_session_from_options($options);
    if ($session instanceof Session && $session->getServer() !== null) {
        return $session->getServer();
    }

    $readPreference = extract_read_preference_from_options($options);
    if (! $readPreference instanceof ReadPreference) {
        // TODO: PHPLIB-476: Read transaction read preference once PHPC-1439 is implemented
        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
    }

    return $manager->selectServer($readPreference);
}
