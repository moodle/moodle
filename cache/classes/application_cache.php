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
use core\exception\moodle_exception;

/**
 * An application cache.
 *
 * This class is used for application caches returned by the cache::make methods.
 * On top of the standard functionality it also allows locking to be required and or manually operated.
 *
 * This cache class should never be interacted with directly. Instead you should always use the cache::make methods.
 * It is technically possible to call those methods through this class however there is no guarantee that you will get an
 * instance of this class back again.
 *
 * @internal don't use me directly.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class application_cache extends cache implements loader_with_locking_interface {
    /**
     * Lock identifier.
     * This is used to ensure the lock belongs to the cache instance + definition + user.
     * @var string
     */
    protected $lockidentifier;

    /**
     * Gets set to true if the cache's primary store natively supports locking.
     * If it does then we use that, otherwise we need to instantiate a second store to use for locking.
     * @var store
     */
    protected $nativelocking = null;

    /**
     * Gets set to true if the cache is going to be using locking.
     * This isn't a requirement, it doesn't need to use locking (most won't) and this bool is used to quickly check things.
     * If required then locking will be forced for the get|set|delete operation.
     * @var bool
     */
    protected $requirelocking = false;

    /**
     * Gets set to true if the cache writes (set|delete) must have a manual lock created first
     * @var bool
     */
    protected $requirelockingbeforewrite = false;

    /**
     * Gets set to a store to use for locking if the caches primary store doesn't support locking natively.
     * @var lockable_cache_interface
     */
    protected $cachelockinstance;

    /**
     * Store a list of locks acquired by this process.
     * @var array
     */
    protected $locks;

    /**
     * Overrides the cache construct method.
     *
     * You should not call this method from your code, instead you should use the cache::make methods.
     *
     * @param definition $definition
     * @param store $store
     * @param loader_interface|data_source_interface $loader
     */
    public function __construct(definition $definition, store $store, $loader = null) {
        parent::__construct($definition, $store, $loader);
        $this->nativelocking = $this->store_supports_native_locking();
        if ($definition->require_locking()) {
            $this->requirelocking = true;
            $this->requirelockingbeforewrite = $definition->require_locking_before_write();
        }

        $this->handle_invalidation_events();
    }

    /**
     * Returns the identifier to use
     *
     * @staticvar int $instances Counts the number of instances. Used as part of the lock identifier.
     * @return string
     */
    public function get_identifier() {
        static $instances = 0;
        if ($this->lockidentifier === null) {
            $this->lockidentifier = md5(
                $this->get_definition()->generate_definition_hash() .
                sesskey() .
                $instances++ .
                application_cache::class,
            );
        }
        return $this->lockidentifier;
    }

    /**
     * Fixes the instance up after a clone.
     */
    public function __clone() {
        // Force a new idenfitier.
        $this->lockidentifier = null;
    }

    /**
     * Acquires a lock on the given key.
     *
     * This is done automatically if the definition requires it.
     * It is recommended to use a definition if you want to have locking although it is possible to do locking without having
     * it required by the definition.
     * The problem with such an approach is that you cannot ensure that code will consistently use locking. You will need to
     * rely on the integrators review skills.
     *
     * @param string|int $key The key as given to get|set|delete
     * @return bool Always returns true
     * @throws moodle_exception If the lock cannot be obtained
     */
    public function acquire_lock($key) {
        $releaseparent = false;
        try {
            if ($this->get_loader() !== false) {
                $this->get_loader()->acquire_lock($key);
                // We need to release this lock later if the lock is not successful.
                $releaseparent = true;
            }
            $hashedkey = helper::hash_key($key, $this->get_definition());
            $before = microtime(true);
            if ($this->nativelocking) {
                $lock = $this->get_store()->acquire_lock($hashedkey, $this->get_identifier());
            } else {
                $this->ensure_cachelock_available();
                $lock = $this->cachelockinstance->lock($hashedkey, $this->get_identifier());
            }
            $after = microtime(true);
            if ($lock) {
                $this->locks[$hashedkey] = $lock;
                if (MDL_PERF || $this->perfdebug) {
                    \core\lock\timing_wrapper_lock_factory::record_lock_data(
                        $after,
                        $before,
                        $this->get_definition()->get_id(),
                        $hashedkey,
                        $lock,
                        $this->get_identifier() . $hashedkey
                    );
                }
                $releaseparent = false;
                return true;
            } else {
                throw new moodle_exception(
                    'ex_unabletolock',
                    'cache',
                    '',
                    null,
                    'store: ' . get_class($this->get_store()) . ', lock: ' . $hashedkey
                );
            }
        } finally {
            // Release the parent lock if we acquired it, then threw an exception.
            if ($releaseparent) {
                $this->get_loader()->release_lock($key);
            }
        }
    }

    /**
     * Checks if this cache has a lock on the given key.
     *
     * @param string|int $key The key as given to get|set|delete
     * @return bool|null Returns true if there is a lock and this cache has it, null if no one has a lock on that key, false if
     *      someone else has the lock.
     */
    public function check_lock_state($key) {
        $key = helper::hash_key($key, $this->get_definition());
        if (!empty($this->locks[$key])) {
            return true; // Shortcut to save having to make a call to the cache store if the lock is held by this process.
        }
        if ($this->nativelocking) {
            return $this->get_store()->check_lock_state($key, $this->get_identifier());
        } else {
            $this->ensure_cachelock_available();
            return $this->cachelockinstance->check_state($key, $this->get_identifier());
        }
    }

    /**
     * Releases the lock this cache has on the given key
     *
     * @param string|int $key
     * @return bool True if the operation succeeded, false otherwise.
     */
    public function release_lock($key) {
        $loaderkey = $key;
        $key = helper::hash_key($key, $this->get_definition());
        if ($this->nativelocking) {
            $released = $this->get_store()->release_lock($key, $this->get_identifier());
        } else {
            $this->ensure_cachelock_available();
            $released = $this->cachelockinstance->unlock($key, $this->get_identifier());
        }
        if ($released && array_key_exists($key, $this->locks)) {
            unset($this->locks[$key]);
            if (MDL_PERF || $this->perfdebug) {
                \core\lock\timing_wrapper_lock_factory::record_lock_released_data($this->get_identifier() . $key);
            }
        }
        if ($this->get_loader() !== false) {
            $this->get_loader()->release_lock($loaderkey);
        }
        return $released;
    }

    /**
     * Ensure that the dedicated lock store is ready to go.
     *
     * This should only happen if the cache store doesn't natively support it.
     */
    protected function ensure_cachelock_available() {
        if ($this->cachelockinstance === null) {
            $this->cachelockinstance = helper::get_cachelock_for_store($this->get_store());
        }
    }

    /**
     * Sends a key => value pair to the cache.
     *
     * <code>
     * // This code will add four entries to the cache, one for each url.
     * $cache->set('main', 'http://moodle.org');
     * $cache->set('docs', 'http://docs.moodle.org');
     * $cache->set('tracker', 'http://tracker.moodle.org');
     * $cache->set('qa', 'http://qa.moodle.net');
     * </code>
     *
     * @param string|int $key The key for the data being requested.
     * @param int $version Version number
     * @param mixed $data The data to set against the key.
     * @param bool $setparents If true, sets all parent loaders, otherwise only this one
     * @return bool True on success, false otherwise.
     * @throws coding_exception If a required lock has not beeen acquired
     */
    protected function set_implementation($key, int $version, $data, bool $setparents = true): bool {
        if ($this->requirelockingbeforewrite && !$this->check_lock_state($key)) {
            throw new coding_exception('Attempted to set cache key "' . $key . '" without a lock. '
                . 'Locking before writes is required for ' . $this->get_definition()->get_id());
        }
        return parent::set_implementation($key, $version, $data, $setparents);
    }

    /**
     * Sends several key => value pairs to the cache.
     *
     * Using this function comes with potential performance implications.
     * Not all cache stores will support get_many/set_many operations and in order to replicate this functionality will call
     * the equivalent singular method for each item provided.
     * This should not deter you from using this function as there is a performance benefit in situations where the cache store
     * does support it, but you should be aware of this fact.
     *
     * <code>
     * // This code will add four entries to the cache, one for each url.
     * $cache->set_many(array(
     *     'main' => 'http://moodle.org',
     *     'docs' => 'http://docs.moodle.org',
     *     'tracker' => 'http://tracker.moodle.org',
     *     'qa' => ''http://qa.moodle.net'
     * ));
     * </code>
     *
     * @param array $keyvaluearray An array of key => value pairs to send to the cache.
     * @return int The number of items successfully set. It is up to the developer to check this matches the number of items.
     *      ... if they care that is.
     * @throws coding_exception If a required lock has not beeen acquired
     */
    public function set_many(array $keyvaluearray) {
        if ($this->requirelockingbeforewrite) {
            foreach ($keyvaluearray as $key => $value) {
                if (!$this->check_lock_state($key)) {
                    throw new coding_exception('Attempted to set cache key "' . $key . '" without a lock. '
                            . 'Locking before writes is required for ' . $this->get_definition()->get_id());
                }
            }
        }
        return parent::set_many($keyvaluearray);
    }

    /**
     * Delete the given key from the cache.
     *
     * @param string|int $key The key to delete.
     * @param bool $recurse When set to true the key will also be deleted from all stacked cache loaders and their stores.
     *     This happens by default and ensure that all the caches are consistent. It is NOT recommended to change this.
     * @return bool True of success, false otherwise.
     * @throws coding_exception If a required lock has not beeen acquired
     */
    public function delete($key, $recurse = true) {
        if ($this->requirelockingbeforewrite && !$this->check_lock_state($key)) {
            throw new coding_exception('Attempted to delete cache key "' . $key . '" without a lock. '
                    . 'Locking before writes is required for ' . $this->get_definition()->get_id());
        }
        return parent::delete($key, $recurse);
    }

    /**
     * Delete all of the given keys from the cache.
     *
     * @param array $keys The key to delete.
     * @param bool $recurse When set to true the key will also be deleted from all stacked cache loaders and their stores.
     *     This happens by default and ensure that all the caches are consistent. It is NOT recommended to change this.
     * @return int The number of items successfully deleted.
     * @throws coding_exception If a required lock has not beeen acquired
     */
    public function delete_many(array $keys, $recurse = true) {
        if ($this->requirelockingbeforewrite) {
            foreach ($keys as $key) {
                if (!$this->check_lock_state($key)) {
                    throw new coding_exception('Attempted to delete cache key "' . $key . '" without a lock. '
                            . 'Locking before writes is required for ' . $this->get_definition()->get_id());
                }
            }
        }
        return parent::delete_many($keys, $recurse);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(application_cache::class, \cache_application::class);
