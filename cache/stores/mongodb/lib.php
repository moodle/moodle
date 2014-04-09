<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The library file for the MongoDB store plugin.
 *
 * This file is part of the MongoDB store plugin, it contains the API for interacting with an instance of the store.
 *
 * @package    cachestore_mongodb
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The MongoDB Cache store.
 *
 * This cache store uses the MongoDB Native Driver.
 * For installation instructions have a look at the following two links:
 *  - {@link http://www.php.net/manual/en/mongo.installation.php}
 *  - {@link http://www.mongodb.org/display/DOCS/PHP+Language+Center}
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_mongodb extends cache_store implements cache_is_configurable {

    /**
     * The name of the store
     * @var string
     */
    protected $name;

    /**
     * The server connection string. Comma separated values.
     * @var string
     */
    protected $server = 'mongodb://127.0.0.1:27017';

    /**
     * The database connection options
     * @var array
     */
    protected $options = array();

    /**
     * The name of the database to use.
     * @var string
     */
    protected $databasename = 'mcache';

    /**
     * The Connection object
     * @var Mongo
     */
    protected $connection = false;

    /**
     * The Database Object
     * @var MongoDB
     */
    protected $database;

    /**
     * The Collection object
     * @var MongoCollection
     */
    protected $collection;

    /**
     * Determines if and what safe setting is to be used.
     * @var bool|int
     */
    protected $usesafe = false;

    /**
     * If set to true then multiple identifiers will be requested and used.
     * @var bool
     */
    protected $extendedmode = false;

    /**
     * The definition has which is used in the construction of the collection.
     * @var string
     */
    protected $definitionhash = null;

    /**
     * Set to true once this store is ready to be initialised and used.
     * @var bool
     */
    protected $isready = false;

    /**
     * Constructs a new instance of the Mongo store.
     *
     * Noting that this function is not an initialisation. It is used to prepare the store for use.
     * The store will be initialised when required and will be provided with a cache_definition at that time.
     *
     * @param string $name
     * @param array $configuration
     */
    public function __construct($name, array $configuration = array()) {
        $this->name = $name;

        if (array_key_exists('server', $configuration)) {
            $this->server = $configuration['server'];
        }

        if (array_key_exists('replicaset', $configuration)) {
            $this->options['replicaSet'] = (string)$configuration['replicaset'];
        }
        if (array_key_exists('username', $configuration) && !empty($configuration['username'])) {
            $this->options['username'] = (string)$configuration['username'];
        }
        if (array_key_exists('password', $configuration) && !empty($configuration['password'])) {
            $this->options['password'] = (string)$configuration['password'];
        }
        if (array_key_exists('database', $configuration)) {
            $this->databasename = (string)$configuration['database'];
        }
        if (array_key_exists('usesafe', $configuration)) {
            $this->usesafe = $configuration['usesafe'];
        }
        if (array_key_exists('extendedmode', $configuration)) {
            $this->extendedmode = $configuration['extendedmode'];
        }

        try {
            $this->connection = new Mongo($this->server, $this->options);
            $this->isready = true;
        } catch (MongoConnectionException $e) {
            // We only want to catch MongoConnectionExceptions here.
        }
    }

    /**
     * Returns true if the requirements of this store have been met.
     * @return bool
     */
    public static function are_requirements_met() {
        return class_exists('Mongo');
    }

    /**
     * Returns the supported features.
     * @param array $configuration
     * @return int
     */
    public static function get_supported_features(array $configuration = array()) {
        $supports = self::SUPPORTS_DATA_GUARANTEE;
        if (array_key_exists('extendedmode', $configuration) && $configuration['extendedmode']) {
            $supports += self::SUPPORTS_MULTIPLE_IDENTIFIERS;
        }
        return $supports;
    }

    /**
     * Returns an int describing the supported modes.
     * @param array $configuration
     * @return int
     */
    public static function get_supported_modes(array $configuration = array()) {
        return self::MODE_APPLICATION;
    }

    /**
     * Initialises the store instance for use.
     *
     * Once this has been done the cache is all set to be used.
     *
     * @param cache_definition $definition
     * @throws coding_exception
     */
    public function initialise(cache_definition $definition) {
        if ($this->is_initialised()) {
            throw new coding_exception('This mongodb instance has already been initialised.');
        }
        $this->database = $this->connection->selectDB($this->databasename);
        $this->definitionhash = $definition->generate_definition_hash();
        $this->collection = $this->database->selectCollection($this->definitionhash);
        $this->collection->ensureIndex(array('key' => 1), array(
            'safe' => $this->usesafe,
            'name' => 'idx_key'
        ));
    }

    /**
     * Returns true if this store instance has been initialised.
     * @return bool
     */
    public function is_initialised() {
        return ($this->database instanceof MongoDB);
    }

    /**
     * Returns true if this store instance is ready to use.
     * @return bool
     */
    public function is_ready() {
        return $this->isready;
    }

    /**
     * Returns true if the given mode is supported by this store.
     * @param int $mode
     * @return bool
     */
    public static function is_supported_mode($mode) {
        return ($mode == self::MODE_APPLICATION || $mode == self::MODE_SESSION);
    }

    /**
     * Returns true if this store is making use of multiple identifiers.
     * @return bool
     */
    public function supports_multiple_identifiers() {
        return $this->extendedmode;
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key) {
        if (!is_array($key)) {
            $key = array('key' => $key);
        }

        $result = $this->collection->findOne($key);
        if ($result === null || !array_key_exists('data', $result)) {
            return false;
        }
        $data = @unserialize($result['data']);
        return $data;
    }

    /**
     * Retrieves several items from the cache store in a single transaction.
     *
     * If not all of the items are available in the cache then the data value for those that are missing will be set to false.
     *
     * @param array $keys The array of keys to retrieve
     * @return array An array of items from the cache.
     */
    public function get_many($keys) {
        if ($this->extendedmode) {
            $query = $this->get_many_extendedmode_query($keys);
            $keyarray = array();
            foreach ($keys as $key) {
                $keyarray[] = $key['key'];
            }
            $keys = $keyarray;
            $query = array('key' => array('$in' => $keys));
        } else {
            $query = array('key' => array('$in' => $keys));
        }
        $cursor = $this->collection->find($query);
        $results = array();
        foreach ($cursor as $result) {
            $id = (string)$result['key'];
            $results[$id] = unserialize($result['data']);
        }
        foreach ($keys as $key) {
            if (!array_key_exists($key, $results)) {
                $results[$key] = false;
            }
        }
        return $results;
    }

    /**
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data) {
        if (!is_array($key)) {
            $record = array(
                'key' => $key
            );
        } else {
            $record = $key;
        }
        $record['data'] = serialize($data);
        $options = array(
            'upsert' => true,
            'safe' => $this->usesafe,
            'w' => $this->usesafe ? 1 : 0
        );
        $this->delete($key);
        $result = $this->collection->insert($record, $options);
        if ($result === true) {
            // Safe mode is off.
            return true;
        } else if (is_array($result)) {
            if (empty($result['ok']) || isset($result['err'])) {
                return false;
            }
            return true;
        }
        // Who knows?
        return false;
    }

    /**
     * Sets many items in the cache in a single transaction.
     *
     * @param array $keyvaluearray An array of key value pairs. Each item in the array will be an associative array with two
     *      keys, 'key' and 'value'.
     * @return int The number of items successfully set. It is up to the developer to check this matches the number of items
     *      sent ... if they care that is.
     */
    public function set_many(array $keyvaluearray) {
        $count = 0;
        foreach ($keyvaluearray as $pair) {
            $result = $this->set($pair['key'], $pair['value']);
            if ($result === true) {
                 $count++;
            }
        }
        return $count;
    }

    /**
     * Deletes an item from the cache store.
     *
     * @param string $key The key to delete.
     * @return bool Returns true if the operation was a success, false otherwise.
     */
    public function delete($key) {
        if (!is_array($key)) {
            $criteria = array(
                'key' => $key
            );
        } else {
            $criteria = $key;
        }
        $options = array(
            'justOne' => false,
            'safe' => $this->usesafe,
            'w' => $this->usesafe ? 1 : 0
        );
        $result = $this->collection->remove($criteria, $options);

        if ($result === true) {
            // Safe mode.
            return true;
        } else if (is_array($result)) {
            if (empty($result['ok']) || isset($result['err'])) {
                return false;
            } else if (empty($result['n'])) {
                // Nothing was removed.
                return false;
            }
            return true;
        }
        // Who knows?
        return false;
    }

    /**
     * Deletes several keys from the cache in a single action.
     *
     * @param array $keys The keys to delete
     * @return int The number of items successfully deleted.
     */
    public function delete_many(array $keys) {
        $count = 0;
        foreach ($keys as $key) {
            if ($this->delete($key)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Purges the cache deleting all items within it.
     *
     * @return boolean True on success. False otherwise.
     */
    public function purge() {
        if ($this->isready) {
            $this->collection->drop();
            $this->collection = $this->database->selectCollection($this->definitionhash);
        }

        return true;
    }

    /**
     * Takes the object from the add instance store and creates a configuration array that can be used to initialise an instance.
     *
     * @param stdClass $data
     * @return array
     */
    public static function config_get_configuration_array($data) {
        $return = array(
            'server' => $data->server,
            'database' => $data->database,
            'extendedmode' => (!empty($data->extendedmode))
        );
        if (!empty($data->username)) {
            $return['username'] = $data->username;
        }
        if (!empty($data->password)) {
            $return['password'] = $data->password;
        }
        if (!empty($data->replicaset)) {
            $return['replicaset'] = $data->replicaset;
        }
        if (!empty($data->usesafe)) {
            $return['usesafe'] = true;
            if (!empty($data->usesafevalue)) {
                $return['usesafe'] = (int)$data->usesafevalue;
                $return['usesafevalue'] = $return['usesafe'];
            }
        }
        return $return;
    }

    /**
     * Allows the cache store to set its data against the edit form before it is shown to the user.
     *
     * @param moodleform $editform
     * @param array $config
     */
    public static function config_set_edit_form_data(moodleform $editform, array $config) {
        $data = array();
        if (!empty($config['server'])) {
            $data['server'] = $config['server'];
        }
        if (!empty($config['database'])) {
            $data['database'] = $config['database'];
        }
        if (isset($config['extendedmode'])) {
            $data['extendedmode'] = (bool)$config['extendedmode'];
        }
        if (!empty($config['username'])) {
            $data['username'] = $config['username'];
        }
        if (!empty($config['password'])) {
            $data['password'] = $config['password'];
        }
        if (!empty($config['replicaset'])) {
            $data['replicaset'] = $config['replicaset'];
        }
        if (isset($config['usesafevalue'])) {
            $data['usesafe'] = true;
            $data['usesafevalue'] = (int)$data['usesafe'];
        } else if (isset($config['usesafe'])) {
            $data['usesafe'] = (bool)$config['usesafe'];
        }
        $editform->set_data($data);
    }

    /**
     * Performs any necessary clean up when the store instance is being deleted.
     */
    public function instance_deleted() {
        // We can't use purge here that acts upon a collection.
        // Instead we must drop the named database.
        if ($this->connection) {
            $connection = $this->connection;
        } else {
            try {
               $connection = new Mongo($this->server, $this->options);
            } catch (MongoConnectionException $e) {
                // We only want to catch MongoConnectionExceptions here.
                // If the server cannot be connected to we cannot clean it.
                return;
            }
        }
        $database = $connection->selectDB($this->databasename);
        $database->drop();
        $connection = null;
        $database = null;
        // Explicitly unset things to cause a close.
        $this->collection = null;
        $this->database = null;
        $this->connection = null;
    }

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * @param cache_definition $definition
     * @return false
     */
    public static function initialise_test_instance(cache_definition $definition) {
        if (!self::are_requirements_met()) {
            return false;
        }

        $config = get_config('cachestore_mongodb');
        if (empty($config->testserver)) {
            return false;
        }
        $configuration = array();
        $configuration['server'] = $config->testserver;
        if (!empty($config->testreplicaset)) {
            $configuration['replicaset'] = $config->testreplicaset;
        }
        if (!empty($config->testusername)) {
            $configuration['username'] = $config->testusername;
        }
        if (!empty($config->testpassword)) {
            $configuration['password'] = $config->testpassword;
        }
        if (!empty($config->testdatabase)) {
            $configuration['database'] = $config->testdatabase;
        }
        $configuration['usesafe'] = 1;
        if (!empty($config->testextendedmode)) {
            $configuration['extendedmode'] = (bool)$config->testextendedmode;
        }

        $store = new cachestore_mongodb('Test mongodb', $configuration);
        $store->initialise($definition);

        return $store;
    }

    /**
     * Returns the name of this instance.
     * @return string
     */
    public function my_name() {
        return $this->name;
    }
}
