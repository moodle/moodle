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

use MongoDB\BSON\Serializable;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;
use ReflectionClass;

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
    if ( ! is_array($document) && ! is_object($document)) {
        throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
    }

    return \MongoDB\BSON\toPHP(\MongoDB\BSON\fromPHP($document), $typeMap);
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

    if ( ! is_array($document)) {
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

    if ( ! is_array($document)) {
        throw InvalidArgumentException::invalidType('$document', $document, 'array or object');
    }

    reset($document);
    $firstKey = (string) key($document);

    return (isset($firstKey[0]) && $firstKey[0] === '$');
}

/**
 * Return whether the aggregation pipeline ends with an $out operator.
 *
 * This is used for determining whether the aggregation pipeline must be
 * executed against a primary server.
 *
 * @internal
 * @param array $pipeline List of pipeline operations
 * @return boolean
 */
function is_last_pipeline_operator_out(array $pipeline)
{
    $lastOp = end($pipeline);

    if ($lastOp === false) {
        return false;
    }

    $lastOp = (array) $lastOp;

    return key($lastOp) === '$out';
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
    if ( ! is_array($out) && ! is_object($out)) {
        return false;
    }

    if ($out instanceof Serializable) {
        $out = $out->bsonSerialize();
    }

    if (is_object($out)) {
        $out = get_object_vars($out);
    }

    if ( ! is_array($out)) {
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

    return ($minWireVersion <= $feature && $maxWireVersion >= $feature);
}

function is_string_array($input) {
    if (!is_array($input)){
        return false;
    }
    foreach($input as $item) {
        if (!is_string($item)) {
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
 */
function recursive_copy($element) {
    if (is_array($element)) {
        foreach ($element as $key => $value) {
            $element[$key] = recursive_copy($value);
        }
        return $element;
    }

    if ( ! is_object($element)) {
        return $element;
    }

    if ( ! (new ReflectionClass($element))->isCloneable()) {
        return $element;
    }

    return clone $element;
}
