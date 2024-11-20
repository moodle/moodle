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
 * Defines restore_plan_builder class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/moodle2/restore_root_task.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_course_task.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_section_task.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_activity_task.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_final_task.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_block_task.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_default_block_task.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_qbank_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_qtype_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_qtype_extrafields_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_format_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_local_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_theme_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_report_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_coursereport_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_plagiarism_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_gradingform_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_enrol_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_qbank_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_qtype_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_qtype_extrafields_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_format_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_local_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_theme_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_report_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_coursereport_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plagiarism_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_gradingform_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_enrol_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_subplugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_settingslib.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_stepslib.php');

// Load all the activity tasks for moodle2 format
$mods = core_component::get_plugin_list('mod');
foreach ($mods as $mod => $moddir) {
    $taskpath = $moddir . '/backup/moodle2/restore_' . $mod . '_activity_task.class.php';
    if (plugin_supports('mod', $mod, FEATURE_BACKUP_MOODLE2)) {
        if (file_exists($taskpath)) {
            require_once($taskpath);
        }
    }
}

// Load all the block tasks for moodle2 format
$blocks = core_component::get_plugin_list('block');
foreach ($blocks as $block => $blockdir) {
    $taskpath = $blockdir . '/backup/moodle2/restore_' . $block . '_block_task.class.php';
    if (file_exists($taskpath)) {
        require_once($taskpath);
    }
}

/**
 * Abstract class defining the static method in charge of building the whole
 * restore plan, based in @restore_controller preferences.
 *
 * TODO: Finish phpdocs
 */
abstract class restore_plan_builder {

    /**
     * Dispatches, based on type to specialised builders
     */
    public static function build_plan($controller) {

        $plan = $controller->get_plan();

        // Add the root task, responsible for
        // preparing everything, creating the
        // needed structures (users, roles),
        // preloading information to temp table
        // and other init tasks
        $plan->add_task(new restore_root_task('root_task'));
        $controller->get_progress()->progress();

        switch ($controller->get_type()) {
            case backup::TYPE_1ACTIVITY:
                self::build_activity_plan($controller, key($controller->get_info()->activities));
                break;
            case backup::TYPE_1SECTION:
                self::build_section_plan($controller, key($controller->get_info()->sections));
                break;
            case backup::TYPE_1COURSE:
                self::build_course_plan($controller, $controller->get_courseid());
                break;
        }

        // Add the final task, responsible for closing
        // all the pending bits (remapings, inter-links
        // conversion...)
        // and perform other various final actions.
        $plan->add_task(new restore_final_task('final_task'));
        $controller->get_progress()->progress();
    }


// Protected API starts here

    /**
     * Restore one 1-activity backup
     */
    protected static function build_activity_plan($controller, $activityid) {

        $plan = $controller->get_plan();
        $info = $controller->get_info();
        $infoactivity = $info->activities[$activityid];

        // Add the activity task, responsible for restoring
        // all the module related information. So it conditionally
        // as far as the module can be missing on restore
        if ($task = restore_factory::get_restore_activity_task($infoactivity)) { // can be missing
            $plan->add_task($task);
            $controller->get_progress()->progress();

            // Some activities may have delegated section integrations.
            self::build_delegated_section_plan($controller, $infoactivity->moduleid);

            // For the given activity path, add as many block tasks as necessary
            // TODO: Add blocks, we need to introspect xml here
            $blocks = backup_general_helper::get_blocks_from_path($task->get_taskbasepath());
            foreach ($blocks as $basepath => $name) {
                if ($task = restore_factory::get_restore_block_task($name, $basepath)) {
                    $plan->add_task($task);
                    $controller->get_progress()->progress();
                } else {
                    // TODO: Debug information about block not supported
                }
            }
        } else { // Activity is missing in target site, inform plan about that
            $plan->set_missing_modules();
        }

    }

    /**
     * Build a course module delegated section backup plan.
     * @param restore_controller $controller
     * @param int $cmid the parent course module id.
     */
    protected static function build_delegated_section_plan($controller, $cmid) {
        $info = $controller->get_info();

        // Find if some section depends on that course module.
        $delegatedsectionid = null;
        foreach ($info->sections as $sectionid => $section) {
            // Delegated sections are not course responsability.
            if (isset($section->parentcmid) && $section->parentcmid == $cmid) {
                $delegatedsectionid = $sectionid;
                break;
            }
        }

        if (!$delegatedsectionid) {
            return;
        }
        self::build_section_plan($controller, $delegatedsectionid);
    }

    /**
     * Restore one 1-section backup
     */
    protected static function build_section_plan($controller, $sectionid) {

        $plan = $controller->get_plan();
        $info = $controller->get_info();
        $infosection = $info->sections[$sectionid];

        // Add the section task, responsible for restoring
        // all the section related information
        $plan->add_task(restore_factory::get_restore_section_task($infosection));
        $controller->get_progress()->progress();
        // For the given section, add as many activity tasks as necessary
        foreach ($info->activities as $activityid => $activity) {
            if ($activity->sectionid != $infosection->sectionid) {
                continue;
            }
            if (plugin_supports('mod', $activity->modulename, FEATURE_BACKUP_MOODLE2)) { // Check we support the format
                self::build_activity_plan($controller, $activityid);
            } else {
                // TODO: Debug information about module not supported
            }
        }
    }

    /**
     * Restore one 1-course backup
     */
    protected static function build_course_plan($controller, $courseid) {

        $plan = $controller->get_plan();
        $info = $controller->get_info();

        // Add the course task, responsible for restoring
        // all the course related information
        $task = restore_factory::get_restore_course_task($info->course, $courseid);
        $plan->add_task($task);
        $controller->get_progress()->progress();

        // For the given course path, add as many block tasks as necessary
        // TODO: Add blocks, we need to introspect xml here
        $blocks = backup_general_helper::get_blocks_from_path($task->get_taskbasepath());
        foreach ($blocks as $basepath => $name) {
            if ($task = restore_factory::get_restore_block_task($name, $basepath)) {
                $plan->add_task($task);
                $controller->get_progress()->progress();
            } else {
                // TODO: Debug information about block not supported
            }
        }

        // For the given course, add as many section tasks as necessary
        foreach ($info->sections as $sectionid => $section) {
            // Delegated sections are not course responsability.
            if (isset($section->parentcmid) && !empty($section->parentcmid)) {
                continue;
            }
            self::build_section_plan($controller, $sectionid);
        }
    }
}
