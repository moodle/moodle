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
 * This file replaces:
 *   * STATEMENTS section in db/install.xml
 *   * lib.php/modulename_install() post installation hook
 *   * partially defaults.php
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_lesson_install() {
    global $DB;

/// Install logging support
    update_log_display_entry('lesson', 'start', 'lesson', 'name');
    update_log_display_entry('lesson', 'end', 'lesson', 'name');
    update_log_display_entry('lesson', 'view', 'lesson_pages', 'title');

}
