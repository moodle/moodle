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

use core\progress\base;

/**
 * Lock utilities.
 *
 * @package   core
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lock_utils {
    /**
     * Start a progress bar and attempt to get a lock, updating the bar until a lock is achieved.
     *
     * This will make multiple attempts at getting the lock using a short timeout set by $progressupdatetime. After
     * each failed attempt, it will update the progress bar and try again, until $timeout is reached.
     *
     * @param lock_factory $lockfactory The factory to use to get the lock
     * @param string $resource The resource key we will try to get a lock on
     * @param base $progress The progress bar
     * @param int $timeout The maximum time in seconds to wait for a lock
     * @param string $message Optional message to display on the progress bar
     * @param int $progressupdatetime The number of seconds to wait for each lock attempt before updating the progress bar.
     * @param int $maxlifetime The maxlifetime to set on the lock, if supported.
     * @return lock|false A lock if successful, or false if the timeout expires.
     * @throws \coding_exception
     */
    public static function wait_for_lock_with_progress(
        lock_factory $lockfactory,
        string $resource,
        \core\progress\base $progress,
        int $timeout,
        string $message = '',
        int $progressupdatetime = 10,
        int $maxlifetime = DAYSECS,
    ) {
        if ($progressupdatetime < 1) {
            throw new \invalid_parameter_exception('Progress bar cannot update more than once per second. ' .
                '$progressupdate time must be at least 1.');
        }
        if ($progressupdatetime > $timeout) {
            throw new \invalid_parameter_exception('$timeout must be greater than $progressupdatetime.');
        }
        $lockattempts = 0;
        $maxattempts = $timeout / $progressupdatetime;
        $lock = false;
        $progress->start_progress($message);
        while (!$lock && $lockattempts < $maxattempts) {
            $lock = $lockfactory->get_lock($resource, $progressupdatetime, $maxlifetime);
            if (!$lock) {
                $progress->progress();
                $lockattempts++;
            }
        }
        $progress->end_progress();
        return $lock;
    }
}
