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
 * The library file for the file cache store.
 *
 * This file is part of the file cache store, it contains the API for interacting with an instance of the store.
 * This is used as a default cache store within the Cache API. It should never be deleted.
 *
 * @package    cachestore_file
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The file store class.
 *
 * Configuration options
 *      path:           string: path to the cache directory, if left empty one will be created in the cache directory
 *      autocreate:     true, false
 *      prescan:        true, false
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_file extends cache_store implements cache_is_key_aware, cache_is_configurable  {

    /**
     * The name of the store.
     * @var string
     */
    protected $name;

    /**
     * The path to use for the file storage.
     * @var string
     */
    protected $path = null;

    /**
     * Set to true when a prescan has been performed.
     * @var bool
     */
    protected $prescan = false;

    /**
     * Set to true if we should store files within a single directory.
     * By default we use a nested structure in order to reduce the chance of conflicts and avoid any file system
     * limitations such as maximum files per directory.
     * @var bool
     */
    protected $singledirectory = false;

    /**
     * Set to true when the path should be automatically created if it does not yet exist.
     * @var bool
     */
    protected $autocreate = false;

    /**
     * Set to true if a custom path is being used.
     * @var bool
     */
    protected $custompath = false;

    /**
     * An array of keys we are sure about presently.
     * @var array
     */
    protected $keys = array();

    /**
     * True when the store is ready to be initialised.
     * @var bool
     */
    protected $isready = false;

    /**
     * The cache definition this instance has been initialised with.
     * @var cache_definition
     */
    protected $definition;

    /**
     * Constructs the store instance.
     *
     * Noting that this function is not an initialisation. It is used to prepare the store for use.
     * The store will be initialised when required and will be provided with a cache_definition at that time.
     *
     * @param string $name
     * @param array $configuration
     */
    public function __construct($name, array $configuration = array()) {
        $this->name = $name;
        if (array_key_exists('path', $configuration) && $configuration['path'] !== '') {
            $this->custompath = true;
            $this->autocreate = !empty($configuration['autocreate']);
            $path = (string)$configuration['path'];
            if (!is_dir($path)) {
                if ($this->autocreate) {
                    if (!make_writable_directory($path, false)) {
                        $path = false;
                        debugging('Error trying to autocreate file store path. '.$path, DEBUG_DEVELOPER);
                    }
                } else {
                    $path = false;
                    debugging('The given file cache store path does not exist. '.$path, DEBUG_DEVELOPER);
                }
            }
            if ($path !== false && !is_writable($path)) {
                $path = false;
                debugging('The file cache store path is not writable for `'.$name.'`', DEBUG_DEVELOPER);
            }
        } else {
            $path = make_cache_directory('cachestore_file/'.preg_replace('#[^a-zA-Z0-9\.\-_]+#', '', $name));
        }
        $this->isready = $path !== false;
        $this->path = $path;
        // Check if we should prescan the directory.
        if (array_key_exists('prescan', $configuration)) {
            $this->prescan = (bool)$configuration['prescan'];
        } else {
            // Default is no, we should not prescan.
            $this->prescan = false;
        }
        // Check if we should be storing in a single directory.
        if (array_key_exists('singledirectory', $configuration)) {
            $this->singledirectory = (bool)$configuration['singledirectory'];
        } else {
            // Default: No, we will use multiple directories.
            $this->singledirectory = false;
        }
    }

    /**
     * Returns true if this store instance is ready to be used.
     * @return bool
     */
    public function is_ready() {
        return $this->isready;
    }

    /**
     * Returns true once this instance has been initialised.
     *
     * @return bool
     */
    public function is_initialised() {
        return true;
    }

    /**
     * Returns the supported features as a combined int.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_features(array $configuration = array()) {
        $supported = self::SUPPORTS_DATA_GUARANTEE +
                     self::SUPPORTS_NATIVE_TTL;
        return $supported;
    }

    /**
     * Returns the supported modes as a combined int.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_modes(array $configuration = array()) {
        return self::MODE_APPLICATION + self::MODE_SESSION;
    }

    /**
     * Returns true if the store requirements are met.
     *
     * @return bool
     */
    public static function are_requirements_met() {
        return true;
    }

    /**
     * Returns true if the given mode is supported by this store.
     *
     * @param int $mode One of cache_store::MODE_*
     * @return bool
     */
    public static function is_supported_mode($mode) {
        return ($mode === self::MODE_APPLICATION || $mode === self::MODE_SESSION);
    }

    /**
     * Initialises the cache.
     *
     * Once this has been done the cache is all set to be used.
     *
     * @param cache_definition $definition
     */
    public function initialise(cache_definition $definition) {
        $this->definition = $definition;
        $hash = preg_replace('#[^a-zA-Z0-9]+#', '_', $this->definition->get_id());
        $this->path .= '/'.$hash;
        make_writable_directory($this->path);
        if ($this->prescan && $definition->get_mode() !== self::MODE_REQUEST) {
            $this->prescan = false;
        }
        if ($this->prescan) {
            $this->prescan_keys();
        }
    }

    /**
     * Pre-scan the cache to see which keys are present.
     */
    protected function prescan_keys() {
        $files = glob($this->glob_keys_pattern(), GLOB_MARK | GLOB_NOSORT);
        if (is_array($files)) {
            foreach ($files as $filename) {
                $this->keys[basename($filename)] = filemtime($filename);
            }
        }
    }

    /**
     * Gets a pattern suitable for use with glob to find all keys in the cache.
     * @return string The pattern.
     */
    protected function glob_keys_pattern() {
        if ($this->singledirectory) {
            return $this->path . '/*.cache';
        } else {
            return $this->path . '/*/*.cache';
        }
    }

    /**
     * Returns the file path to use for the given key.
     *
     * @param string $key The key to generate a file path for.
     * @param bool $create If set to the true the directory structure the key requires will be created.
     * @return string The full path to the file that stores a particular cache key.
     */
    protected function file_path_for_key($key, $create = false) {
        if ($this->singledirectory) {
            // Its a single directory, easy, just the store instances path + the file name.
            return $this->path . '/' . $key . '.cache';
        } else {
            // We are using a single subdirectory to achieve 1 level.
            $subdir = substr($key, 0, 3);
            $dir = $this->path . '/' . $subdir;
            if ($create) {
                // Create the directory. This function does it recursivily!
                make_writable_directory($dir);
            }
            return $dir . '/' . $key . '.cache';
        }
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key) {
        $filename = $key.'.cache';
        $file = $this->file_path_for_key($key);
        $ttl = $this->definition->get_ttl();
        if ($ttl) {
            $maxtime = cache::now() - $ttl;
        }
        $readfile = false;
        if ($this->prescan && array_key_exists($key, $this->keys)) {
            if (!$ttl || $this->keys[$filename] >= $maxtime && file_exists($file)) {
                $readfile = true;
            } else {
                $this->delete($key);
            }
        } else if (file_exists($file) && (!$ttl || filemtime($file) >= $maxtime)) {
            $readfile = true;
        }
        if (!$readfile) {
            return false;
        }
        // Check the filesize first, likely not needed but important none the less.
        $filesize = filesize($file);
        if (!$filesize) {
            return false;
        }
        // Open ensuring the file for writing, truncating it and setting the pointer to the start.
        if (!$handle = fopen($file, 'rb')) {
            return false;
        }
        // Lock it up!
        // We don't care if this succeeds or not, on some systems it will, on some it won't, meah either way.
        flock($handle, LOCK_SH);
        // HACK ALERT
        // There is a problem when reading from the file during PHPUNIT tests. For one reason or another the filesize is not correct
        // Doesn't happen during normal operation, just during unit tests.
        // Read it.
        $data = fread($handle, $filesize+128);
        // Unlock it.
        flock($handle, LOCK_UN);
        // Return it unserialised.
        return $this->prep_data_after_read($data);
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
        $result = array();
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }

    /**
     * Deletes an item from the cache store.
     *
     * @param string $key The key to delete.
     * @return bool Returns true if the operation was a success, false otherwise.
     */
    public function delete($key) {
        $filename = $key.'.cache';
        $file = $this->file_path_for_key($key);

        if (@unlink($file)) {
            unset($this->keys[$filename]);
            return true;
        }

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
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data) {
        $this->ensure_path_exists();
        $filename = $key.'.cache';
        $file = $this->file_path_for_key($key, true);
        $result = $this->write_file($file, $this->prep_data_before_save($data));
        if (!$result) {
            // Couldn't write the file.
            return false;
        }
        // Record the key if required.
        if ($this->prescan) {
            $this->keys[$filename] = cache::now() + 1;
        }
        // Return true.. it all worked **miracles**.
        return true;
    }

    /**
     * Prepares data to be stored in a file.
     *
     * @param mixed $data
     * @return string
     */
    protected function prep_data_before_save($data) {
        return serialize($data);
    }

    /**
     * Prepares the data it has been read from the cache. Undoing what was done in prep_data_before_save.
     *
     * @param string $data
     * @return mixed
     * @throws coding_exception
     */
    protected function prep_data_after_read($data) {
        $result = @unserialize($data);
        if ($result === false) {
            throw new coding_exception('Failed to unserialise data from file. Either failed to read, or failed to write.');
        }
        return $result;
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
            if ($this->set($pair['key'], $pair['value'])) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Checks if the store has a record for the given key and returns true if so.
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        $filename = $key.'.cache';
        $maxtime = cache::now() - $this->definition->get_ttl();
        if ($this->prescan) {
            return array_key_exists($filename, $this->keys) && $this->keys[$filename] >= $maxtime;
        }
        $file = $this->file_path_for_key($key);
        return (file_exists($file) && ($this->definition->get_ttl() == 0 || filemtime($file) >= $maxtime));
    }

    /**
     * Returns true if the store contains records for all of the given keys.
     *
     * @param array $keys
     * @return bool
     */
    public function has_all(array $keys) {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns true if the store contains records for any of the given keys.
     *
     * @param array $keys
     * @return bool
     */
    public function has_any(array $keys) {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Purges the cache deleting all items within it.
     *
     * @return boolean True on success. False otherwise.
     */
    public function purge() {
        $files = glob($this->glob_keys_pattern(), GLOB_MARK | GLOB_NOSORT);
        if (is_array($files)) {
            foreach ($files as $filename) {
                @unlink($filename);
            }
        }
        $this->keys = array();
        return true;
    }

    /**
     * Given the data from the add instance form this function creates a configuration array.
     *
     * @param stdClass $data
     * @return array
     */
    public static function config_get_configuration_array($data) {
        $config = array();

        if (isset($data->path)) {
            $config['path'] = $data->path;
        }
        if (isset($data->autocreate)) {
            $config['autocreate'] = $data->autocreate;
        }
        if (isset($data->singledirectory)) {
            $config['singledirectory'] = $data->singledirectory;
        }
        if (isset($data->prescan)) {
            $config['prescan'] = $data->prescan;
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
        $data = array();
        if (!empty($config['path'])) {
            $data['path'] = $config['path'];
        }
        if (isset($config['autocreate'])) {
            $data['autocreate'] = (bool)$config['autocreate'];
        }
        if (isset($config['singledirectory'])) {
            $data['singledirectory'] = (bool)$config['singledirectory'];
        }
        if (isset($config['prescan'])) {
            $data['prescan'] = (bool)$config['prescan'];
        }
        $editform->set_data($data);
    }

    /**
     * Checks to make sure that the path for the file cache exists.
     *
     * @return bool
     * @throws coding_exception
     */
    protected function ensure_path_exists() {
        if (!is_writable($this->path)) {
            if ($this->custompath && !$this->autocreate) {
                throw new coding_exception('File store path does not exist. It must exist and be writable by the web server.');
            }
            if (!make_writable_directory($this->path, false)) {
                throw new coding_exception('File store path does not exist and can not be created.');
            }
        }
        return true;
    }

    /**
     * Performs any necessary clean up when the store instance is being deleted.
     *
     * 1. Purges the cache directory.
     * 2. Deletes the directory we created for this cache instances data.
     */
    public function cleanup() {
        $this->purge();
        @rmdir($this->path);
    }

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * Returns an instance of the cache store, or false if one cannot be created.
     *
     * @param cache_definition $definition
     * @return cachestore_file
     */
    public static function initialise_test_instance(cache_definition $definition) {
        $name = 'File test';
        $path = make_cache_directory('cachestore_file_test');
        $cache = new cachestore_file($name, array('path' => $path));
        $cache->initialise($definition);
        return $cache;
    }

    /**
     * Writes your madness to a file.
     *
     * There are several things going on in this function to try to ensure what we don't end up with partial writes etc.
     *   1. Files for writing are opened with the mode xb, the file must be created and can not already exist.
     *   2. Renaming, data is written to a temporary file, where it can be verified using md5 and is then renamed.
     *
     * @param string $file Absolute file path
     * @param string $content The content to write.
     * @return bool
     */
    protected function write_file($file, $content) {
        // Generate a temp file that is going to be unique. We'll rename it at the end to the desired file name.
        // in this way we avoid partial writes.
        $path = dirname($file);
        while (true) {
            $tempfile = $path.'/'.uniqid(sesskey().'.', true) . '.temp';
            if (!file_exists($tempfile)) {
                break;
            }
        }

        // Open the file with mode=x. This acts to create and open the file for writing only.
        // If the file already exists this will return false.
        // We also force binary.
        $handle = @fopen($tempfile, 'xb+');
        if ($handle === false) {
            // File already exists... lock already exists, return false.
            return false;
        }
        fwrite($handle, $content);
        fflush($handle);
        // Close the handle, we're done.
        fclose($handle);

        if (md5_file($tempfile) !== md5($content)) {
            // The md5 of the content of the file must match the md5 of the content given to be written.
            @unlink($tempfile);
            return false;
        }

        // Finally rename the temp file to the desired file, returning the true|false result.
        $result = rename($tempfile, $file);
        if (!$result) {
            // Failed to rename, don't leave files lying around.
            @unlink($tempfile);
        }
        return $result;
    }

    /**
     * Returns the name of this instance.
     * @return string
     */
    public function my_name() {
        return $this->name;
    }
}
