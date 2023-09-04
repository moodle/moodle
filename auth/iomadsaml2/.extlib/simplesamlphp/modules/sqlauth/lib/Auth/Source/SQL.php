<?php

namespace SimpleSAML\Module\sqlauth\Auth\Source;

use Exception;
use PDO;
use PDOException;
use SimpleSAML\Error;
use SimpleSAML\Logger;

/**
 * Simple SQL authentication source
 *
 * This class is an example authentication source which authenticates an user
 * against a SQL database.
 *
 * @package SimpleSAMLphp
 */

class SQL extends \SimpleSAML\Module\core\Auth\UserPassBase
{
    /**
     * The DSN we should connect to.
     */
    private $dsn;

    /**
     * The username we should connect to the database with.
     */
    private $username;

    /**
     * The password we should connect to the database with.
     */
    private $password;

    /**
     * The options that we should connect to the database with.
     */
    private $options;

    /**
     * The query we should use to retrieve the attributes for the user.
     *
     * The username and password will be available as :username and :password.
     */
    private $query;

    /**
     * Constructor for this authentication source.
     *
     * @param array $info  Information about this authentication source.
     * @param array $config  Configuration.
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        // Make sure that all required parameters are present.
        foreach (['dsn', 'username', 'password', 'query'] as $param) {
            if (!array_key_exists($param, $config)) {
                throw new Exception('Missing required attribute \''.$param.
                    '\' for authentication source '.$this->authId);
            }

            if (!is_string($config[$param])) {
                throw new Exception('Expected parameter \''.$param.
                    '\' for authentication source '.$this->authId.
                    ' to be a string. Instead it was: '.
                    var_export($config[$param], true));
            }
        }

        $this->dsn = $config['dsn'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->query = $config['query'];
        if (isset($config['options'])) {
            $this->options = $config['options'];
        }
    }


    /**
     * Create a database connection.
     *
     * @return \PDO  The database connection.
     */
    private function connect()
    {
        try {
            $db = new PDO($this->dsn, $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            // Obfuscate the password if it's part of the dsn
            $obfuscated_dsn =  preg_replace('/(user|password)=(.*?([;]|$))/', '${1}=***', $this->dsn);

            throw new Exception('sqlauth:' . $this->authId . ': - Failed to connect to \'' .
                $obfuscated_dsn . '\': ' . $e->getMessage());
        }

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $driver = explode(':', $this->dsn, 2);
        $driver = strtolower($driver[0]);

        // Driver specific initialization
        switch ($driver) {
            case 'mysql':
                // Use UTF-8
                $db->exec("SET NAMES 'utf8mb4'");
                break;
            case 'pgsql':
                // Use UTF-8
                $db->exec("SET NAMES 'UTF8'");
                break;
        }

        return $db;
    }


    /**
     * Attempt to log in using the given username and password.
     *
     * On a successful login, this function should return the users attributes. On failure,
     * it should throw an exception. If the error was caused by the user entering the wrong
     * username or password, a \SimpleSAML\Error\Error('WRONGUSERPASS') should be thrown.
     *
     * Note that both the username and the password are UTF-8 encoded.
     *
     * @param string $username  The username the user wrote.
     * @param string $password  The password the user wrote.
     * @return array  Associative array with the users attributes.
     */
    protected function login($username, $password)
    {
        assert(is_string($username));
        assert(is_string($password));

        $db = $this->connect();

        try {
            $sth = $db->prepare($this->query);
        } catch (PDOException $e) {
            throw new Exception('sqlauth:'.$this->authId.
                ': - Failed to prepare query: '.$e->getMessage());
        }

        try {
            $sth->execute(['username' => $username, 'password' => $password]);
        } catch (PDOException $e) {
            throw new Exception('sqlauth:'.$this->authId.
                ': - Failed to execute query: '.$e->getMessage());
        }

        try {
            $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('sqlauth:'.$this->authId.
                ': - Failed to fetch result set: '.$e->getMessage());
        }

        Logger::info('sqlauth:'.$this->authId.': Got '.count($data).
            ' rows from database');

        if (count($data) === 0) {
            // No rows returned - invalid username/password
            Logger::error('sqlauth:'.$this->authId.
                ': No rows in result set. Probably wrong username/password.');
            throw new Error\Error('WRONGUSERPASS');
        }

        /* Extract attributes. We allow the resultset to consist of multiple rows. Attributes
         * which are present in more than one row will become multivalued. null values and
         * duplicate values will be skipped. All values will be converted to strings.
         */
        $attributes = [];
        foreach ($data as $row) {
            foreach ($row as $name => $value) {
                if ($value === null) {
                    continue;
                }

                $value = (string) $value;

                if (!array_key_exists($name, $attributes)) {
                    $attributes[$name] = [];
                }

                if (in_array($value, $attributes[$name], true)) {
                    // Value already exists in attribute
                    continue;
                }

                $attributes[$name][] = $value;
            }
        }

        Logger::info('sqlauth:'.$this->authId.': Attributes: '.
            implode(',', array_keys($attributes)));

        return $attributes;
    }
}
