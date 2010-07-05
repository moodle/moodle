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
 * @subpackage backup-dbops
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing DB support to the @backup_controller
 *
 * This class contains various static methods available for all the DB operations
 * performed by the backup_controller class
 *
 * TODO: Finish phpdocs
 */
abstract class backup_controller_dbops extends backup_dbops {

    public static function save_controller($controller, $checksum) {
        global $DB;
        // Check we are going to save one backup_controller
        if (! $controller instanceof backup_controller) {
            throw new backup_controller_exception('backup_controller_expected');
        }
        // Check checksum is ok. Sounds silly but it isn't ;-)
        if (!$controller->is_checksum_correct($checksum)) {
            throw new backup_dbops_exception('backup_controller_dbops_saving_checksum_mismatch');
        }
        // Get all the columns
        $rec = new stdclass();
        $rec->backupid     = $controller->get_backupid();
        $rec->operation    = $controller->get_operation();
        $rec->type         = $controller->get_type();
        $rec->itemid       = $controller->get_id();
        $rec->format       = $controller->get_format();
        $rec->interactive  = $controller->get_interactive();
        $rec->purpose      = $controller->get_mode();
        $rec->userid       = $controller->get_userid();
        $rec->status       = $controller->get_status();
        $rec->execution    = $controller->get_execution();
        $rec->executiontime= $controller->get_executiontime();
        $rec->checksum     = $checksum;
        // Serialize information
        $rec->controller = base64_encode(serialize($controller));
        // Send it to DB
        if ($recexists = $DB->get_record('backup_controllers', array('backupid' => $rec->backupid))) {
            $rec->id = $recexists->id;
            $rec->timemodified = time();
            $DB->update_record('backup_controllers', $rec);
        } else {
            $rec->timecreated = time();
            $rec->timemodified = 0;
            $rec->id = $DB->insert_record('backup_controllers', $rec);
        }
        return $rec->id;
    }

    public static function load_controller($backupid) {
        global $DB;
        if (! $controllerrec = $DB->get_record('backup_controllers', array('backupid' => $backupid))) {
            throw new backup_dbops_exception('backup_controller_dbops_nonexisting');
        }
        $controller = unserialize(base64_decode($controllerrec->controller));
        // Check checksum is ok. Sounds silly but it isn't ;-)
        if (!$controller->is_checksum_correct($controllerrec->checksum)) {
            throw new backup_dbops_exception('backup_controller_dbops_loading_checksum_mismatch');
        }
        return $controller;
    }

    public static function create_backup_ids_temp_table($backupid) {
        global $CFG, $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        // Note: For now we are going to load the backup_ids_template from core lib/db/install.xml
        // that way, any change in the "template" will be applied here automatically. If this causes
        // too much slow, we can always forget about the template and keep maintained the xmldb_table
        // structure inline - manually - here.
        $templatetablename = 'backup_ids_template';
        $targettablename = 'backup_ids_temp';
        $xmldb_file = new xmldb_file($CFG->dirroot . '/lib/db/install.xml');
        if (!$xmldb_file->fileExists()) {
            throw new ddl_exception('ddlxmlfileerror', null, 'File does not exist');
        }
        $loaded = $xmldb_file->loadXMLStructure();
        if (!$loaded || !$xmldb_file->isLoaded()) {
            throw new ddl_exception('ddlxmlfileerror', null, 'not loaded??');
        }
        $xmldb_structure = $xmldb_file->getStructure();
        $xmldb_table = $xmldb_structure->getTable($templatetablename);
        if (is_null($xmldb_table)) {
            throw new ddl_exception('ddlunknowntable', null, 'The table ' . $templatetablename . ' is not defined in file ' . $file);
        }
        // Set default backupid (not needed but this enforce any missing backupid). That's hackery in action!
        $xmldb_table->getField('backupid')->setDefault($backupid);
        // Clean prev & next, we are alone
        $xmldb_table->setNext(null);
        $xmldb_table->setPrevious(null);
        // Rename
        $xmldb_table->setName($targettablename);

        $dbman->create_temp_table($xmldb_table); // And create it
    }

    public static function drop_backup_ids_temp_table($backupid) {
        global $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        $targettablename = 'backup_ids_temp';
        $table = new xmldb_table($targettablename);
        $dbman->drop_temp_table($table); // And drop it
    }

    /**
     * Given one type and id from controller, return the corresponding courseid
     */
    public static function get_courseid_from_type_id($type, $id) {
        global $DB;
        if ($type == backup::TYPE_1COURSE) {
            return $id; // id is the course id

        } else if ($type == backup::TYPE_1SECTION) {
            if (! $courseid = $DB->get_field('course_sections', 'course', array('id' => $id))) {
                throw new backup_dbops_exception('course_not_found_for_section', $id);
            }
            return $courseid;
        } else if ($type == backup::TYPE_1ACTIVITY) {
            if (! $courseid = $DB->get_field('course_modules', 'course', array('id' => $id))) {
                throw new backup_dbops_exception('course_not_found_for_moduleid', $id);
            }
            return $courseid;
        }
    }

    /**
     * Given one activity task, return the activity information and related settings
     * Used by get_moodle_backup_information()
     */
    private static function get_activity_backup_information($task) {

        $contentinfo = array(
            'moduleid'   => $task->get_moduleid(),
            'sectionid'  => $task->get_sectionid(),
            'modulename' => $task->get_modulename(),
            'title'      => $task->get_name(),
            'directory'  => 'activities/' . $task->get_modulename() . '_' . $task->get_moduleid());

        // Now get activity settings
        // Calculate prefix to find valid settings
        $prefix = basename($contentinfo['directory']);
        $settingsinfo = array();
        foreach ($task->get_settings() as $setting) {
            // Discard ones without valid prefix
            if (strpos($setting->get_name(), $prefix) !== 0) {
                continue;
            }
            // Validate level is correct (activity)
            if ($setting->get_level() != backup_setting::ACTIVITY_LEVEL) {
                throw new backup_controller_exception('setting_not_activity_level', $setting);
            }
            $settinginfo = array(
                'level'    => 'activity',
                'activity' => $prefix,
                'name'     => $setting->get_name(),
                'value'    => $setting->get_value());
            $settingsinfo[$setting->get_name()] = (object)$settinginfo;
        }
        return array($contentinfo, $settingsinfo);
    }

    /**
     * Given one section task, return the section information and related settings
     * Used by get_moodle_backup_information()
     */
    private static function get_section_backup_information($task) {

        $contentinfo = array(
            'sectionid'  => $task->get_sectionid(),
            'title'      => $task->get_name(),
            'directory'  => 'sections/' . 'section_' . $task->get_sectionid());

        // Now get section settings
        // Calculate prefix to find valid settings
        $prefix = basename($contentinfo['directory']);
        $settingsinfo = array();
        foreach ($task->get_settings() as $setting) {
            // Discard ones without valid prefix
            if (strpos($setting->get_name(), $prefix) !== 0) {
                continue;
            }
            // Validate level is correct (section)
            if ($setting->get_level() != backup_setting::SECTION_LEVEL) {
                throw new backup_controller_exception('setting_not_section_level', $setting);
            }
            $settinginfo = array(
                'level'    => 'section',
                'section'  => $prefix,
                'name'     => $setting->get_name(),
                'value'    => $setting->get_value());
            $settingsinfo[$setting->get_name()] = (object)$settinginfo;
        }
        return array($contentinfo, $settingsinfo);
    }

    /**
     * Given one course task, return the course information and related settings
     * Used by get_moodle_backup_information()
     */
    private static function get_course_backup_information($task) {

        $contentinfo = array(
            'courseid'   => $task->get_courseid(),
            'title'      => $task->get_name(),
            'directory'  => 'course');

        // Now get course settings
        // Calculate prefix to find valid settings
        $prefix = basename($contentinfo['directory']);
        $settingsinfo = array();
        foreach ($task->get_settings() as $setting) {
            // Discard ones without valid prefix
            if (strpos($setting->get_name(), $prefix) !== 0) {
                continue;
            }
            // Validate level is correct (course)
            if ($setting->get_level() != backup_setting::COURSE_LEVEL) {
                throw new backup_controller_exception('setting_not_course_level', $setting);
            }
            $settinginfo = array(
                'level'    => 'course',
                'name'     => $setting->get_name(),
                'value'    => $setting->get_value());
            $settingsinfo[$setting->get_name()] = (object)$settinginfo;
        }
        return array($contentinfo, $settingsinfo);
    }

    /**
     * Given one root task, return the course information and related settings
     * Used by get_moodle_backup_information()
     */
    private static function get_root_backup_information($task) {

        // Now get root settings
        $settingsinfo = array();
        foreach ($task->get_settings() as $setting) {
            // Validate level is correct (root)
            if ($setting->get_level() != backup_setting::ROOT_LEVEL) {
                throw new backup_controller_exception('setting_not_root_level', $setting);
            }
            $settinginfo = array(
                'level'    => 'root',
                'name'     => $setting->get_name(),
                'value'    => $setting->get_value());
            $settingsinfo[$setting->get_name()] = (object)$settinginfo;
        }
        return array(null, $settingsinfo);
    }

    /**
     * Get details information for main moodle_backup.xml file, extracting it from
     * the specified controller
     */
    public static function get_moodle_backup_information($backupid) {

        $detailsinfo = array(); // Information details
        $contentsinfo= array(); // Information about backup contents
        $settingsinfo= array(); // Information about backup settings
        $bc = self::load_controller($backupid); // Load controller

        // Details info
        $detailsinfo['id'] = $bc->get_id();
        $detailsinfo['backup_id'] = $bc->get_backupid();
        $detailsinfo['type'] = $bc->get_type();
        $detailsinfo['format'] = $bc->get_format();
        $detailsinfo['interactive'] = $bc->get_interactive();
        $detailsinfo['mode'] = $bc->get_mode();
        $detailsinfo['execution'] = $bc->get_execution();
        $detailsinfo['executiontime'] = $bc->get_executiontime();
        $detailsinfo['userid'] = $bc->get_userid();
        $detailsinfo['courseid'] = $bc->get_courseid();


        // Init content placeholders
        $contentsinfo['activities'] = array();
        $contentsinfo['sections']   = array();
        $contentsinfo['course']     = array();

        // Contents info (extract information from tasks)
        foreach ($bc->get_plan()->get_tasks() as $task) {

            if ($task instanceof backup_activity_task) { // Activity task

                list($contentinfo, $settings) = self::get_activity_backup_information($task);
                $contentsinfo['activities'][] = $contentinfo;
                $settingsinfo = array_merge($settingsinfo, $settings);

            } else if ($task instanceof backup_section_task) { // Section task

                list($contentinfo, $settings) = self::get_section_backup_information($task);
                $contentsinfo['sections'][] = $contentinfo;
                $settingsinfo = array_merge($settingsinfo, $settings);

            } else if ($task instanceof backup_course_task) { // Course task

                list($contentinfo, $settings) = self::get_course_backup_information($task);
                $contentsinfo['course'][] = $contentinfo;
                $settingsinfo = array_merge($settingsinfo, $settings);

            } else if ($task instanceof backup_root_task) { // Root task

                list($contentinfo, $settings) = self::get_root_backup_information($task);
                $settingsinfo = array_merge($settingsinfo, $settings);
            }
        }

        return array(array((object)$detailsinfo), $contentsinfo, $settingsinfo);
    }

    /**
     * Update CFG->backup_version and CFG->backup_release if change in
     * version is detected.
     */
    public static function apply_version_and_release() {
        global $CFG;

        if ($CFG->backup_version < backup::VERSION) {
            set_config('backup_version', backup::VERSION);
            set_config('backup_release', backup::RELEASE);
        }
    }

    /**
     * Sets the controller settings default values from the backup config.
     * 
     * @param backup_controller $controller
     */
    public static function apply_general_config_defaults(backup_controller $controller) {
        $settings = array(
            // Config name                      => Setting name
            'backup_general_users'              => 'users',
            'backup_general_anonymize'          => 'anonymize',
            'backup_general_role_assignments'   => 'role_assignments',
            'backup_general_user_files'         => 'user_files',
            'backup_general_activities'         => 'activities',
            'backup_general_blocks'             => 'blocks',
            'backup_general_filters'            => 'filters',
            'backup_general_comments'           => 'comments',
            'backup_general_userscompletion'    => 'userscompletion',
            'backup_general_logs'               => 'logs',
            'backup_general_histories'          => 'grade_histories'
        );
        $plan = $controller->get_plan();
        foreach ($settings as $config=>$settingname) {
            $value = get_config('backup', $config);
            $locked = (get_config('backup', $config.'_locked') == true);
            if ($plan->setting_exists($settingname)) {
                $setting = $plan->get_setting($settingname);
                if ($setting->get_value() != $value || 1==1) {
                    $setting->set_value($value);
                    if ($locked) {
                        $setting->set_status(base_setting::LOCKED_BY_CONFIG);
                    }
                }
            }
        }
    }
}
