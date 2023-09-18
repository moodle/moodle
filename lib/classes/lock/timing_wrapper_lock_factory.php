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

namespace core\lock;

use coding_exception;

/**
 * Timing wrapper around a lock factory.
 *
 * This passes all calls through to the underlying lock factory, but adds timing information on how
 * long it takes to get a lock and how long the lock is held for.
 *
 * @package core
 * @category lock
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class timing_wrapper_lock_factory implements lock_factory {

    /** @var lock_factory Real lock factory */
    protected $factory;

    /** @var string Type (Frankenstyle) used for these locks */
    protected $type;

    /**
     * Constructor required by interface.
     *
     * @param string $type Type (should be same as passed to real lock factory)
     * @param lock_factory $factory Real lock factory
     */
    public function __construct($type, lock_factory $factory = null) {
        $this->type = $type;
        if (!$factory) {
            // This parameter has to be optional because of the interface, but it is actually
            // required.
            throw new \coding_exception('The $factory parameter must be specified');
        }
        $this->factory = $factory;
    }

    /**
     * Gets the real lock factory that this is wrapping.
     *
     * @return lock_factory ReaL lock factory
     */
    public function get_real_factory(): lock_factory {
        return $this->factory;
    }

    /**
     * Implementation of lock_factory::get_lock that defers to function inner_get_lock and keeps
     * track of how long it took.
     *
     * @param string $resource Identifier for the lock
     * @param int $timeout Number of seconds to wait for a lock before giving up
     * @param int $maxlifetime Number of seconds to wait before reclaiming a stale lock
     * @return \core\lock\lock|boolean - An instance of \core\lock\lock if the lock was obtained, or false.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {
        $before = microtime(true);

        $result = $this->factory->get_lock($resource, $timeout, $maxlifetime);

        $after = microtime(true);
        self::record_lock_data($after, $before, $this->type, $resource, (bool)$result, $result);
        if ($result) {
            $result->init_factory($this);
        }

        return $result;
    }

    /**
     * Records statistics about a lock to the performance data.
     *
     * @param float $after The time after the lock was achieved.
     * @param float $before The time before the lock was requested.
     * @param string $type The type of lock.
     * @param string $resource The resource being locked.
     * @param bool $result Whether the lock was successful.
     * @param lock|string $lock A value uniquely identifying the lock.
     * @return void
     */
    public static function record_lock_data(float $after, float $before, string $type, string $resource, bool $result, $lock) {
        global $PERF;
        $duration = $after - $before;
        if (empty($PERF->locks)) {
            $PERF->locks = [];
        }
        $lockdata = (object) [
            'type' => $type,
            'resource' => $resource,
            'wait' => $duration,
            'success' => $result
        ];
        if ($result) {
            $lockdata->lock = $lock;
            $lockdata->timestart = $after;
        }
        $PERF->locks[] = $lockdata;
    }

    /**
     * Release a lock that was previously obtained with {@see get_lock}.
     *
     * @param lock $lock - The lock to release.
     * @return boolean - True if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        self::record_lock_released_data($lock);
        return $this->factory->release_lock($lock);
    }

    /**
     * Find the lock in the performance info and update it with the time held.
     *
     * @param lock|string $lock A value uniquely identifying the lock.
     * @return void
     */
    public static function record_lock_released_data($lock) {
        global $PERF;

        // Find this lock in the list of locks we got, looking backwards since it is probably
        // the last one.
        for ($index = count($PERF->locks) - 1; $index >= 0; $index--) {
            $lockdata = $PERF->locks[$index];
            if (!empty($lockdata->lock) && $lockdata->lock === $lock) {
                // Update the time held.
                unset($lockdata->lock);
                $lockdata->held = microtime(true) - $lockdata->timestart;
                break;
            }
        }
    }

    /**
     * Calls parent factory to check if it supports timeout.
     *
     * @return boolean False if attempting to get a lock will block indefinitely.
     */
    public function supports_timeout() {
        return $this->factory->supports_timeout();
    }

    /**
     * Calls parent factory to check if it auto-releases locks.
     *
     * @return boolean True if this lock type will be automatically released when the current process ends.
     */
    public function supports_auto_release() {
        return $this->factory->supports_auto_release();
    }

    /**
     * @deprecated since Moodle 3.10.
     */
    public function supports_recursion() {
        throw new coding_exception('The function supports_recursion() has been removed, please do not use it anymore.');
    }

    /**
     * Calls parent factory to check if it is available.
     *
     * @return boolean True if this lock type is available in this environment.
     */
    public function is_available() {
        return $this->factory->is_available();
    }

    /**
     * @deprecated since Moodle 3.10.
     */
    public function extend_lock() {
        throw new coding_exception('The function extend_lock() has been removed, please do not use it anymore.');
    }
}
