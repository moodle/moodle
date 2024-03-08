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
 * Non instantiable factory class providing different backup object instances
 *
 * This class contains various methods available in order to easily
 * create different parts of the backup architecture in an easy way
 *
 * TODO: Finish phpdocs
 */
abstract class backup_factory {

    public static function get_destination_chain($type, $id, $mode, $execution) {
        return null;
    }

    public static function get_logger_chain($interactive, $execution, $backupid) {
        global $CFG;

        $dfltloglevel = backup::LOG_WARNING; // Default logging level
        if ($CFG->debugdeveloper) { // Debug developer raises default logging level
            $dfltloglevel = backup::LOG_DEBUG;
        }

        $enabledloggers = array(); // Array to store all enabled loggers

        // Create error_log_logger, observing $CFG->backup_error_log_logger_level,
        // defaulting to $dfltloglevel
        $elllevel = isset($CFG->backup_error_log_logger_level) ? $CFG->backup_error_log_logger_level : $dfltloglevel;
        $enabledloggers[] = new error_log_logger($elllevel);

        // Create output_indented_logger, observing $CFG->backup_output_indented_logger_level and $CFG->debugdisplay,
        // defaulting to LOG_ERROR. Only if interactive and inmediate
        if ($CFG->debugdisplay && $interactive == backup::INTERACTIVE_YES && $execution == backup::EXECUTION_INMEDIATE) {
            $oillevel = isset($CFG->backup_output_indented_logger_level) ? $CFG->backup_output_indented_logger_level : backup::LOG_ERROR;
            $enabledloggers[] = new output_indented_logger($oillevel, false, false);
        }

        // Create file_logger, observing $CFG->backup_file_logger_level
        // defaulting to $dfltloglevel
        $backuptempdir = make_backup_temp_directory(''); // Need to ensure that $CFG->backuptempdir already exists.
        $fllevel = isset($CFG->backup_file_logger_level) ? $CFG->backup_file_logger_level : $dfltloglevel;
        $enabledloggers[] = new file_logger($fllevel, true, true, $backuptempdir . '/' . $backupid . '.log');

        // Create database_logger, observing $CFG->backup_database_logger_level and defaulting to LOG_WARNING
        // and pointing to the backup_logs table
        $dllevel = isset($CFG->backup_database_logger_level) ? $CFG->backup_database_logger_level : $dfltloglevel;
        $columns = array('backupid' => $backupid);
        $enabledloggers[] = new database_logger($dllevel, 'timecreated', 'loglevel', 'message', 'backup_logs', $columns);

        // Create extra file_logger, observing $CFG->backup_file_logger_extra and $CFG->backup_file_logger_extra_level
        // defaulting to $fllevel (normal file logger)
        if (isset($CFG->backup_file_logger_extra)) {
            $flelevel = isset($CFG->backup_file_logger_extra_level) ? $CFG->backup_file_logger_extra_level : $fllevel;
            $enabledloggers[] =  new file_logger($flelevel, true, true, $CFG->backup_file_logger_extra);
        }

        // Build the chain
        $loggers = null;
        foreach ($enabledloggers as $currentlogger) {
            if ($loggers == null) {
                $loggers = $currentlogger;
            } else {
                $lastlogger->set_next($currentlogger);
            }
            $lastlogger = $currentlogger;
        }

        return $loggers;
    }


    /**
     * Given one format and one course module id, return the corresponding
     * backup_xxxx_activity_task()
     */
    public static function get_backup_activity_task($format, $moduleid) {
        global $CFG, $DB;

        // Check moduleid exists
        if (!$coursemodule = get_coursemodule_from_id(false, $moduleid)) {
            throw new backup_task_exception('activity_task_coursemodule_not_found', $moduleid);
        }
        $classname = 'backup_' . $coursemodule->modname . '_activity_task';
        return new $classname($coursemodule->name, $moduleid);
    }

    /**
     * Given one format, one block id and, optionally, one moduleid, return the corresponding backup_xxx_block_task()
     */
    public static function get_backup_block_task($format, $blockid, $moduleid = null) {
        global $CFG, $DB;

        // Check blockid exists
        if (!$block = $DB->get_record('block_instances', array('id' => $blockid))) {
            throw new backup_task_exception('block_task_block_instance_not_found', $blockid);
        }

        // Set default block backup task
        $classname = 'backup_default_block_task';
        $testname  = 'backup_' . $block->blockname . '_block_task';
        // If the block has custom backup/restore task class (testname), use it
        if (class_exists($testname)) {
            $classname = $testname;
        }
        return new $classname($block->blockname, $blockid, $moduleid);
    }

    /**
     * Given one format and one section id, return the corresponding backup_section_task()
     */
    public static function get_backup_section_task($format, $sectionid) {
        global $DB;

        // Check section exists
        if (!$section = $DB->get_record('course_sections', array('id' => $sectionid))) {
            throw new backup_task_exception('section_task_section_not_found', $sectionid);
        }

        return new backup_section_task((string)$section->name === '' ? $section->section : $section->name, $sectionid);
    }

    /**
     * Given one format and one course id, return the corresponding backup_course_task()
     */
    public static function get_backup_course_task($format, $courseid) {
        global $DB;

        // Check course exists
        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            throw new backup_task_exception('course_task_course_not_found', $courseid);
        }

        return new backup_course_task($course->shortname, $courseid);
    }

    /**
     * Dispatches the creation of the @backup_plan to the proper format builder
     */
    public static function build_plan($controller) {
        backup_plan_builder::build_plan($controller);
    }
}
