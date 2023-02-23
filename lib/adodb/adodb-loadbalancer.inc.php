<?php
/**
 * ADOdb Load Balancer
 *
 * ADOdbLoadBalancer is a class that allows the user to do read/write splitting
 * and load balancing across multiple servers. It can handle and load balance
 * any number of write capable (AKA: master) or readonly (AKA: slave) connections,
 * including dealing with connection failures and retrying queries on a different
 * connection instead.
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2016 Mike Benoit and the ADOdb community
 */

/**
 * Class ADOdbLoadBalancer
 */
class ADOdbLoadBalancer
{
    /**
     * @var bool    Once a write or readonly connection is made, stick to that connection for the entire request.
     */
    public $enable_sticky_sessions = true;


    /**
     * @var bool|array    All connections to each database.
     */
    protected $connections = false;

    /**
     * @var bool|array    Just connections to the write capable database.
     */
    protected $connections_write = false;

    /**
     * @var bool|array    Just connections to the readonly database.
     */
    protected $connections_readonly = false;

    /**
     * @var array    Counts of all connections and their types.
     */
    protected $total_connections = array('all' => 0, 'write' => 0, 'readonly' => 0);

    /**
     * @var array    Weights of all connections for each type.
     */
    protected $total_connection_weights = array('all' => 0, 'write' => 0, 'readonly' => 0);

    /**
     * @var bool    When in transactions, always use this connection.
     */
    protected $pinned_connection_id = false;

    /**
     * @var array    Last connection_id for each database type.
     */
    protected $last_connection_id = array('write' => false, 'readonly' => false, 'all' => false);

    /**
     * @var bool    Session variables that must be maintained across all connections, ie: SET TIME ZONE.
     */
    protected $session_variables = false;

    /**
     * @var bool    Called immediately after connecting to any DB.
     */
    protected $user_defined_session_init_sql = false;


    /**
     * Defines SQL queries that are executed each time a new database connection is established.
     *
     * @param  $sql
     * @return bool
     */
    public function setSessionInitSQL($sql)
    {
        $this->user_defined_session_init_sql[] = $sql;

        return true;
    }

    /**
     * Adds a new database connection to the pool, but no actual connection is made until its needed.
     *
     * @param  $obj
     * @return bool
     * @throws Exception
     */
    public function addConnection($obj)
    {
        if ($obj instanceof ADOdbLoadBalancerConnection) {
            $this->connections[] = $obj;
            end($this->connections);
            $i = key($this->connections);

            $this->total_connections[$obj->type]++;
            $this->total_connections['all']++;

            $this->total_connection_weights[$obj->type] += abs($obj->weight);
            $this->total_connection_weights['all'] += abs($obj->weight);

            if ($obj->type == 'write') {
                $this->connections_write[] = $i;
            } else {
                $this->connections_readonly[] = $i;
            }

            return true;
        }

        throw new Exception('Connection object is not an instance of ADOdbLoadBalancerConnection');
    }

    /**
     * Removes a database connection from the pool.
     *
     * @param  $i
     * @return bool
     */
    public function removeConnection($i)
    {
        if (isset($this->connections[$i])) {
               $obj = $this->connections[ $i ];

               $this->total_connections[ $obj->type ]--;
               $this->total_connections['all']--;

               $this->total_connection_weights[ $obj->type ] -= abs($obj->weight);
               $this->total_connection_weights['all'] -= abs($obj->weight);

            if ($obj->type == 'write') {
                unset($this->connections_write[array_search($i, $this->connections_write)]);
                // Reindex array.
                $this->connections_write = array_values($this->connections_write);
            } else {
                unset($this->connections_readonly[array_search($i, $this->connections_readonly)]);
                // Reindex array.
                $this->connections_readonly = array_values($this->connections_readonly);
            }

            // Remove any sticky connections as well.
            if ($this->last_connection_id[$obj->type] == $i) {
                $this->last_connection_id[$obj->type] = false;
            }

            unset($this->connections[$i]);

            return true;
        }

        return false;
    }

    /**
     * Returns a database connection of the specified type.
     *
     * Takes into account the connection weight for load balancing.
     *
     * @param  string $type Type of database connection, either: 'write' capable or 'readonly'
     * @return bool|int|string
     */
    public function getConnectionByWeight($type)
    {
        if ($type == 'readonly') {
            $total_weight = $this->total_connection_weights['all'];
        } else {
            $total_weight = $this->total_connection_weights['write'];
        }

        $i = false;
        if (is_array($this->connections)) {
            $n = 0;
            $num = mt_rand(0, $total_weight);
            foreach ($this->connections as $i => $connection_obj) {
                if ($connection_obj->weight > 0 && ($type == 'readonly' || $connection_obj->type == 'write')) {
                    $n += $connection_obj->weight;
                    if ($n >= $num) {
                        break;
                    }
                }
            }
        }

        return $i;
    }

    /**
     * Returns the proper database connection when taking into account sticky sessions and load balancing.
     *
     * @param  $type
     * @return bool|int|mixed|string
     */
    public function getLoadBalancedConnection($type)
    {
        if ($this->total_connections == 0) {
            $connection_id = 0;
        } else {
            if ($this->enable_sticky_sessions == true && $this->last_connection_id[$type] !== false) {
                $connection_id = $this->last_connection_id[$type];
            } else {
                if ($type == 'write' && $this->total_connections['write'] == 1) {
                    $connection_id = $this->connections_write[0];
                } else {
                    $connection_id = $this->getConnectionByWeight($type);
                }
            }
        }

        return $connection_id;
    }

    /**
     * Returns the ADODB connection object by connection_id.
     *
     * Ensures that it's connected and the session variables are executed.
     *
     * @param  $connection_id
     * @return bool|ADOConnection
     * @throws Exception
     */
    public function getConnectionById($connection_id)
    {
        if (isset($this->connections[$connection_id])) {
            $connection_obj = $this->connections[$connection_id];
            /** @var ADOConnection $adodb_obj */
            $adodb_obj = $connection_obj->getADOdbObject();
            if (is_object($adodb_obj) && $adodb_obj->_connectionID == false) {
                try {
                    if ($connection_obj->persistent_connection == true) {
                        $adodb_obj->Pconnect(
                            $connection_obj->host,
                            $connection_obj->user,
                            $connection_obj->password,
                            $connection_obj->database
                        );
                    } else {
                        $adodb_obj->Connect(
                            $connection_obj->host,
                            $connection_obj->user,
                            $connection_obj->password,
                            $connection_obj->database
                        );
                    }
                } catch (Exception $e) {
                    // Connection error, see if there are other connections to try still.
                    throw $e; // No connections left, reThrow exception so application can catch it.
                }

                // Check to see if a connection test callback was defined, and if so execute it.
                // This is useful for testing replication lag and such to ensure the connection is suitable to be used.
                $test_connection_callback = $connection_obj->getConnectionTestCallback();
                if (is_callable($test_connection_callback)
                    && $test_connection_callback($connection_obj, $adodb_obj) !== TRUE
                ) {
                    return false;
                }

                if (is_array($this->user_defined_session_init_sql)) {
                    foreach ($this->user_defined_session_init_sql as $session_init_sql) {
                        $adodb_obj->Execute($session_init_sql);
                    }
                }
                $this->executeSessionVariables($adodb_obj);
            }

            return $adodb_obj;
        } else {
            throw new Exception('Unable to return Connection object...');
        }
    }

    /**
     * Returns the ADODB connection object by database type.
     *
     * Ensures that it's connected and the session variables are executed.
     *
     * @param  string $type
     * @param  null   $pin_connection
     * @return ADOConnection|bool
     * @throws Exception
     */
    public function getConnection($type = 'write', $pin_connection = null)
    {
        while (($type == 'write' && $this->total_connections['write'] > 0)
            || ($type == 'readonly' && $this->total_connections['all'] > 0)
        ) {
            if ($this->pinned_connection_id !== false) {
                $connection_id = $this->pinned_connection_id;
            } else {
                $connection_id = $this->getLoadBalancedConnection($type);
            }

            if ($connection_id !== false) {
                try {
                    $adodb_obj = $this->getConnectionById($connection_id);
                    if (is_object($adodb_obj)) {
                        break; //Found valid connection, continue with it.
                    } else {
                        throw new Exception('ADODB Connection Object does not exist. Perhaps LoadBalancer Database Connection Test Failed?');
                    }
                } catch (Exception $e) {
                    // Connection error, see if there are other connections to try still.
                    $this->removeConnection($connection_id);
                    if (   ($type == 'write' && $this->total_connections['write'] == 0)
                        || ($type == 'readonly' && $this->total_connections['all'] == 0)
                    ) {
                        throw $e;
                    }
                }
            } else {
                throw new Exception('Connection ID is invalid!');
            }
        }

        if (!isset($connection_id)) {
            throw new Exception('No connection available to use at this time! Type: ' . $type);
        }

        $this->last_connection_id[$type] = $connection_id;

        if ($pin_connection === true) {
            $this->pinned_connection_id = $connection_id;
        } elseif ($pin_connection === false && $adodb_obj->transOff <= 1) {
            // UnPin connection only if we are 1 level deep in a transaction.
            $this->pinned_connection_id = false;

            // When unpinning connection, reset last_connection_id so readonly
            // queries don't get stuck on the write capable connection.
            $this->last_connection_id['write'] = false;
            $this->last_connection_id['readonly'] = false;
        }

        return $adodb_obj;
    }

    /**
     * This is a hack to work around pass by reference error.
     *
     * Parameter 1 to ADOConnection::GetInsertSQL() expected to be a reference,
     * value given in adodb-loadbalancer.inc.php on line 83
     *
     * @param  $arr
     * @return array
     */
    private function makeValuesReferenced($arr)
    {
        $refs = array();

        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }

        return $refs;
    }

    /**
     * Allow setting session variables that are maintained across connections.
     *
     * Its important that these are set using name/value, so it can determine
     * if the same variable is set multiple times causing bloat/clutter when
     * new connections are established. For example if the time_zone is set to
     * many different ones through the course of a single connection, a new
     * connection should only set it to the most recent value.
     *
     * @param  $name
     * @param  $value
     * @param  bool  $execute_immediately
     * @return array|bool|mixed
     * @throws Exception
     */
    public function setSessionVariable($name, $value, $execute_immediately = true)
    {
        $this->session_variables[$name] = $value;

        if ($execute_immediately == true) {
            return $this->executeSessionVariables();
        } else {
            return true;
        }
    }

    /**
     * Executes the session variables on a given ADODB object.
     *
     * @param  ADOConnection|bool $adodb_obj
     * @return array|bool|mixed
     * @throws Exception
     */
    private function executeSessionVariables($adodb_obj = false)
    {
        if (is_array($this->session_variables)) {
            $sql = '';
            foreach ($this->session_variables as $name => $value) {
                // $sql .= 'SET SESSION '. $name .' '. $value;
                // MySQL uses: SET SESSION foo_bar='foo'
                // PGSQL uses: SET SESSION foo_bar 'foo'
                // So leave it up to the user to pass the proper value with '=' if needed.
                // This may be a candidate to move into ADOdb proper.
                $sql .= 'SET SESSION ' . $name . ' ' . $value;
            }

            if ($adodb_obj !== false) {
                return $adodb_obj->Execute($sql);
            } else {
                return $this->ClusterExecute($sql);
            }
        }

        return false;
    }

    /**
     * Executes the same SQL QUERY on the entire cluster of connections.
     * Would be used for things like SET SESSION TIME ZONE calls and such.
     *
     * @param  $sql
     * @param  bool $inputarr
     * @param  bool $return_all_results
     * @param  bool $existing_connections_only
     * @return array|bool|mixed
     * @throws Exception
     */
    public function clusterExecute(
        $sql,
        $inputarr = false,
        $return_all_results = false,
        $existing_connections_only = true
    ) {
        if (is_array($this->connections) && count($this->connections) > 0) {
            foreach ($this->connections as $key => $connection_obj) {
                if ($existing_connections_only == false
                    || ($existing_connections_only == true
                        && $connection_obj->getADOdbObject()->_connectionID !== false
                    )
                ) {
                    $adodb_obj = $this->getConnectionById($key);
                    if (is_object($adodb_obj)) {
                        $result_arr[] = $adodb_obj->Execute($sql, $inputarr);
                    }
                }
            }

            if (isset($result_arr) && $return_all_results == true) {
                return $result_arr;
            } else {
                // Loop through all results checking to see if they match, if they do return the first one
                // otherwise return an array of all results.
                if (isset($result_arr)) {
                    foreach ($result_arr as $result) {
                        if ($result == false) {
                            return $result_arr;
                        }
                    }

                    return $result_arr[0];
                } else {
                    // When using lazy connections, there are cases where
                    // setSessionVariable() is called early on, but there are
                    // no connections to execute the queries on yet.
                    // This captures that case and forces a RETURN TRUE to occur.
                    // As likely the queries will be executed as soon as a
                    // connection is established.
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determines if a SQL query is read-only or not.
     *
     * @param  string $sql SQL Query to test.
     * @return bool
     */
    public function isReadOnlyQuery($sql)
    {
        if (   stripos($sql, 'SELECT') === 0
            && stripos($sql, 'FOR UPDATE') === false
            && stripos($sql, ' INTO ') === false
            && stripos($sql, 'LOCK IN') === false
        ) {
            return true;
        }

        return false;
    }

    /**
     * Use this instead of __call() as it significantly reduces the overhead of call_user_func_array().
     *
     * @param  $sql
     * @param  bool $inputarr
     * @return array|bool|mixed
     * @throws Exception
     */
    public function execute($sql, $inputarr = false)
    {
        $type = 'write';
        $pin_connection = null;

        // Prevent leading spaces from causing isReadOnlyQuery/stripos from failing.
        $sql = trim($sql);

        // SELECT queries that can write and therefore must be run on a write capable connection.
        // SELECT ... FOR UPDATE;
        // SELECT ... INTO ...
        // SELECT .. LOCK IN ... (MYSQL)
        if ($this->isReadOnlyQuery($sql) == true) {
            $type = 'readonly';
        } elseif (stripos($sql, 'SET') === 0) {
            // SET SQL statements should likely use setSessionVariable() instead,
            // so state is properly maintained across connections, especially when they are lazily created.
            return $this->ClusterExecute($sql, $inputarr);
        }

        $adodb_obj = $this->getConnection($type, $pin_connection);
        if ($adodb_obj !== false) {
            return $adodb_obj->Execute($sql, $inputarr);
        }

        return false;
    }

    /**
     * Magic method to intercept method and callback to the proper ADODB object for write/readonly connections.
     *
     * @param  string $method ADODB method to call.
     * @param  array  $args   Arguments to the ADODB method.
     * @return bool|mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        $type = 'write';
        $pin_connection = null;

        // Intercept specific methods to determine if they are read-only or not.
        $method = strtolower($method);
        switch ($method) {
            // case 'execute': // This is the direct overloaded function above instead.
            case 'getone':
            case 'getrow':
            case 'getall':
            case 'getcol':
            case 'getassoc':
            case 'selectlimit':
                if ($this->isReadOnlyQuery(trim($args[0])) == true) {
                    $type = 'readonly';
                }
                break;
            case 'cachegetone':
            case 'cachegetrow':
            case 'cachegetall':
            case 'cachegetcol':
            case 'cachegetassoc':
            case 'cacheexecute':
            case 'cacheselect':
            case 'pageexecute':
            case 'cachepageexecute':
                $type = 'readonly';
                break;
                // case 'ignoreerrors':
                // 	// When ignoreerrors is called, PIN to the connection until its called again.
                // 	if (!isset($args[0]) || (isset($args[0]) && $args[0] == FALSE)) {
                // 		$pin_connection = TRUE;
                // 	} else {
                // 		$pin_connection = FALSE;
                // 	}
                // 	break;

                // Manual transactions
            case 'begintrans':
            case 'settransactionmode':
                    $pin_connection = true;
                break;
            case 'rollbacktrans':
            case 'committrans':
                $pin_connection = false;
                break;
                // Smart transactions
            case 'starttrans':
                $pin_connection = true;
                break;
            case 'completetrans':
            case 'failtrans':
                // getConnection() will only unpin the transaction if we're exiting the last nested transaction
                $pin_connection = false;
                break;

            // Functions that don't require any connection and therefore
            // shouldn't force a connection be established before they run.
            case 'qstr':
            case 'escape':
            case 'binddate':
            case 'bindtimestamp':
            case 'setfetchmode':
            case 'setcustommetatype':
                  $type = false; // No connection necessary.
                break;

            // Default to assuming write connection is required to be on the safe side.
            default:
                break;
        }

        if ($type === false) {
            if (is_array($this->connections) && count($this->connections) > 0) {
                foreach ($this->connections as $key => $connection_obj) {
                    $adodb_obj = $connection_obj->getADOdbObject();
                    return call_user_func_array(array($adodb_obj, $method), $this->makeValuesReferenced($args)); // Just makes the function call on the first object.
                }
            }
        } else {
               $adodb_obj = $this->getConnection($type, $pin_connection);
            if (is_object($adodb_obj)) {
                $result = call_user_func_array(array($adodb_obj, $method), $this->makeValuesReferenced($args));

                return $result;
            }
        }
        return false;
    }

    /**
     * Magic method to proxy property getter calls back to the proper ADODB object currently in use.
     *
     * @param  $property
     * @return mixed
     * @throws Exception
     */
    public function __get($property)
    {
        if (is_array($this->connections) && count($this->connections) > 0) {
            foreach ($this->connections as $key => $connection_obj) {
                // Just returns the property from the first object.
                return $connection_obj->getADOdbObject()->$property;
            }
        }

        return false;
    }

    /**
     * Magic method to proxy property setter calls back to the proper ADODB object currently in use.
     *
     * @param  $property
     * @param  $value
     * @return mixed
     * @throws Exception
     */
    public function __set($property, $value)
    {
        // Special function to set object properties on all objects
        // without initiating a connection to the database.
        if (is_array($this->connections) && count($this->connections) > 0) {
            foreach ($this->connections as $key => $connection_obj) {
                $connection_obj->getADOdbObject()->$property = $value;
            }

               return true;
        }

        return false;
    }

    /**
     *  Override the __clone() magic method.
     */
    private function __clone()
    {
    }
}

/**
 * Class ADOdbLoadBalancerConnection
 */
class ADOdbLoadBalancerConnection
{
    /**
     * @var bool    ADOdb drive name.
     */
    protected $driver = false;

    /**
     * @var bool    ADODB object.
     */
    protected $adodb_obj = false;

    /**
     * @var callable    Closure
     */
    protected $connection_test_callback = NULL;

    /**
     * @var string    Type of connection, either 'write' capable or 'readonly'
     */
    public $type = 'write';

    /**
     * @var int        Weight of connection, lower receives less queries, higher receives more queries.
     */
    public $weight = 1;

    /**
     * @var bool    Determines if the connection persistent.
     */
    public $persistent_connection = false;

    /**
     * @var string    Database connection host
     */
    public $host = '';

    /**
     * @var string    Database connection user
     */
    public $user = '';

    /**
     * @var string    Database connection password
     */
    public $password = '';

    /**
     * @var string    Database connection database name
     */
    public $database = '';

    /**
     * ADOdbLoadBalancerConnection constructor to setup the ADODB object.
     *
     * @param $driver
     * @param string $type
     * @param int    $weight
     * @param bool   $persistent_connection
     * @param string $argHostname
     * @param string $argUsername
     * @param string $argPassword
     * @param string $argDatabaseName
     */
    public function __construct(
        $driver,
        $type = 'write',
        $weight = 1,
        $persistent_connection = false,
        $argHostname = '',
        $argUsername = '',
        $argPassword = '',
        $argDatabaseName = ''
    ) {
        if ($type !== 'write' && $type !== 'readonly') {
            return false;
        }

        $this->adodb_obj = ADONewConnection($driver);

        $this->type = $type;
        $this->weight = $weight;
        $this->persistent_connection = $persistent_connection;

        $this->host = $argHostname;
        $this->user = $argUsername;
        $this->password = $argPassword;
        $this->database = $argDatabaseName;

        return true;
    }

    /**
     * Anonymous function that is called and must return TRUE for the connection to be usable.*
     *   The first argument is the type of connection to test.
     *   Useful to check things like replication lag.
     * @param callable $callback
     * @return void
     */
    function setConnectionTestCallback($callback) {
        $this->connection_test_callback = $callback;
    }

    /**
     * @return callable|null
     */
    function getConnectionTestCallback() {
        return $this->connection_test_callback;
    }

    /**
     * Returns the ADODB object for this connection.
     *
     * @return bool
     */
    public function getADOdbObject()
    {
        return $this->adodb_obj;
    }
}
