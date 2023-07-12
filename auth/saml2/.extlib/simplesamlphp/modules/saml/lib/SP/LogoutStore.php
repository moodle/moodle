<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\SP;

use PDO;
use SAML2\XML\saml\NameID;
use SimpleSAML\Logger;
use SimpleSAML\Session;
use SimpleSAML\Store;
use SimpleSAML\Utils;

/**
 * A directory over logout information.
 *
 * @package SimpleSAMLphp
 */

class LogoutStore
{
    /**
     * Create logout table in SQL, if it is missing.
     *
     * @param \SimpleSAML\Store\SQL $store  The datastore.
     * @return void
     */
    private static function createLogoutTable(Store\SQL $store): void
    {
        $tableVer = $store->getTableVersion('saml_LogoutStore');
        if ($tableVer === 4) {
            return;
        } elseif ($tableVer === 3) {
            /**
             * Table version 4 fixes the column type for the _expire column.
             * We now use DATETIME instead of TIMESTAMP to support MSSQL.
             */
            switch ($store->driver) {
                case 'pgsql':
                    // This does not affect the NOT NULL constraint
                    $update = [
                        'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore ALTER COLUMN _expire TYPE TIMESTAMP'
                    ];
                    break;
                case 'sqlsrv':
                    $update = [
                        'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore ALTER COLUMN _expire DATETIME NOT NULL'
                    ];
                    break;
                case 'sqlite':
                    /**
                     * Because SQLite does not support field alterations, the approach is to:
                     *     Create a new table without the proper column size
                     *     Copy the current data to the new table
                     *     Drop the old table
                     *     Rename the new table correctly
                     *     Read the index
                     */
                    $update = [
                        'CREATE TABLE ' . $store->prefix . '_saml_LogoutStore_new (' .
                        '_authSource VARCHAR(255) NOT NULL, _nameId VARCHAR(40) NOT NULL' .
                        ', _sessionIndex VARCHAR(50) NOT NULL, _expire DATETIME NOT NULL,' .
                        '_sessionId VARCHAR(50) NOT NULL, UNIQUE (_authSource, _nameID, _sessionIndex))',
                        'INSERT INTO ' . $store->prefix . '_saml_LogoutStore_new SELECT * FROM ' .
                        $store->prefix . '_saml_LogoutStore',
                        'DROP TABLE ' . $store->prefix . '_saml_LogoutStore',
                        'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore_new RENAME TO ' .
                        $store->prefix . '_saml_LogoutStore',
                        'CREATE INDEX ' . $store->prefix . '_saml_LogoutStore_expire ON ' .
                        $store->prefix . '_saml_LogoutStore (_expire)',
                        'CREATE INDEX ' . $store->prefix . '_saml_LogoutStore_nameId ON ' .
                        $store->prefix . '_saml_LogoutStore (_authSource, _nameId)'
                    ];
                    break;
                default:
                    $update = [
                        'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore MODIFY _expire DATETIME NOT NULL'
                    ];
                    break;
            }

            try {
                foreach ($update as $query) {
                    $store->pdo->exec($query);
                }
            } catch (\Exception $e) {
                Logger::warning('Database error: ' . var_export($store->pdo->errorInfo(), true));
                return;
            }
            $store->setTableVersion('saml_LogoutStore', 4);
            return;
        } elseif ($tableVer === 2) {
            /**
             * TableVersion 3 fixes the indexes that were set to 255 in version 2;
             *   they cannot be larger than 191 on MySQL
             */

            if ($store->driver === 'mysql') {
                // Drop old indexes
                $query = 'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore DROP INDEX ' .
                $store->prefix . '_saml_LogoutStore_nameId';
                $store->pdo->exec($query);
                $query = 'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore DROP INDEX _authSource';
                $store->pdo->exec($query);

                // Create new indexes
                $query = 'CREATE INDEX ' . $store->prefix . '_saml_LogoutStore_nameId ON ';
                $query .= $store->prefix . '_saml_LogoutStore (_authSource(191), _nameId)';
                $store->pdo->exec($query);

                $query = 'ALTER TABLE ' . $store->prefix .
                '_saml_LogoutStore ADD UNIQUE KEY (_authSource(191), _nameID, _sessionIndex)';
                $store->pdo->exec($query);
            }

            $store->setTableVersion('saml_LogoutStore', 3);
            return;
        } elseif ($tableVer === 1) {
            // TableVersion 2 increased the column size to 255 (191 for mysql) which is the maximum length of a FQDN
            switch ($store->driver) {
                case 'pgsql':
                    // This does not affect the NOT NULL constraint
                    $update = [
                        'ALTER TABLE ' . $store->prefix .
                        '_saml_LogoutStore ALTER COLUMN _authSource TYPE VARCHAR(255)'];
                    break;
               case 'sqlsrv':
                    $update = [
                        'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore ALTER COLUMN _authSource VARCHAR(255) NOT NULL'
                    ];
                    break;
                case 'sqlite':
                    /**
                     * Because SQLite does not support field alterations, the approach is to:
                     *     Create a new table without the proper column size
                     *     Copy the current data to the new table
                     *     Drop the old table
                     *     Rename the new table correctly
                     *     Read the index
                     */
                    $update = [
                        'CREATE TABLE ' . $store->prefix .
                        '_saml_LogoutStore_new (_authSource VARCHAR(255) NOT NULL,' .
                        '_nameId VARCHAR(40) NOT NULL, _sessionIndex VARCHAR(50) NOT NULL, ' .
                        '_expire TIMESTAMP NOT NULL, _sessionId VARCHAR(50) NOT NULL, UNIQUE ' .
                        '(_authSource, _nameID, _sessionIndex))',
                        'INSERT INTO ' . $store->prefix . '_saml_LogoutStore_new SELECT * FROM ' .
                        $store->prefix . '_saml_LogoutStore',
                        'DROP TABLE ' . $store->prefix . '_saml_LogoutStore',
                        'ALTER TABLE ' . $store->prefix . '_saml_LogoutStore_new RENAME TO ' .
                        $store->prefix . '_saml_LogoutStore',
                        'CREATE INDEX ' . $store->prefix . '_saml_LogoutStore_expire ON ' .
                        $store->prefix . '_saml_LogoutStore (_expire)',
                        'CREATE INDEX ' . $store->prefix . '_saml_LogoutStore_nameId ON ' .
                        $store->prefix . '_saml_LogoutStore (_authSource, _nameId)'
                    ];
                    break;
                case 'mysql':
                    $update = [
                        'ALTER TABLE ' . $store->prefix .
                        '_saml_LogoutStore MODIFY _authSource VARCHAR(191) NOT NULL'
                    ];
                    break;
                default:
                    $update = [
                        'ALTER TABLE ' . $store->prefix .
                        '_saml_LogoutStore MODIFY _authSource VARCHAR(255) NOT NULL'
                    ];
                    break;
            }

            try {
                foreach ($update as $query) {
                    $store->pdo->exec($query);
                }
            } catch (\Exception $e) {
                Logger::warning('Database error: ' . var_export($store->pdo->errorInfo(), true));
                return;
            }
            $store->setTableVersion('saml_LogoutStore', 2);
            return;
        }

        $query = 'CREATE TABLE ' . $store->prefix . '_saml_LogoutStore (
            _authSource VARCHAR(' . ($store->driver === 'mysql' ? '191' : '255') . ') NOT NULL,
            _nameId VARCHAR(40) NOT NULL,
            _sessionIndex VARCHAR(50) NOT NULL,
            _expire ' . ($store->driver === 'pgsql' ? 'TIMESTAMP' : 'DATETIME') . ' NOT NULL,
            _sessionId VARCHAR(50) NOT NULL,
            UNIQUE (_authSource' . ($store->driver === 'mysql' ? '(191)' : '') . ', _nameID, _sessionIndex)
        )';
        $store->pdo->exec($query);

        $query = 'CREATE INDEX ' . $store->prefix . '_saml_LogoutStore_expire ON ';
        $query .= $store->prefix . '_saml_LogoutStore (_expire)';
        $store->pdo->exec($query);

        $query = 'CREATE INDEX ' . $store->prefix . '_saml_LogoutStore_nameId ON ';
        $query .= $store->prefix . '_saml_LogoutStore (_authSource' . ($store->driver === 'mysql' ? '(191)' : '') .
        ', _nameId)';
        $store->pdo->exec($query);

        $store->setTableVersion('saml_LogoutStore', 4);
    }


    /**
     * Clean the logout table of expired entries.
     *
     * @param \SimpleSAML\Store\SQL $store  The datastore.
     * @return void
     */
    private static function cleanLogoutStore(Store\SQL $store): void
    {
        Logger::debug('saml.LogoutStore: Cleaning logout store.');

        $query = 'DELETE FROM ' . $store->prefix . '_saml_LogoutStore WHERE _expire < :now';
        $params = ['now' => gmdate('Y-m-d H:i:s')];

        $query = $store->pdo->prepare($query);
        $query->execute($params);
    }


    /**
     * Register a session in the SQL datastore.
     *
     * @param \SimpleSAML\Store\SQL $store  The datastore.
     * @param string $authId  The authsource ID.
     * @param string $nameId  The hash of the users NameID.
     * @param string $sessionIndex  The SessionIndex of the user.
     * @param int $expire
     * @param string $sessionId
     * @return void
     */
    private static function addSessionSQL(
        Store\SQL $store,
        string $authId,
        string $nameId,
        string $sessionIndex,
        int $expire,
        string $sessionId
    ): void {
        self::createLogoutTable($store);

        if (rand(0, 1000) < 10) {
            self::cleanLogoutStore($store);
        }

        $data = [
            '_authSource' => $authId,
            '_nameId' => $nameId,
            '_sessionIndex' => $sessionIndex,
            '_expire' => gmdate('Y-m-d H:i:s', $expire),
            '_sessionId' => $sessionId,
        ];
        $store->insertOrUpdate(
            $store->prefix . '_saml_LogoutStore',
            ['_authSource', '_nameId', '_sessionIndex'],
            $data
        );
    }


    /**
     * Retrieve sessions from the SQL datastore.
     *
     * @param \SimpleSAML\Store\SQL $store  The datastore.
     * @param string $authId  The authsource ID.
     * @param string $nameId  The hash of the users NameID.
     * @return array  Associative array of SessionIndex =>  SessionId.
     */
    private static function getSessionsSQL(Store\SQL $store, string $authId, string $nameId): array
    {
        self::createLogoutTable($store);

        $params = [
            '_authSource' => $authId,
            '_nameId' => $nameId,
            'now' => gmdate('Y-m-d H:i:s'),
        ];

        // We request the columns in lowercase in order to be compatible with PostgreSQL
        $query = 'SELECT _sessionIndex AS _sessionindex, _sessionId AS _sessionid FROM ' . $store->prefix;
        $query .= '_saml_LogoutStore WHERE _authSource = :_authSource AND _nameId = :_nameId AND _expire >= :now';
        $query = $store->pdo->prepare($query);
        $query->execute($params);

        $res = [];
        while (($row = $query->fetch(PDO::FETCH_ASSOC)) !== false) {
            $res[$row['_sessionindex']] = $row['_sessionid'];
        }

        return $res;
    }


    /**
     * Retrieve all session IDs from a key-value store.
     *
     * @param \SimpleSAML\Store $store  The datastore.
     * @param string $authId  The authsource ID.
     * @param string $nameId  The hash of the users NameID.
     * @param array $sessionIndexes  The session indexes.
     * @return array  Associative array of SessionIndex =>  SessionId.
     */
    private static function getSessionsStore(
        Store $store,
        string $authId,
        string $nameId,
        array $sessionIndexes
    ): array {
        $res = [];
        foreach ($sessionIndexes as $sessionIndex) {
            $sessionId = $store->get('saml.LogoutStore', $nameId . ':' . $sessionIndex);
            if ($sessionId === null) {
                continue;
            }
            assert(is_string($sessionId));
            $res[$sessionIndex] = $sessionId;
        }

        return $res;
    }


    /**
     * Register a new session in the datastore.
     *
     * Please observe the change of the signature in this method. Previously, the second parameter ($nameId) was forced
     * to be an array. However, it has no type restriction now, and the documentation states it must be a
     * \SAML2\XML\saml\NameID object. Currently, this function still accepts an array passed as $nameId, and will
     * silently convert it to a \SAML2\XML\saml\NameID object. This is done to keep backwards-compatibility, though will
     * no longer be possible in the future as the $nameId parameter will be required to be an object.
     *
     * @param string $authId  The authsource ID.
     * @param \SAML2\XML\saml\NameID $nameId The NameID of the user.
     * @param string|null $sessionIndex  The SessionIndex of the user.
     * @param int $expire
     * @return void
     */
    public static function addSession($authId, $nameId, $sessionIndex, $expire)
    {
        assert(is_string($authId));
        assert(is_string($sessionIndex) || $sessionIndex === null);
        assert(is_int($expire));

        $session = Session::getSessionFromRequest();
        if ($session->isTransient()) {
            // transient sessions are useless for this purpose, nothing to do
            return;
        }

        if ($sessionIndex === null) {
            /* This IdP apparently did not include a SessionIndex, and thus probably does not
             * support SLO. We still want to add the session to the data store just in case
             * it supports SLO, but we don't want an LogoutRequest with a specific
             * SessionIndex to match this session. We therefore generate our own session index.
             */
            $sessionIndex = Utils\Random::generateID();
        }

        $store = Store::getInstance();
        if ($store === false) {
            // We don't have a datastore.
            return;
        }

        // serialize and anonymize the NameID
        // TODO: remove this conditional statement
        if (is_array($nameId)) {
            /** @psalm-suppress UndefinedMethod */
            $nameId = NameID::fromArray($nameId);
        }
        $strNameId = serialize($nameId);
        $strNameId = sha1($strNameId);

        // Normalize SessionIndex
        if (strlen($sessionIndex) > 50) {
            $sessionIndex = sha1($sessionIndex);
        }

        /** @var string $sessionId */
        $sessionId = $session->getSessionId();

        if ($store instanceof Store\SQL) {
            self::addSessionSQL($store, $authId, $strNameId, $sessionIndex, $expire, $sessionId);
        } else {
            $store->set('saml.LogoutStore', $strNameId . ':' . $sessionIndex, $sessionId, $expire);
        }
    }


    /**
     * Log out of the given sessions.
     *
     * @param string $authId  The authsource ID.
     * @param \SAML2\XML\saml\NameID $nameId The NameID of the user.
     * @param array $sessionIndexes  The SessionIndexes we should log out of. Logs out of all if this is empty.
     * @return int|false  Number of sessions logged out, or FALSE if not supported.
     */
    public static function logoutSessions($authId, $nameId, array $sessionIndexes)
    {
        assert(is_string($authId));

        $store = Store::getInstance();
        if ($store === false) {
            // We don't have a datastore
            return false;
        }

        // serialize and anonymize the NameID
        // TODO: remove this conditional statement
        if (is_array($nameId)) {
            /** @psalm-suppress UndefinedMethod */
            $nameId = NameID::fromArray($nameId);
        }
        $strNameId = serialize($nameId);
        $strNameId = sha1($strNameId);

        // Normalize SessionIndexes
        foreach ($sessionIndexes as &$sessionIndex) {
            assert(is_string($sessionIndex));
            if (strlen($sessionIndex) > 50) {
                $sessionIndex = sha1($sessionIndex);
            }
        }

        // Remove reference
        unset($sessionIndex);

        if ($store instanceof Store\SQL) {
            $sessions = self::getSessionsSQL($store, $authId, $strNameId);
        } else {
            if (empty($sessionIndexes)) {
                // We cannot fetch all sessions without a SQL store
                return false;
            }
            /** @var array $sessions At this point the store cannot be false */
            $sessions = self::getSessionsStore($store, $authId, $strNameId, $sessionIndexes);
        }

        if (empty($sessionIndexes)) {
            $sessionIndexes = array_keys($sessions);
        }

        $numLoggedOut = 0;
        foreach ($sessionIndexes as $sessionIndex) {
            if (!isset($sessions[$sessionIndex])) {
                Logger::info('saml.LogoutStore: Logout requested for unknown SessionIndex.');
                continue;
            }

            $sessionId = $sessions[$sessionIndex];

            $session = Session::getSession($sessionId);
            if ($session === null) {
                Logger::info('saml.LogoutStore: Skipping logout of missing session.');
                continue;
            }

            if (!$session->isValid($authId)) {
                Logger::info(
                    'saml.LogoutStore: Skipping logout of session because it isn\'t authenticated.'
                );
                continue;
            }

            Logger::info(
                'saml.LogoutStore: Logging out of session with trackId [' . $session->getTrackID() . '].'
            );
            $session->doLogout($authId);
            $numLoggedOut += 1;
        }

        return $numLoggedOut;
    }
}
