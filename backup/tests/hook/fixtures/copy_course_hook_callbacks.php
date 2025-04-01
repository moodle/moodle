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

namespace core_backup\hook\fixtures;

use core_backup\hook\before_copy_course_execute;

/**
 * Callbacks used to test the hooks in the copy course task.
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class copy_course_hook_callbacks {
    /** @var int Used to keep track oh how many times the callback has been called. */
    public static $count = 0;

    /**
     * Callback used to test the before_copy_course_execute hook.
     *
     * @param before_copy_course_execute $hook
     */
    public static function before_copy_course_execute(before_copy_course_execute $hook) {
        self::$count++;
    }
}
