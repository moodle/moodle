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

use MongoDB\Collection;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\GridFS\Bucket;
use MongoDB\Model\CollectionInfoIterator;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\ListCollections;
use MongoDB\Operation\ModifyCollection;
use MongoDB\Operation\Watch;

class Database
{
    private static $defaultTypeMap = [
        'array' => 'MongoDB\Model\BSONArray',
        'document' => 'MongoDB\Model\BSONDocument',
        'root' => 'MongoDB\Model\BSONDocument',
    ];
    private static $wireVersionForReadConcern = 4;
    private static $wireVersionForWritableCommandWriteConcern = 5;

    private $databaseName;
    private $manager;
    private $readConcern;
    private $readPreference;
    private $typeMap;
    private $writeConcern;

    /**
     * Constructs new Database instance.
     *
     * This class provides methods for database-specific operations and serves
     * as a gateway for accessing collections.
     *
     * Supported options:
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): The default read concern to
     *    use for database operations and selected collections. Defaults to the
     *    Manager's read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for database operations and selected collections.
     *    Defaults to the Manager's read preference.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for database operations and selected collections. Defaults to
     *    the Manager's write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Database options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(Manager $manager, $databaseName, array $options = [])
    {
        if (strlen($databaseName) < 1) {
            throw new InvalidArgumentException('$databaseName is invalid: ' . $databaseName);
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], 'MongoDB\Driver\ReadConcern');
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }

        $this->manager = $manager;
        $this->databaseName = (string) $databaseName;
        $this->readConcern = isset($options['readConcern']) ? $options['readConcern'] : $this->manager->getReadConcern();
        $this->readPreference = isset($options['readPreference']) ? $options['readPreference'] : $this->manager->getReadPreference();
        $this->typeMap = isset($options['typeMap']) ? $options['typeMap'] : self::$defaultTypeMap;
        $this->writeConcern = isset($options['writeConcern']) ? $options['writeConcern'] : $this->manager->getWriteConcern();
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'databaseName' => $this->databaseName,
            'manager' => $this->manager,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Select a collection within this database.
     *
     * Note: collections whose names contain special characters (e.g. ".") may
     * be selected with complex syntax (e.g. $database->{"system.profile"}) or
     * {@link selectCollection()}.
     *
     * @see http://php.net/oop5.overloading#object.get
     * @see http://php.net/types.string#language.types.string.parsing.complex
     * @param string $collectionName Name of the collection to select
     * @return Collection
     */
    public function __get($collectionName)
    {
        return $this->selectCollection($collectionName);
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->databaseName;
    }

    /**
     * Execute a command on this database.
     *
     * @see DatabaseCommand::__construct() for supported options
     * @param array|object $command Command document
     * @param array        $options Options for command execution
     * @return Cursor
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function command($command, array $options = [])
    {
        if ( ! isset($options['readPreference'])) {
            $options['readPreference'] = $this->readPreference;
        }

        if ( ! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $operation = new DatabaseCommand($this->databaseName, $command, $options);
        $server = $this->manager->selectServer($options['readPreference']);

        return $operation->execute($server);
    }

    /**
     * Create a new collection explicitly.
     *
     * @see CreateCollection::__construct() for supported options
     * @param string $collectionName
     * @param array  $options
     * @return array|object Command result document
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function createCollection($collectionName, array $options = [])
    {
        if ( ! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        if ( ! isset($options['writeConcern']) && \MongoDB\server_supports_feature($server, self::$wireVersionForWritableCommandWriteConcern)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new CreateCollection($this->databaseName, $collectionName, $options);

        return $operation->execute($server);
    }

    /**
     * Drop this database.
     *
     * @see DropDatabase::__construct() for supported options
     * @param array $options Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function drop(array $options = [])
    {
        if ( ! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        if ( ! isset($options['writeConcern']) && \MongoDB\server_supports_feature($server, self::$wireVersionForWritableCommandWriteConcern)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new DropDatabase($this->databaseName, $options);

        return $operation->execute($server);
    }

    /**
     * Drop a collection within this database.
     *
     * @see DropCollection::__construct() for supported options
     * @param string $collectionName Collection name
     * @param array  $options        Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropCollection($collectionName, array $options = [])
    {
        if ( ! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        if ( ! isset($options['writeConcern']) && \MongoDB\server_supports_feature($server, self::$wireVersionForWritableCommandWriteConcern)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new DropCollection($this->databaseName, $collectionName, $options);

        return $operation->execute($server);
    }

    /**
     * Returns the database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Return the Manager.
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Return the read concern for this database.
     *
     * @see http://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     * @return ReadConcern
     */
    public function getReadConcern()
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this database.
     *
     * @return ReadPreference
     */
    public function getReadPreference()
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this database.
     *
     * @return array
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this database.
     *
     * @see http://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     * @return WriteConcern
     */
    public function getWriteConcern()
    {
        return $this->writeConcern;
    }

    /**
     * Returns information for all collections in this database.
     *
     * @see ListCollections::__construct() for supported options
     * @param array $options
     * @return CollectionInfoIterator
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listCollections(array $options = [])
    {
        $operation = new ListCollections($this->databaseName, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Modifies a collection or view.
     *
     * @see ModifyCollection::__construct() for supported options
     * @param string $collectionName    Collection or view to modify
     * @param array  $collectionOptions Collection or view options to assign
     * @param array  $options           Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function modifyCollection($collectionName, array $collectionOptions, array $options = [])
    {
        if ( ! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        if ( ! isset($options['writeConcern']) && \MongoDB\server_supports_feature($server, self::$wireVersionForWritableCommandWriteConcern)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new ModifyCollection($this->databaseName, $collectionName, $collectionOptions, $options);

        return $operation->execute($server);
    }

    /**
     * Select a collection within this database.
     *
     * @see Collection::__construct() for supported options
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @return Collection
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectCollection($collectionName, array $options = [])
    {
        $options += [
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Collection($this->manager, $this->databaseName, $collectionName, $options);
    }

    /**
     * Select a GridFS bucket within this database.
     *
     * @see Bucket::__construct() for supported options
     * @param array $options Bucket constructor options
     * @return Bucket
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectGridFSBucket(array $options = [])
    {
        $options += [
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Bucket($this->manager, $this->databaseName, $options);
    }

    /**
     * Create a change stream for watching changes to the database.
     *
     * @see Watch::__construct() for supported options
     * @param array $pipeline List of pipeline operations
     * @param array $options  Command options
     * @return ChangeStream
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function watch(array $pipeline = [], array $options = [])
    {
        if ( ! isset($options['readPreference'])) {
            $options['readPreference'] = $this->readPreference;
        }

        $server = $this->manager->selectServer($options['readPreference']);

        if ( ! isset($options['readConcern']) && \MongoDB\server_supports_feature($server, self::$wireVersionForReadConcern)) {
            $options['readConcern'] = $this->readConcern;
        }

        if ( ! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $operation = new Watch($this->manager, $this->databaseName, null, $pipeline, $options);

        return $operation->execute($server);
    }

    /**
     * Get a clone of this database with different options.
     *
     * @see Database::__construct() for supported options
     * @param array $options Database constructor options
     * @return Database
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function withOptions(array $options = [])
    {
        $options += [
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Database($this->manager, $this->databaseName, $options);
    }
}
