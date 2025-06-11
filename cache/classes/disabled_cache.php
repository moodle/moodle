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

namespace core_cache;

use core\exception\coding_exception;

/**
 * The cache loader class used when the Cache has been disabled.
 *
 * @package core_cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class disabled_cache extends cache implements loader_with_locking_interface {
    /**
     * Constructs the cache.
     *
     * @param definition $definition
     * @param store $store
     * @param null $loader Unused.
     */
    public function __construct(definition $definition, store $store, $loader = null) {
        if ($loader instanceof data_source_interface) {
            // Set the data source to allow data sources to work when caching is entirely disabled.
            $this->set_data_source($loader);
        }

        // No other features are handled.
    }

    /**
     * Gets a key from the cache.
     *
     * @param int|string $key
     * @param int $requiredversion Minimum required version of the data or cache::VERSION_NONE
     * @param int $strictness Unused.
     * @param mixed &$actualversion If specified, will be set to the actual version number retrieved
     * @return bool
     */
    protected function get_implementation($key, int $requiredversion, int $strictness, &$actualversion = null) {
        $datasource = $this->get_datasource();
        if ($datasource !== false) {
            if ($requiredversion === cache::VERSION_NONE) {
                return $datasource->load_for_cache($key);
            } else {
                if (!$datasource instanceof versionable_data_source_interface) {
                    throw new coding_exception('Data source is not versionable');
                }
                $result = $datasource->load_for_cache_versioned($key, $requiredversion, $actualversion);
                if ($result && $actualversion < $requiredversion) {
                    throw new coding_exception('Data source returned outdated version');
                }
                return $result;
            }
        }
        return false;
    }

    /**
     * Gets many keys at once from the cache.
     *
     * @param array $keys
     * @param int $strictness Unused.
     * @return array
     */
    public function get_many(array $keys, $strictness = IGNORE_MISSING) {
        if ($this->get_datasource() !== false) {
            return $this->get_datasource()->load_many_for_cache($keys);
        }

        return array_combine($keys, array_fill(0, count($keys), false));
    }

    /**
     * Sets a key value pair in the cache.
     *
     * @param int|string $key Unused.
     * @param int $version Unused.
     * @param mixed $data Unused.
     * @param bool $setparents Unused.
     * @return bool
     */
    protected function set_implementation($key, int $version, $data, bool $setparents = true): bool {
        return false;
    }

    /**
     * Sets many key value pairs in the cache at once.
     *
     * @param array $keyvaluearray Unused.
     * @return int
     */
    public function set_many(array $keyvaluearray) {
        return 0;
    }

    /**
     * Deletes an item from the cache.
     *
     * @param int|string $key Unused.
     * @param bool $recurse Unused.
     * @return bool
     */
    public function delete($key, $recurse = true) {
        return false;
    }

    /**
     * Deletes many items at once from the cache.
     *
     * @param array $keys Unused.
     * @param bool $recurse Unused.
     * @return int
     */
    public function delete_many(array $keys, $recurse = true) {
        return 0;
    }

    /**
     * Checks if the cache has the requested key.
     *
     * @param int|string $key Unused.
     * @param bool $tryloadifpossible Unused.
     * @return bool
     */
    public function has($key, $tryloadifpossible = false) {
        $result = $this->get($key);

        return $result !== false;
    }

    /**
     * Checks if the cache has all of the requested keys.
     * @param array $keys Unused.
     * @return bool
     */
    public function has_all(array $keys) {
        if (!$this->get_datasource()) {
            return false;
        }

        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the cache has any of the requested keys.
     *
     * @param array $keys Unused.
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
     * Purges all items from the cache.
     *
     * @return bool
     */
    public function purge() {
        return true;
    }

    /**
     * Pretend that we got a lock to avoid errors.
     *
     * @param int|string $key
     * @return bool
     */
    public function acquire_lock($key): bool {
        return true;
    }

    /**
     * Pretend that we released a lock to avoid errors.
     *
     * @param int|string $key
     * @return bool
     */
    public function release_lock($key): bool {
        return true;
    }

    /**
     * Pretend that we have a lock to avoid errors.
     *
     * @param int|string $key
     * @return bool
     */
    public function check_lock_state($key): bool {
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(disabled_cache::class, \cache_disabled::class);
