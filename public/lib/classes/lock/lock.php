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
 * Class representing a lock
 *
 * The methods available for a specific lock type are only known by it's factory.
 *
 * @package   core
 * @category  lock
 * @copyright Damyon Wiese 2013
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lock {

    /** @var string|int $key A unique key representing a held lock */
    protected $key = '';

    /** @var lock_factory $factory The factory that generated this lock */
    protected $factory;

    /** @var bool $released Has this lock been released? If a lock falls out of scope without being released - show a warning. */
    protected $released;

    /** @var string $caller Where was this called from? Stored for when a warning is shown */
    protected $caller = 'unknown';

    /**
     * Construct a lock containing the unique key required to release it.
     * @param mixed $key - The lock key. The type of this is up to the lock_factory being used.
     *      For file locks this is a file handle. For MySQL this is a string.
     * @param lock_factory $factory - The factory that generated this lock.
     */
    public function __construct($key, $factory) {
        $this->factory = $factory;
        $this->key = $key;
        $this->released = false;

        // Track where the lock was raised, so we can report un-released locks in a helpful way.
        $this->caller = format_backtrace(debug_backtrace(), true);
    }

    /**
     * Sets the lock factory that owns a lock. This function should not be called under normal use.
     * It is intended only for cases like {@see timing_wrapper_lock_factory} where we wrap a lock
     * factory.
     *
     * When used, it should be called immediately after constructing the lock.
     *
     * @param lock_factory $factory New lock factory that owns this lock
     */
    public function init_factory(lock_factory $factory): void {
        $this->factory = $factory;
    }

    /**
     * Return the unique key representing this lock.
     * @return string|int lock key.
     */
    public function get_key() {
        return $this->key;
    }

    /**
     * Release this lock
     * @return bool
     */
    public function release() {
        $this->released = true;
        if (empty($this->factory)) {
            return false;
        }
        $result = $this->factory->release_lock($this);
        // Release any held references to the factory.
        unset($this->factory);
        $this->factory = null;
        $this->key = '';
        return $result;
    }

    /**
     * Release a lock if it has not already been released.
     *
     * @param bool $withexception If true, throw an exception if the lock has not been released.
     * @throws \coding_exception
     */
    public function release_if_not_released(bool $withexception = false): void {
        if (!$this->released) {
            $key = $this->key;

            $this->release();

            if ($withexception) {
                throw new \core\exception\coding_exception(<<<EOF
                    A lock was created but not released at: {$this->caller}
                    Code should look like:

                        \$factory = \core\lock\lock_config::get_lock_factory('type');
                        \$lock = \$factory->get_lock($key);
                        \$lock->release();  // Locks must ALWAYS be released like this.
                EOF);
            }
        }
    }

    /**
     * Print debugging if this lock falls out of scope before being released.
     */
    public function __destruct() {
        global $CFG;
        $this->release_if_not_released(defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST || $CFG->debugdeveloper);
    }
}
