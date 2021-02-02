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

use Iterator;
use Jean85\PrettyVersions;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\InvalidArgumentException as DriverInvalidArgumentException;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\DatabaseInfoIterator;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\ListDatabaseNames;
use MongoDB\Operation\ListDatabases;
use MongoDB\Operation\Watch;
use Throwable;
use function is_array;
use function is_string;

class Client
{
    /** @var array */
    private static $defaultTypeMap = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    /** @var integer */
    private static $wireVersionForReadConcern = 4;

    /** @var integer */
    private static $wireVersionForWritableCommandWriteConcern = 5;

    /** @var string */
    private static $handshakeSeparator = ' / ';

    /** @var string|null */
    private static $version;

    /** @var Manager */
    private $manager;

    /** @var ReadConcern */
    private $readConcern;

    /** @var ReadPreference */
    private $readPreference;

    /** @var string */
    private $uri;

    /** @var array */
    private $typeMap;

    /** @var WriteConcern */
    private $writeConcern;

    /**
     * Constructs a new Client instance.
     *
     * This is the preferred class for connecting to a MongoDB server or
     * cluster of servers. It serves as a gateway for accessing individual
     * databases and collections.
     *
     * Supported driver-specific options:
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     * Other options are documented in MongoDB\Driver\Manager::__construct().
     *
     * @see http://docs.mongodb.org/manual/reference/connection-string/
     * @see http://php.net/manual/en/mongodb-driver-manager.construct.php
     * @see http://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps
     * @param string $uri           MongoDB connection string
     * @param array  $uriOptions    Additional connection string options
     * @param array  $driverOptions Driver-specific options
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverInvalidArgumentException for parameter/option parsing errors in the driver
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function __construct($uri = 'mongodb://127.0.0.1/', array $uriOptions = [], array $driverOptions = [])
    {
        $driverOptions += ['typeMap' => self::$defaultTypeMap];

        if (! is_array($driverOptions['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" driver option', $driverOptions['typeMap'], 'array');
        }

        if (isset($driverOptions['autoEncryption']['keyVaultClient'])) {
            if ($driverOptions['autoEncryption']['keyVaultClient'] instanceof self) {
                $driverOptions['autoEncryption']['keyVaultClient'] = $driverOptions['autoEncryption']['keyVaultClient']->manager;
            } elseif (! $driverOptions['autoEncryption']['keyVaultClient'] instanceof Manager) {
                throw InvalidArgumentException::invalidType('"keyVaultClient" autoEncryption option', $driverOptions['autoEncryption']['keyVaultClient'], [self::class, Manager::class]);
            }
        }

        $driverOptions['driver'] = $this->mergeDriverInfo($driverOptions['driver'] ?? []);

        $this->uri = (string) $uri;
        $this->typeMap = $driverOptions['typeMap'] ?? null;

        unset($driverOptions['typeMap']);

        $this->manager = new Manager($uri, $uriOptions, $driverOptions);
        $this->readConcern = $this->manager->getReadConcern();
        $this->readPreference = $this->manager->getReadPreference();
        $this->writeConcern = $this->manager->getWriteConcern();
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
            'manager' => $this->manager,
            'uri' => $this->uri,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Select a database.
     *
     * Note: databases whose names contain special characters (e.g. "-") may
     * be selected with complex syntax (e.g. $client->{"that-database"}) or
     * {@link selectDatabase()}.
     *
     * @see http://php.net/oop5.overloading#object.get
     * @see http://php.net/types.string#language.types.string.parsing.complex
     * @param string $databaseName Name of the database to select
     * @return Database
     */
    public function __get($databaseName)
    {
        return $this->selectDatabase($databaseName);
    }

    /**
     * Return the connection string (i.e. URI).
     *
     * @return string
     */
    public function __toString()
    {
        return $this->uri;
    }

    /**
     * Returns a ClientEncryption instance for explicit encryption and decryption
     *
     * @param array $options Encryption options
     *
     * @return ClientEncryption
     */
    public function createClientEncryption(array $options)
    {
        if (isset($options['keyVaultClient'])) {
            if ($options['keyVaultClient'] instanceof self) {
                $options['keyVaultClient'] = $options['keyVaultClient']->manager;
            } elseif (! $options['keyVaultClient'] instanceof Manager) {
                throw InvalidArgumentException::invalidType('"keyVaultClient" option', $options['keyVaultClient'], [self::class, Manager::class]);
            }
        }

        return $this->manager->createClientEncryption($options);
    }

    /**
     * Drop a database.
     *
     * @see DropDatabase::__construct() for supported options
     * @param string $databaseName Database name
     * @param array  $options      Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropDatabase($databaseName, array $options = [])
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['writeConcern']) && server_supports_feature($server, self::$wireVersionForWritableCommandWriteConcern) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new DropDatabase($databaseName, $options);

        return $operation->execute($server);
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
     * Return the read concern for this client.
     *
     * @see http://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     * @return ReadConcern
     */
    public function getReadConcern()
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this client.
     *
     * @return ReadPreference
     */
    public function getReadPreference()
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this client.
     *
     * @return array
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this client.
     *
     * @see http://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     * @return WriteConcern
     */
    public function getWriteConcern()
    {
        return $this->writeConcern;
    }

    /**
     * List database names.
     *
     * @see ListDatabaseNames::__construct() for supported options
     * @throws UnexpectedValueException if the command response was malformed
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listDatabaseNames(array $options = []) : Iterator
    {
        $operation = new ListDatabaseNames($options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * List databases.
     *
     * @see ListDatabases::__construct() for supported options
     * @param array $options
     * @return DatabaseInfoIterator
     * @throws UnexpectedValueException if the command response was malformed
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listDatabases(array $options = [])
    {
        $operation = new ListDatabases($options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Select a collection.
     *
     * @see Collection::__construct() for supported options
     * @param string $databaseName   Name of the database containing the collection
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @return Collection
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectCollection($databaseName, $collectionName, array $options = [])
    {
        $options += ['typeMap' => $this->typeMap];

        return new Collection($this->manager, $databaseName, $collectionName, $options);
    }

    /**
     * Select a database.
     *
     * @see Database::__construct() for supported options
     * @param string $databaseName Name of the database to select
     * @param array  $options      Database constructor options
     * @return Database
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectDatabase($databaseName, array $options = [])
    {
        $options += ['typeMap' => $this->typeMap];

        return new Database($this->manager, $databaseName, $options);
    }

    /**
     * Start a new client session.
     *
     * @see http://php.net/manual/en/mongodb-driver-manager.startsession.php
     * @param array $options Session options
     * @return Session
     */
    public function startSession(array $options = [])
    {
        return $this->manager->startSession($options);
    }

    /**
     * Create a change stream for watching changes to the cluster.
     *
     * @see Watch::__construct() for supported options
     * @param array $pipeline List of pipeline operations
     * @param array $options  Command options
     * @return ChangeStream
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function watch(array $pipeline = [], array $options = [])
    {
        if (! isset($options['readPreference']) && ! is_in_transaction($options)) {
            $options['readPreference'] = $this->readPreference;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['readConcern']) && server_supports_feature($server, self::$wireVersionForReadConcern) && ! is_in_transaction($options)) {
            $options['readConcern'] = $this->readConcern;
        }

        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $operation = new Watch($this->manager, null, null, $pipeline, $options);

        return $operation->execute($server);
    }

    private static function getVersion() : string
    {
        if (self::$version === null) {
            try {
                self::$version = PrettyVersions::getVersion('mongodb/mongodb')->getPrettyVersion();
            } catch (Throwable $t) {
                return 'unknown';
            }
        }

        return self::$version;
    }

    private function mergeDriverInfo(array $driver) : array
    {
        $mergedDriver = [
            'name' => 'PHPLIB',
            'version' => self::getVersion(),
        ];

        if (isset($driver['name'])) {
            if (! is_string($driver['name'])) {
                throw InvalidArgumentException::invalidType('"name" handshake option', $driver['name'], 'string');
            }

            $mergedDriver['name'] .= self::$handshakeSeparator . $driver['name'];
        }

        if (isset($driver['version'])) {
            if (! is_string($driver['version'])) {
                throw InvalidArgumentException::invalidType('"version" handshake option', $driver['version'], 'string');
            }

            $mergedDriver['version'] .= self::$handshakeSeparator . $driver['version'];
        }

        if (isset($driver['platform'])) {
            $mergedDriver['platform'] = $driver['platform'];
        }

        return $mergedDriver;
    }
}
