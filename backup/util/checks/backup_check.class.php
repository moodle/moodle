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
 * @package    moodlecore
 * @subpackage backup-factories
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing different backup checks
 *
 * This class contains various static methods available in order to easily
 * perform a bunch of backup architecture tests
 *
 * TODO: Finish phpdocs
 */
abstract class backup_check {

    public static function check_format_and_type($format, $type) {
        global $CFG;

        $file = $CFG->dirroot . '/backup/' . $format . '/backup_plan_builder.class.php';
        if (! file_exists($file)) {
            throw new backup_controller_exception('backup_check_unsupported_format', $format);
        }
        require_once($file);
        if (!in_array($type, backup_plan_builder::supported_backup_types())) {
            throw new backup_controller_exception('backup_check_unsupported_type', $type);
        }

        require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
    }

    public static function check_id($type, $id) {
        global $DB;
        switch ($type) {
            case backup::TYPE_1ACTIVITY:
                // id must exist in course_modules table
                if (! $DB->record_exists('course_modules', array('id' => $id))) {
                    throw new backup_controller_exception('backup_check_module_not_exists', $id);
                }
                break;
            case backup::TYPE_1SECTION:
                // id must exist in course_sections table
                if (! $DB->record_exists('course_sections', array('id' => $id))) {
                    throw new backup_controller_exception('backup_check_section_not_exists', $id);
                }
                break;
            case backup::TYPE_1COURSE:
                // id must exist in course table
                if (! $DB->record_exists('course', array('id' => $id))) {
                    throw new backup_controller_exception('backup_check_course_not_exists', $id);
                }
                break;
            default:
                throw new backup_controller_exception('backup_check_incorrect_type', $type);
        }
        return true;
    }

    public static function check_user($userid) {
        global $DB;
        // userid must exist in user table
        if (! $DB->record_exists('user', array('id' => $userid))) {
            throw new backup_controller_exception('backup_check_user_not_exists', $userid);
        }
        return true;
    }

    public static function check_security($backup_controller, $apply) {
        if (! $backup_controller instanceof backup_controller) {
            throw new backup_controller_exception('backup_check_security_requires_backup_controller');
        }
        $backup_controller->log('checking plan security', backup::LOG_INFO);
        return true;
    }
}
