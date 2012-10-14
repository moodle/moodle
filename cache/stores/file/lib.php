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
class cachestore_file implements cache_store, cache_is_key_aware {

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
                debugging('The given file cache store path is not writable. '.$path, DEBUG_DEVELOPER);
            }
        } else {
            $path = make_cache_directory('cachestore_file/'.preg_replace('#[^a-zA-Z0-9\.\-_]+#', '', $name));
        }
        $this->isready = $path !== false;
        $this->path = $path;
        $this->prescan = array_key_exists('prescan', $configuration) ? (bool)$configuration['prescan'] : false;
    }

    /**
     * Returns true if this store instance is ready to be used.
     * @return bool
     */
    public function is_ready() {
        return ($this->path !== null);
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
     * Returns true if the store instance supports multiple identifiers.
     *
     * @return bool
     */
    public function supports_multiple_indentifiers() {
        return false;
    }

    /**
     * Returns true if the store instance guarantees data.
     *
     * @return bool
     */
    public function supports_data_guarantee() {
        return true;
    }

    /**
     * Returns true if the store instance supports native ttl.
     *
     * @return bool
     */
    public function supports_native_ttl() {
        return true;
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
            $pattern = $this->path.'/*.cache';
            foreach (glob($pattern, GLOB_MARK | GLOB_NOSORT) as $filename) {
                $this->keys[basename($filename)] = filemtime($filename);
            }
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
        $file = $this->path.'/'.$filename;
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
        $file = $this->path.'/'.$filename;
        $result = @unlink($file);
        unset($this->keys[$filename]);
        return $result;
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
        $file = $this->path.'/'.$filename;
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
        $file = $this->path.'/'.$key.'.cache';
        $maxtime = cache::now() - $this->definition->get_ttl();
        if ($this->prescan) {
            return array_key_exists($filename, $this->keys) && $this->keys[$filename] >= $maxtime;
        }
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
        $pattern = $this->path.'/*.cache';
        foreach (glob($pattern, GLOB_MARK | GLOB_NOSORT) as $filename) {
            @unlink($filename);
        }
        $this->keys = array();
        return true;
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
     * Returns true if the user can add an instance of the store plugin.
     *
     * @return bool
     */
    public static function can_add_instance() {
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