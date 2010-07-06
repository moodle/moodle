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
 * Non instantiable factory class providing different restore object instances
 *
 * This class contains various methods available in order to easily
 * create different parts of the restore architecture in an easy way
 *
 * TODO: Finish phpdocs
 */
abstract class restore_factory {

    public static function get_restore_activity_task($info) {

        $classname = 'restore_' . $info->modulename . '_activity_task';
        if (class_exists($classname)) {
            return new $classname($info->title, $info);
        }
    }

    public static function get_restore_block_task($format, $blockid, $moduleid = null) {
        global $CFG, $DB;

        // Check blockid exists
        if (!$block = $DB->get_record('block_instances', array('id' => $blockid))) {
            throw new restore_task_exception('block_task_block_instance_not_found', $blockid);
        }

        // Set default block backup task
        $classname = 'restore_default_block_task';
        $testname  = 'restore_' . $block->blockname . '_block_task';
        // If the block has custom backup/restore task class (testname), use it
        if (class_exists($testname)) {
            $classname = $testname;
        }
        return new $classname($block->blockname, $blockid, $moduleid);
    }

    public static function get_restore_section_task($info) {

        return new restore_section_task($info->title, $info);
    }

    public static function get_restore_course_task($info, $courseid) {
        global $DB;

        // Check course exists
        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            throw new restore_task_exception('course_task_course_not_found', $courseid);
        }

        return new restore_course_task($info->title, $info);
    }
}
