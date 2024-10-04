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

use core_cache\configurable_cache_interface;
use core_cache\definition;
use core_cache\key_aware_cache_interface;
use core_cache\store;

/**
 * The APCu cache store class.
 *
 * @package    cachestore_apcu
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_apcu extends store implements configurable_cache_interface, key_aware_cache_interface {
    /**
     * The required version of APCu for this extension.
     */
    const REQUIRED_VERSION = '4.0.0';

    /**
     * The name of this store instance.
     * @var string
     */
    protected $name;

    /**
     * The definition used when this instance was initialised.
     * @var definition
     */
    protected $definition = null;

    /**
     * The storeprefix to use on all instances of this store.  Configured as part store setup.
     * @var string
     */
    protected $storeprefix = null;

    /**
     * The prefix added specifically for this cache.
     * @var string
     */
    protected $cacheprefix = null;

    /**
     * Static method to check that the APCu stores requirements have been met.
     *
     * It checks that the APCu extension has been loaded and that it has been enabled.
     *
     * @return bool True if the stores software/hardware requirements have been met and it can be used. False otherwise.
     */
    public static function are_requirements_met() {
        $enabled = ini_get('apc.enabled') && (php_sapi_name() != "cli" || ini_get('apc.enable_cli'));
        if (!extension_loaded('apcu') || !$enabled) {
            return false;
        }

        $version = phpversion('apcu');
        return $version && version_compare($version, self::REQUIRED_VERSION, '>=');
    }

    /**
     * Static method to check if a store is usable with the given mode.
     *
     * @param int $mode One of store::MODE_*
     * @return bool True if the mode is supported.
     */
    public static function is_supported_mode($mode) {
        return ($mode === self::MODE_APPLICATION || $mode === self::MODE_SESSION);
    }

    /**
     * Returns the supported features as a binary flag.
     *
     * @param array $configuration The configuration of a store to consider specifically.
     * @return int The supported features.
     */
    public static function get_supported_features(array $configuration = array()) {
        return self::SUPPORTS_NATIVE_TTL;
    }

    /**
     * Returns the supported modes as a binary flag.
     *
     * @param array $configuration The configuration of a store to consider specifically.
     * @return int The supported modes.
     */
    public static function get_supported_modes(array $configuration = array()) {
        return self::MODE_APPLICATION + self::MODE_SESSION;
    }

    /**
     * Constructs an instance of the cache store.
     *
     * This method should not create connections or perform and processing, it should be used
     *
     * @param string $name The name of the cache store
     * @param array $configuration The configuration for this store instance.
     */
    public function __construct($name, array $configuration = array()) {
        global $CFG;
        $this->name = $name;
        $this->storeprefix = $CFG->prefix;
        if (isset($configuration['prefix'])) {
            $this->storeprefix = $configuration['prefix'];
        }
    }

    /**
     * Returns the name of this store instance.
     * @return string
     */
    public function my_name() {
        return $this->name;
    }

    /**
     * Initialises a new instance of the cache store given the definition the instance is to be used for.
     *
     * This function should prepare any given connections etc.
     *
     * @param definition $definition
     * @return bool
     */
    public function initialise(definition $definition) {
        $this->definition = $definition;
        $this->cacheprefix = $this->storeprefix.$definition->generate_definition_hash().'__';
        return true;
    }

    /**
     * Returns true if this cache store instance has been initialised.
     * @return bool
     */
    public function is_initialised() {
        return ($this->definition !== null);
    }

    /**
     * Prepares the given key for use.
     *
     * Should be called before all interaction.
     *
     * @param string $key The key to prepare for storing in APCu.
     *
     * @return string
     */
    protected function prepare_key($key) {
        return $this->cacheprefix . $key;
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key) {
        $key = $this->prepare_key($key);
        $success = false;
        $outcome = apcu_fetch($key, $success);
        if ($success) {
            return $outcome;
        }
        return $success;
    }

    /**
     * Retrieves several items from the cache store in a single transaction.
     *
     * If not all of the items are available in the cache then the data value for those that are missing will be set to false.
     *
     * @param array $keys The array of keys to retrieve
     * @return array An array of items from the cache. There will be an item for each key, those that were not in the store will
     *      be set to false.
     */
    public function get_many($keys) {
        $map = array();
        foreach ($keys as $key) {
            $map[$key] = $this->prepare_key($key);
        }
        $outcomes = array();
        $success = false;
        $results = apcu_fetch($map, $success);
        if ($success) {
            foreach ($map as $key => $used) {
                if (array_key_exists($used, $results)) {
                    $outcomes[$key] = $results[$used];
                } else {
                    $outcomes[$key] = false;
                }
            }
        } else {
            $outcomes = array_fill_keys($keys, false);
        }
        return $outcomes;
    }

    /**
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data) {
        $key = $this->prepare_key($key);
        return apcu_store($key, $data, $this->definition->get_ttl());
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
        $map = array();
        foreach ($keyvaluearray as $pair) {
            $key = $this->prepare_key($pair['key']);
            $map[$key] = $pair['value'];
        }
        $result = apcu_store($map, null, $this->definition->get_ttl());
        return count($map) - count($result);
    }

    /**
     * Deletes an item from the cache store.
     *
     * @param string $key The key to delete.
     * @return bool Returns true if the operation was a success, false otherwise.
     */
    public function delete($key) {
        $key = $this->prepare_key($key);
        return apcu_delete($key);
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
        if (class_exists('APCUIterator', false)) {
            $iterator = new APCUIterator('#^' . preg_quote($this->cacheprefix, '#') . '#');
        } else {
            $iterator = new APCIterator('user', '#^' . preg_quote($this->cacheprefix, '#') . '#');
        }
        return apcu_delete($iterator);
    }

    /**
     * Performs any necessary clean up when the store instance is being deleted.
     */
    public function instance_deleted() {
        if (class_exists('APCUIterator', false)) {
            $iterator = new APCUIterator('#^' . preg_quote($this->storeprefix, '#') . '#');
        } else {
            $iterator = new APCIterator('user', '#^' . preg_quote($this->storeprefix, '#') . '#');
        }
        return apcu_delete($iterator);
    }

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * Returns an instance of the cache store, or false if one cannot be created.
     *
     * @param definition $definition
     * @return store
     */
    public static function initialise_test_instance(definition $definition) {
        $testperformance = get_config('cachestore_apcu', 'testperformance');
        if (empty($testperformance)) {
            return false;
        }
        if (!self::are_requirements_met()) {
            return false;
        }
        $name = 'APCu test';
        $cache = new cachestore_apcu($name);
        // No need to check if is_ready() as this has already being done by requirement check.
        $cache->initialise($definition);
        return $cache;
    }

    /**
     * Test is a cache has a key.
     *
     * @param string|int $key
     * @return bool True if the cache has the requested key, false otherwise.
     */
    public function has($key) {
        $key = $this->prepare_key($key);
        return apcu_exists($key);
    }

    /**
     * Test if a cache has at least one of the given keys.
     *
     * @param array $keys
     * @return bool True if the cache has at least one of the given keys
     */
    public function has_any(array $keys) {
        foreach ($keys as $arraykey => $key) {
            $keys[$arraykey] = $this->prepare_key($key);
        }
        $result = apcu_exists($keys);
        return count($result) > 0;
    }

    /**
     * Test is a cache has all of the given keys.
     *
     * @param array $keys
     * @return bool True if the cache has all of the given keys, false otherwise.
     */
    public function has_all(array $keys) {
        foreach ($keys as $arraykey => $key) {
            $keys[$arraykey] = $this->prepare_key($key);
        }
        $result = apcu_exists($keys);
        return count($result) === count($keys);
    }

    /**
     * Generates the appropriate configuration required for unit testing.
     *
     * @return array Array of unit test configuration data to be used by initialise().
     */
    public static function unit_test_configuration() {
        return array('prefix' => 'phpunit');
    }

    /**
     * Given the data from the add instance form this function creates a configuration array.
     *
     * @param stdClass $data
     * @return array
     */
    public static function config_get_configuration_array($data) {
        $config = array();

        if (isset($data->prefix)) {
            $config['prefix'] = $data->prefix;
        }
        return $config;
    }
    /**
     * Allows the cache store to set its data against the edit form before it is shown to the user.
     *
     * @param moodleform $editform
     * @param array $config
     */
    public static function config_set_edit_form_data(moodleform $editform, array $config) {
        if (isset($config['prefix'])) {
            $data['prefix'] = $config['prefix'];
        } else {
            $data['prefix'] = '';
        }
        $editform->set_data($data);
    }

    /**
     * Returns true if this cache store instance is both suitable for testing, and ready for testing.
     *
     * Cache stores that support being used as the default store for unit and acceptance testing should
     * override this function and return true if there requirements have been met.
     *
     * @return bool
     */
    public static function ready_to_be_used_for_testing() {
        return true;
    }
}
