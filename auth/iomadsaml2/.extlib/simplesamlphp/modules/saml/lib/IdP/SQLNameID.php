<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\IdP;

use PDO;
use PDOStatement;
use SimpleSAML\Error;
use SimpleSAML\Store;
use SimpleSAML\Database;
use SimpleSAML\Configuration;

/**
 * Helper class for working with persistent NameIDs stored in SQL datastore.
 *
 * @package SimpleSAMLphp
 */
class SQLNameID
{
    public const TABLE_VERSION = 1;
    public const DEFAULT_TABLE_PREFIX = '';
    public const TABLE_SUFFIX = '_saml_PersistentNameID';


    /**
     * @param string $query
     * @param array $params Parameters
     * @param array $config
     * @return \PDOStatement object
     */
    private static function read(string $query, array $params = [], array $config = []): PDOStatement
    {
        if (!empty($config)) {
            $database = Database::getInstance(Configuration::loadFromArray($config));
            $stmt = $database->read($query, $params);
        } else {
            $store = self::getStore();
            $stmt = $store->pdo->prepare($query);
            $stmt->execute($params);
        }
        return $stmt;
    }


    /**
     * @param string $query
     * @param array $params Parameters
     * @param array $config
     * @return int|false The number of rows affected by the query or false on error.
     */
    private static function write(string $query, array $params = [], array $config = [])
    {
        if (!empty($config)) {
            $database = Database::getInstance(Configuration::loadFromArray($config));
            $res = $database->write($query, $params);
        } else {
            $store = self::getStore();
            $query = $store->pdo->prepare($query);
            $res = $query->execute($params);
            if ($res) {
                $res = $query->rowCount();
            }
        }
        return $res;
    }


    /**
     * @param array $config
     * @return string
     */
    private static function tableName(array $config = []): string
    {
        $store = empty($config) ? self::getStore() : null;
        $prefix = $store === null ? self::DEFAULT_TABLE_PREFIX : $store->prefix;
        $table = $prefix . self::TABLE_SUFFIX;
        return $table;
    }

    /**
     * @param array $config
     * @return void
     */
    private static function create(array $config = []): void
    {
        $store = empty($config) ? self::getStore() : null;
        $table = self::tableName($config);
        if ($store === null) {
            try {
                self::createTable($table, $config);
            } catch (\Exception $e) {
                \SimpleSAML\Logger::debug('SQL persistent NameID table already exists.');
            }
        } elseif ($store->getTableVersion('saml_PersistentNameID') !== self::TABLE_VERSION) {
            self::createTable($table);
            $store->setTableVersion('saml_PersistentNameID', self::TABLE_VERSION);
        }
    }


    /**
     * @param string $query
     * @param array $params
     * @param array $config
     * @return \PDOStatement
     */
    private static function createAndRead(string $query, array $params = [], array $config = []): PDOStatement
    {
        self::create($config);
        return self::read($query, $params, $config);
    }


    /**
     * @param string $query
     * @param array $params
     * @param array $config
     * @return int|false The number of rows affected by the query or false on error.
     */
    private static function createAndWrite(string $query, array $params = [], array $config = [])
    {
        self::create($config);
        return self::write($query, $params, $config);
    }


    /**
     * Create NameID table in SQL.
     *
     * @param string $table  The table name.
     * @param array $config
     * @return void
     */
    private static function createTable(string $table, array $config = []): void
    {
        $query = 'CREATE TABLE ' . $table . ' (
            _idp VARCHAR(256) NOT NULL,
            _sp VARCHAR(256) NOT NULL,
            _user VARCHAR(256) NOT NULL,
            _value VARCHAR(40) NOT NULL,
            UNIQUE (_idp, _sp, _user)
        )';
        self::write($query, [], $config);

        $query = 'CREATE INDEX ' . $table . '_idp_sp ON ';
        $query .= $table . ' (_idp, _sp)';
        self::write($query, [], $config);
    }


    /**
     * Retrieve the SQL datastore.
     *
     * @return \SimpleSAML\Store\SQL  SQL datastore.
     */
    private static function getStore(): Store\SQL
    {
        $store = Store::getInstance();
        if (!($store instanceof Store\SQL)) {
            throw new Error\Exception(
                'SQL NameID store requires SimpleSAMLphp to be configured with a SQL datastore.'
            );
        }

        return $store;
    }


    /**
     * Add a NameID into the database.
     *
     * @param string $idpEntityId  The IdP entityID.
     * @param string $spEntityId  The SP entityID.
     * @param string $user  The user's unique identificator (e.g. username).
     * @param string $value  The NameID value.
     * @param array $config
     * @return void
     */
    public static function add(
        string $idpEntityId,
        string $spEntityId,
        string $user,
        string $value,
        array $config = []
    ): void {
        $params = [
            '_idp' => $idpEntityId,
            '_sp' => $spEntityId,
            '_user' => $user,
            '_value' => $value,
        ];

        $query = 'INSERT INTO ' . self::tableName($config);
        $query .= ' (_idp, _sp, _user, _value) VALUES(:_idp, :_sp, :_user, :_value)';
        self::createAndWrite($query, $params, $config);
    }


    /**
     * Retrieve a NameID into from database.
     *
     * @param string $idpEntityId  The IdP entityID.
     * @param string $spEntityId  The SP entityID.
     * @param string $user  The user's unique identificator (e.g. username).
     * @param array $config
     * @return string|null $value  The NameID value, or NULL of no NameID value was found.
     */
    public static function get($idpEntityId, $spEntityId, $user, array $config = [])
    {
        assert(is_string($idpEntityId));
        assert(is_string($spEntityId));
        assert(is_string($user));

        $params = [
            '_idp' => $idpEntityId,
            '_sp' => $spEntityId,
            '_user' => $user,
        ];

        $query = 'SELECT _value FROM ' . self::tableName($config);
        $query .= ' WHERE _idp = :_idp AND _sp = :_sp AND _user = :_user';
        $query = self::createAndRead($query, $params, $config);

        $row = $query->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            // No NameID found
            return null;
        }

        return $row['_value'];
    }


    /**
     * Delete a NameID from the database.
     *
     * @param string $idpEntityId  The IdP entityID.
     * @param string $spEntityId  The SP entityID.
     * @param string $user  The user's unique identificator (e.g. username).
     * @param array $config
     * @return void
     */
    public static function delete($idpEntityId, $spEntityId, $user, array $config = [])
    {
        assert(is_string($idpEntityId));
        assert(is_string($spEntityId));
        assert(is_string($user));

        $params = [
            '_idp' => $idpEntityId,
            '_sp' => $spEntityId,
            '_user' => $user,
        ];

        $query = 'DELETE FROM ' . self::tableName($config);
        $query .= ' WHERE _idp = :_idp AND _sp = :_sp AND _user = :_user';
        self::createAndWrite($query, $params, $config);
    }


    /**
     * Retrieve all federated identities for an IdP-SP pair.
     *
     * @param string $idpEntityId  The IdP entityID.
     * @param string $spEntityId  The SP entityID.
     * @param array $config
     * @return array  Array of userid => NameID.
     */
    public static function getIdentities($idpEntityId, $spEntityId, array $config = [])
    {
        assert(is_string($idpEntityId));
        assert(is_string($spEntityId));

        $params = [
            '_idp' => $idpEntityId,
            '_sp' => $spEntityId,
        ];

        $query = 'SELECT _user, _value FROM ' . self::tableName($config);
        $query .= ' WHERE _idp = :_idp AND _sp = :_sp';
        $query = self::createAndRead($query, $params, $config);

        $res = [];
        while (($row = $query->fetch(PDO::FETCH_ASSOC)) !== false) {
            $user = strval($row['_user']);
            $value = strval($row['_value']);
            $res[$user] = $value;
        }

        return $res;
    }
}
