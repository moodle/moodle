<?php

namespace SimpleSAML\Module\consent\Consent\Store;

/**
 * Store consent in database.
 *
 * This class implements a consent store which stores the consent information in a database. It is tested, and should
 * work against MySQL, PostgreSQL and SQLite.
 *
 * It has the following options:
 * - dsn: The DSN which should be used to connect to the database server. See the PHP Manual for supported drivers and
 *   DSN formats.
 * - username: The username used for database connection.
 * - password: The password used for database connection.
 * - table: The name of the table used. Optional, defaults to 'consent'.
 *
 * @author Olav Morken <olav.morken@uninett.no>
 * @package SimpleSAMLphp
 */

class Database extends \SimpleSAML\Module\consent\Store
{
    /**
     * DSN for the database.
     */
    private $dsn;

    /**
     * The DATETIME SQL function to use
     */
    private $dateTime;

    /**
     * Username for the database.
     */
    private $username;

    /**
     * Password for the database;
     */
    private $password;

    /**
     * Options for the database;
     */
    private $options;

    /**
     * Table with consent.
     */
    private $table;

    /**
     * The timeout of the database connection.
     *
     * @var int|null
     */
    private $timeout = null;

    /**
     * Database handle.
     *
     * This variable can't be serialized.
     */
    private $db;


    /**
     * Parse configuration.
     *
     * This constructor parses the configuration.
     *
     * @param array $config Configuration for database consent store.
     *
     * @throws \Exception in case of a configuration error.
     */
    public function __construct($config)
    {
        parent::__construct($config);

        if (!array_key_exists('dsn', $config)) {
            throw new \Exception('consent:Database - Missing required option \'dsn\'.');
        }
        if (!is_string($config['dsn'])) {
            throw new \Exception('consent:Database - \'dsn\' is supposed to be a string.');
        }

        $this->dsn = $config['dsn'];
        $this->dateTime = (0 === strpos($this->dsn, 'sqlite:')) ? 'DATETIME("NOW")' : 'NOW()';

        if (array_key_exists('username', $config)) {
            if (!is_string($config['username'])) {
                throw new \Exception('consent:Database - \'username\' is supposed to be a string.');
            }
            $this->username = $config['username'];
        } else {
            $this->username = null;
        }

        if (array_key_exists('password', $config)) {
            if (!is_string($config['password'])) {
                throw new \Exception('consent:Database - \'password\' is supposed to be a string.');
            }
            $this->password = $config['password'];
        } else {
            $this->password = null;
        }

        if (array_key_exists('options', $config)) {
            if (!is_array($config['options'])) {
                throw new \Exception('consent:Database - \'options\' is supposed to be an array.');
            }
            $this->options = $config['options'];
        } else {
            $this->options = null;
        }
        if (array_key_exists('table', $config)) {
            if (!is_string($config['table'])) {
                throw new \Exception('consent:Database - \'table\' is supposed to be a string.');
            }
            $this->table = $config['table'];
        } else {
            $this->table = 'consent';
        }

        if (isset($config['timeout'])) {
            if (!is_int($config['timeout'])) {
                throw new \Exception('consent:Database - \'timeout\' is supposed to be an integer.');
            }
            $this->timeout = $config['timeout'];
        }
    }


    /**
     * Called before serialization.
     *
     * @return array The variables which should be serialized.
     */
    public function __sleep()
    {
        return [
            'dsn',
            'dateTime',
            'username',
            'password',
            'table',
            'timeout',
        ];
    }


    /**
     * Check for consent.
     *
     * This function checks whether a given user has authorized the release of
     * the attributes identified by $attributeSet from $source to $destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return bool True if the user has given consent earlier, false if not
     *              (or on error).
     */
    public function hasConsent($userId, $destinationId, $attributeSet)
    {
        assert(is_string($userId));
        assert(is_string($destinationId));
        assert(is_string($attributeSet));

        $st = $this->execute(
            'UPDATE '.$this->table.' '.
            'SET usage_date = '.$this->dateTime.' '.
            'WHERE hashed_user_id = ? AND service_id = ? AND attribute = ?',
            [$userId, $destinationId, $attributeSet]
        );

        if ($st === false) {
            return false;
        }

        $rowCount = $st->rowCount();
        if ($rowCount === 0) {
            \SimpleSAML\Logger::debug('consent:Database - No consent found.');
            return false;
        } else {
            \SimpleSAML\Logger::debug('consent:Database - Consent found.');
            return true;
        }
    }


    /**
     * Save consent.
     *
     * Called when the user asks for the consent to be saved. If consent information
     * for the given user and destination already exists, it should be overwritten.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     * @param string $attributeSet  A hash which identifies the attributes.
     *
     * @return bool True if consent is deleted, false otherwise.
     */
    public function saveConsent($userId, $destinationId, $attributeSet)
    {
        assert(is_string($userId));
        assert(is_string($destinationId));
        assert(is_string($attributeSet));

        // Check for old consent (with different attribute set)
        $st = $this->execute(
            'UPDATE '.$this->table.' '.
            'SET consent_date = '.$this->dateTime.', usage_date = '.$this->dateTime.', attribute = ? '.
            'WHERE hashed_user_id = ? AND service_id = ?',
            [$attributeSet, $userId, $destinationId]
        );

        if ($st === false) {
            return false;
        }

        if ($st->rowCount() > 0) {
            // Consent has already been stored in the database
            \SimpleSAML\Logger::debug('consent:Database - Updated old consent.');
            return false;
        }

        // Add new consent
        $st = $this->execute(
            'INSERT INTO '.$this->table.' ('.'consent_date, usage_date, hashed_user_id, service_id, attribute'.
            ') '.'VALUES ('.$this->dateTime.', '.$this->dateTime.', ?, ?, ?)',
            [$userId, $destinationId, $attributeSet]
        );

        if ($st !== false) {
            \SimpleSAML\Logger::debug('consent:Database - Saved new consent.');
        }
        return true;
    }


    /**
     * Delete consent.
     *
     * Called when a user revokes consent for a given destination.
     *
     * @param string $userId        The hash identifying the user at an IdP.
     * @param string $destinationId A string which identifies the destination.
     *
     * @return int Number of consents deleted
     */
    public function deleteConsent($userId, $destinationId)
    {
        assert(is_string($userId));
        assert(is_string($destinationId));

        $st = $this->execute(
            'DELETE FROM '.$this->table.' WHERE hashed_user_id = ? AND service_id = ?;',
            [$userId, $destinationId]
        );

        if ($st === false) {
            return 0;
        }

        if ($st->rowCount() > 0) {
            \SimpleSAML\Logger::debug('consent:Database - Deleted consent.');
            return $st->rowCount();
        }

        \SimpleSAML\Logger::warning('consent:Database - Attempted to delete nonexistent consent');
        return 0;
    }


    /**
     * Delete all consents.
     *
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return int Number of consents deleted
     */
    public function deleteAllConsents($userId)
    {
        assert(is_string($userId));

        $st = $this->execute(
            'DELETE FROM '.$this->table.' WHERE hashed_user_id = ?',
            [$userId]
        );

        if ($st === false) {
            return 0;
        }

        if ($st->rowCount() > 0) {
            \SimpleSAML\Logger::debug('consent:Database - Deleted ('.$st->rowCount().') consent(s).');
            return $st->rowCount();
        }

        \SimpleSAML\Logger::warning('consent:Database - Attempted to delete nonexistent consent');
        return 0;
    }


    /**
     * Retrieve consents.
     *
     * This function should return a list of consents the user has saved.
     *
     * @param string $userId The hash identifying the user at an IdP.
     *
     * @return array Array of all destination ids the user has given consent for.
     */
    public function getConsents($userId)
    {
        assert(is_string($userId));

        $ret = [];

        $st = $this->execute(
            'SELECT service_id, attribute, consent_date, usage_date FROM '.$this->table.
            ' WHERE hashed_user_id = ?',
            [$userId]
        );

        if ($st === false) {
            return [];
        }

        while ($row = $st->fetch(\PDO::FETCH_NUM)) {
            $ret[] = $row;
        }

        return $ret;
    }


    /**
     * Prepare and execute statement.
     *
     * This function prepares and executes a statement. On error, false will be
     * returned.
     *
     * @param string $statement  The statement which should be executed.
     * @param array  $parameters Parameters for the statement.
     *
     * @return \PDOStatement|false  The statement, or false if execution failed.
     */
    private function execute($statement, $parameters)
    {
        assert(is_string($statement));
        assert(is_array($parameters));

        $db = $this->getDB();
        if ($db === false) {
            return false;
        }

        /** @var \PDOStatement|false $st */
        $st = $db->prepare($statement);
        if ($st === false) {
            \SimpleSAML\Logger::error(
                'consent:Database - Error preparing statement \''.
                $statement.'\': '.self::formatError($db->errorInfo())
            );
            return false;
        }

        if ($st->execute($parameters) !== true) {
            \SimpleSAML\Logger::error(
                'consent:Database - Error executing statement \''.
                $statement.'\': '.self::formatError($st->errorInfo())
            );
            return false;
        }

        return $st;
    }


    /**
     * Get statistics from the database
     *
     * The returned array contains 3 entries
     * - total: The total number of consents
     * - users: Total number of uses that have given consent
     * ' services: Total number of services that has been given consent to
     *
     * @return array Array containing the statistics
     */
    public function getStatistics()
    {
        $ret = [];

        // Get total number of consents
        $st = $this->execute('SELECT COUNT(*) AS no FROM '.$this->table, []);

        if ($st === false) {
            return [];
        }

        if ($row = $st->fetch(\PDO::FETCH_NUM)) {
            $ret['total'] = $row[0];
        }

        // Get total number of users that has given consent
        $st = $this->execute(
            'SELECT COUNT(*) AS no '.
            'FROM (SELECT DISTINCT hashed_user_id FROM '.$this->table.' ) AS foo',
            []
        );

        if ($st === false) {
            return [];
        }

        if ($row = $st->fetch(\PDO::FETCH_NUM)) {
            $ret['users'] = $row[0];
        }

        // Get total number of services that has been given consent to
        $st = $this->execute(
            'SELECT COUNT(*) AS no FROM (SELECT DISTINCT service_id FROM '.$this->table.') AS foo',
            []
        );

        if ($st === false) {
            return [];
        }

        if ($row = $st->fetch(\PDO::FETCH_NUM)) {
            $ret['services'] = $row[0];
        }

        return $ret;
    }


    /**
     * Get database handle.
     *
     * @return \PDO|false Database handle, or false if we fail to connect.
     */
    private function getDB()
    {
        if ($this->db !== null) {
            return $this->db;
        }

        $driver_options = [];
        if (isset($this->timeout)) {
            $driver_options[\PDO::ATTR_TIMEOUT] = $this->timeout;
        }
        if (isset($this->options)) {
            $this->options = array_merge($driver_options, $this->options);
        } else {
            $this->options = $driver_options;
        }

        $this->db = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        return $this->db;
    }


    /**
     * Format PDO error.
     *
     * This function formats a PDO error, as returned from errorInfo.
     *
     * @param array $error The error information.
     *
     * @return string Error text.
     */
    private static function formatError($error)
    {
        assert(is_array($error));
        assert(count($error) >= 3);

        return $error[0].' - '.$error[2].' ('.$error[1].')';
    }


    /**
     * A quick selftest of the consent database.
     *
     * @return boolean True if OK, false if not. Will throw an exception on connection errors.
     */
    public function selftest()
    {
        $st = $this->execute(
            'SELECT * FROM '.$this->table.' WHERE hashed_user_id = ? AND service_id = ? AND attribute = ?',
            ['test', 'test', 'test']
        );

        if ($st === false) {
            // normally, the test will fail by an exception, so we won't reach this code
            return false;
        }
        return true;
    }
}
