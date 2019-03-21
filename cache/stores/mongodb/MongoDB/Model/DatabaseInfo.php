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

namespace MongoDB\Model;

use MongoDB\Exception\BadMethodCallException;
use ArrayAccess;
/**
 * Database information model class.
 *
 * This class models the database information returned by the listDatabases
 * command. It provides methods to access common database properties.
 *
 * @api
 * @see \MongoDB\Client::listDatabases()
 * @see http://docs.mongodb.org/manual/reference/command/listDatabases/
 */
class DatabaseInfo implements ArrayAccess
{
    private $info;

    /**
     * Constructor.
     *
     * @param array $info Database info
     */
    public function __construct(array $info)
    {
        $this->info = $info;
    }

    /**
     * Return the collection info as an array.
     *
     * @see http://php.net/oop5.magic#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return $this->info;
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->info['name'];
    }

    /**
     * Return the databases size on disk (in bytes).
     *
     * @return integer
     */
    public function getSizeOnDisk()
    {
        return (integer) $this->info['sizeOnDisk'];
    }

    /**
     * Return whether the database is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (boolean) $this->info['empty'];
    }

    /**
     * Check whether a field exists in the database information.
     *
     * @see http://php.net/arrayaccess.offsetexists
     * @param mixed $key
     * @return boolean
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->info);
    }

    /**
     * Return the field's value from the database information.
     *
     * @see http://php.net/arrayaccess.offsetget
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->info[$key];
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayaccess.offsetset
     * @throws BadMethodCallException
     */
    public function offsetSet($key, $value)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayaccess.offsetunset
     * @throws BadMethodCallException
     */
    public function offsetUnset($key)
    {
        throw BadMethodCallException::classIsImmutable(__CLASS__);
    }
}
