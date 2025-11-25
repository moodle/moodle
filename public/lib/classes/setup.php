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

namespace core;

use core\exception\moodle_exception;

/**
 * Core setup functionality for Moodle.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setup {
    /**
     * Check the validity of the wwwroot, throwing a Moodle exception if invalid.
     *
     * @throws moodle_exception
     * @return true
     */
    public function validate_wwwroot(): bool {
        if ($this->does_wwwroot_end_in_slash()) {
            // The wwwroot should not end in a slash as this may suggest a misconfiguration.
            throw new moodle_exception('wwwrootslash', 'error');
        }

        if ($this->does_wwwroot_end_in_public()) {
            // The wwwroot should not end in /public as this may suggest a misconfiguration.
            // There may be legitimate sites out there that currently do this but it is not recommended.
            throw new moodle_exception('wwwrootpublic', 'error');
        }

        return true;
    }

    /**
     * Detect whether the wwwroot ends in /public.
     *
     * @return bool
     */
    protected function does_wwwroot_end_in_public(): bool {
        global $CFG;

        return substr($CFG->wwwroot, -7) == '/public';
    }

    /**
     * Detect whether the wwwroot ends in /.
     *
     * @return bool
     */
    protected function does_wwwroot_end_in_slash(): bool {
        global $CFG;

        return str_ends_with($CFG->wwwroot, '/');
    }

    /**
     * Check whether an upgrade is currently running.
     *
     * @return bool
     */
    public static function is_upgrade_running(): bool {
        global $CFG;

        return !empty($CFG->upgraderunning);
    }

    /**
     * Ensure that an upgrade is not running, emitting an exception if it is.
     *
     * @throws moodle_exception
     * @return bool false if no upgrade is running
     */
    public static function ensure_upgrade_is_not_running(): bool {
        if (self::is_upgrade_running()) {
            throw new moodle_exception('cannotexecduringupgrade');
        }

        return false;
    }

    /**
     * Warn if an upgrade is currently running.
     *
     * @return bool true if an upgrade is running, false if no upgrade is running
     */
    public static function warn_if_upgrade_is_running(): bool {
        if (self::is_upgrade_running()) {
            debugging(get_string('cannotexecduringupgrade', 'error'), DEBUG_DEVELOPER);

            return true;
        }

        return false;
    }
}
