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

    /**
     * @var string Backup id for cached backup_includes_files result.
     */
    protected static $includesfilescachebackupid;

    /**
     * @var int Cached backup_includes_files result
     */
    protected static $includesfilescache;

    /**
     * Send one backup controller to DB
     *
     * @param backup_controller $controller controller to send to DB
     * @param string $checksum hash of the controller to be checked
     * @param bool $includeobj to decide if the object itself must be updated (true) or no (false)
     * @param bool $cleanobj to decide if the object itself must be cleaned (true) or no (false)
     * @return int id of the controller record in the DB
     * @throws backup_controller_exception|backup_dbops_exception
     */
    public static function save_controller($controller, $checksum, $includeobj = true, $cleanobj = false) {
        global $DB;
        // Check we are going to save one backup_controller
        if (! $controller instanceof backup_controller) {
            throw new backup_controller_exception('backup_controller_expected');
        }
        // Check checksum is ok. Only if we are including object info. Sounds silly but it isn't ;-).
        if ($includeobj and !$controller->is_checksum_correct($checksum)) {
            throw new backup_dbops_exception('backup_controller_dbops_saving_checksum_mismatch');
        }
        // Cannot request to $includeobj and $cleanobj at the same time.
        if ($includeobj and $cleanobj) {
            throw new backup_dbops_exception('backup_controller_dbops_saving_cannot_include_and_delete');
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
        if ($includeobj) {
            $rec->controller = base64_encode(serialize($controller));
        } else if ($cleanobj) {
            $rec->controller = '';
        }
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

        $xmldb_table = new xmldb_table('backup_ids_temp');
        $xmldb_table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // Set default backupid (not needed but this enforce any missing backupid). That's hackery in action!
        $xmldb_table->add_field('backupid', XMLDB_TYPE_CHAR, 32, null, XMLDB_NOTNULL, null, $backupid);
        $xmldb_table->add_field('itemname', XMLDB_TYPE_CHAR, 160, null, XMLDB_NOTNULL, null, null);
        $xmldb_table->add_field('itemid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
        $xmldb_table->add_field('newitemid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
        $xmldb_table->add_field('parentitemid', XMLDB_TYPE_INTEGER, 10, null, null, null, null);
        $xmldb_table->add_field('info', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $xmldb_table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $xmldb_table->add_key('backupid_itemname_itemid_uk', XMLDB_KEY_UNIQUE, array('backupid','itemname','itemid'));
        $xmldb_table->add_index('backupid_parentitemid_ix', XMLDB_INDEX_NOTUNIQUE, array('backupid','itemname','parentitemid'));
        $xmldb_table->add_index('backupid_itemname_newitemid_ix', XMLDB_INDEX_NOTUNIQUE, array('backupid','itemname','newitemid'));

        $dbman->create_temp_table($xmldb_table); // And create it

    }

    public static function create_backup_files_temp_table($backupid) {
        global $CFG, $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        $xmldb_table = new xmldb_table('backup_files_temp');
        $xmldb_table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // Set default backupid (not needed but this enforce any missing backupid). That's hackery in action!
        $xmldb_table->add_field('backupid', XMLDB_TYPE_CHAR, 32, null, XMLDB_NOTNULL, null, $backupid);
        $xmldb_table->add_field('contextid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
        $xmldb_table->add_field('component', XMLDB_TYPE_CHAR, 100, null, XMLDB_NOTNULL, null, null);
        $xmldb_table->add_field('filearea', XMLDB_TYPE_CHAR, 50, null, XMLDB_NOTNULL, null, null);
        $xmldb_table->add_field('itemid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
        $xmldb_table->add_field('info', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $xmldb_table->add_field('newcontextid', XMLDB_TYPE_INTEGER, 10, null, null, null, null);
        $xmldb_table->add_field('newitemid', XMLDB_TYPE_INTEGER, 10, null, null, null, null);
        $xmldb_table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $xmldb_table->add_index('backupid_contextid_component_filearea_itemid_ix', XMLDB_INDEX_NOTUNIQUE, array('backupid','contextid','component','filearea','itemid'));

        $dbman->create_temp_table($xmldb_table); // And create it
    }

    public static function drop_backup_ids_temp_table($backupid) {
        global $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        $targettablename = 'backup_ids_temp';
        if ($dbman->table_exists($targettablename)) {
            $table = new xmldb_table($targettablename);
            $dbman->drop_table($table); // And drop it
        }
    }

    /**
     * Decode the info field from backup_ids_temp or backup_files_temp.
     *
     * @param mixed $info The info field data to decode, may be an object or a simple integer.
     * @return mixed The decoded information.  For simple types it returns, for complex ones we decode.
     */
    public static function decode_backup_temp_info($info) {
        // We encode all data except null.
        if ($info != null) {
            return unserialize(gzuncompress(base64_decode($info)));
        }
        return $info;
    }

    /**
     * Encode the info field for backup_ids_temp or backup_files_temp.
     *
     * @param mixed $info string The info field data to encode.
     * @return string An encoded string of data or null if the input is null.
     */
    public static function encode_backup_temp_info($info) {
        // We encode if there is any information to keep the translations simpler.
        if ($info != null) {
            // We compress if possible. It reduces db, network and memory storage. The saving is greater than CPU compression cost.
            // Compression level 1 is chosen has it produces good compression with the smallest possible overhead, see MDL-40618.
            return base64_encode(gzcompress(serialize($info), 1));
        }
        return $info;
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
     * the specified controller.
     *
     * If you specify the progress monitor, this will start a new progress section
     * to track progress in processing (in case this task takes a long time).
     *
     * @param string $backupid Backup ID
     * @param \core\progress\base $progress Optional progress monitor
     */
    public static function get_moodle_backup_information($backupid,
            \core\progress\base $progress = null) {

        // Start tracking progress if required (for load_controller).
        if ($progress) {
            $progress->start_progress('get_moodle_backup_information', 2);
        }

        $detailsinfo = array(); // Information details
        $contentsinfo= array(); // Information about backup contents
        $settingsinfo= array(); // Information about backup settings
        $bc = self::load_controller($backupid); // Load controller

        // Note that we have loaded controller.
        if ($progress) {
            $progress->progress(1);
        }

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

        // Get tasks and start nested progress.
        $tasks = $bc->get_plan()->get_tasks();
        if ($progress) {
            $progress->start_progress('get_moodle_backup_information', count($tasks));
            $done = 1;
        }

        // Contents info (extract information from tasks)
        foreach ($tasks as $task) {

            if ($task instanceof backup_activity_task) { // Activity task

                if ($task->get_setting_value('included')) { // Only return info about included activities
                    list($contentinfo, $settings) = self::get_activity_backup_information($task);
                    $contentsinfo['activities'][] = $contentinfo;
                    $settingsinfo = array_merge($settingsinfo, $settings);
                }

            } else if ($task instanceof backup_section_task) { // Section task

                if ($task->get_setting_value('included')) { // Only return info about included sections
                    list($contentinfo, $settings) = self::get_section_backup_information($task);
                    $contentsinfo['sections'][] = $contentinfo;
                    $settingsinfo = array_merge($settingsinfo, $settings);
                }

            } else if ($task instanceof backup_course_task) { // Course task

                list($contentinfo, $settings) = self::get_course_backup_information($task);
                $contentsinfo['course'][] = $contentinfo;
                $settingsinfo = array_merge($settingsinfo, $settings);

            } else if ($task instanceof backup_root_task) { // Root task

                list($contentinfo, $settings) = self::get_root_backup_information($task);
                $settingsinfo = array_merge($settingsinfo, $settings);
            }

            // Report task handled.
            if ($progress) {
                $progress->progress($done++);
            }
        }

        $bc->destroy(); // Always need to destroy controller to handle circular references

        // Finish progress reporting.
        if ($progress) {
            $progress->end_progress();
            $progress->end_progress();
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
     * Given the backupid, detect if the backup includes "mnet" remote users or no
     */
    public static function backup_includes_mnet_remote_users($backupid) {
        global $CFG, $DB;

        $sql = "SELECT COUNT(*)
                  FROM {backup_ids_temp} b
                  JOIN {user} u ON u.id = b.itemid
                 WHERE b.backupid = ?
                   AND b.itemname = 'userfinal'
                   AND u.mnethostid != ?";
        $count = $DB->count_records_sql($sql, array($backupid, $CFG->mnet_localhost_id));
        return (int)(bool)$count;
    }

    /**
     * Given the backupid, determine whether this backup should include
     * files from the moodle file storage system.
     *
     * @param string $backupid The ID of the backup.
     * @return int Indicates whether files should be included in backups.
     */
    public static function backup_includes_files($backupid) {
        // This function is called repeatedly in a backup with many files.
        // Loading the controller is a nontrivial operation (in a large test
        // backup it took 0.3 seconds), so we do a temporary cache of it within
        // this request.
        if (self::$includesfilescachebackupid === $backupid) {
            return self::$includesfilescache;
        }

        // Load controller, get value, then destroy controller and return result.
        self::$includesfilescachebackupid = $backupid;
        $bc = self::load_controller($backupid);
        self::$includesfilescache = $bc->get_include_files();
        $bc->destroy();
        return self::$includesfilescache;
    }

    /**
     * Given the backupid, detect if the backup contains references to external contents
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     * @return int
     */
    public static function backup_includes_file_references($backupid) {
        global $CFG, $DB;

        $sql = "SELECT count(r.repositoryid)
                  FROM {files} f
                  LEFT JOIN {files_reference} r
                       ON r.id = f.referencefileid
                  JOIN {backup_ids_temp} bi
                       ON f.id = bi.itemid
                 WHERE bi.backupid = ?
                       AND bi.itemname = 'filefinal'";
        $count = $DB->count_records_sql($sql, array($backupid));
        return (int)(bool)$count;
    }

    /**
     * Given the courseid, return some course related information we want to transport
     *
     * @param int $course the id of the course this backup belongs to
     */
    public static function backup_get_original_course_info($courseid) {
        global $DB;
        return $DB->get_record('course', array('id' => $courseid), 'fullname, shortname, startdate');
    }

    /**
     * Sets the default values for the settings in a backup operation
     *
     * Based on the mode of the backup it will delegate the process to
     * other methods like {@link apply_general_config_defaults} ...
     * to get proper defaults loaded
     *
     * @param backup_controller $controller
     */
    public static function apply_config_defaults(backup_controller $controller) {
        // Based on the mode of the backup (general, automated, import, hub...)
        // decide the action to perform to get defaults loaded
        $mode = $controller->get_mode();

        switch ($mode) {
            case backup::MODE_GENERAL:
                // Load the general defaults
                self::apply_general_config_defaults($controller);
                break;
            case backup::MODE_AUTOMATED:
                // Load the automated defaults.
                self::apply_auto_config_defaults($controller);
                break;
            default:
                // Nothing to do for other modes (IMPORT/HUB...). Some day we
                // can define defaults (admin UI...) for them if we want to
        }
    }

    /**
     * Sets the controller settings default values from the automated backup config.
     *
     * @param backup_controller $controller
     */
    private static function apply_auto_config_defaults(backup_controller $controller) {
        $settings = array(
            // Config name                   => Setting name.
            'backup_auto_users'              => 'users',
            'backup_auto_role_assignments'   => 'role_assignments',
            'backup_auto_activities'         => 'activities',
            'backup_auto_blocks'             => 'blocks',
            'backup_auto_filters'            => 'filters',
            'backup_auto_comments'           => 'comments',
            'backup_auto_badges'             => 'badges',
            'backup_auto_userscompletion'    => 'userscompletion',
            'backup_auto_logs'               => 'logs',
            'backup_auto_histories'          => 'grade_histories',
            'backup_auto_questionbank'       => 'questionbank'
        );
        $plan = $controller->get_plan();
        foreach ($settings as $config => $settingname) {
            $value = get_config('backup', $config);
            if ($value === false) {
                // The setting is not set.
                $controller->log('Could not find a value for the config ' . $config, BACKUP::LOG_DEBUG);
                continue;
            }
            if ($plan->setting_exists($settingname)) {
                $setting = $plan->get_setting($settingname);
                $setting->set_value($value);
            } else {
                $controller->log('Unknown setting: ' . $settingname, BACKUP::LOG_DEBUG);
            }
        }
    }

    /**
     * Sets the controller settings default values from the backup config.
     *
     * @param backup_controller $controller
     */
    private static function apply_general_config_defaults(backup_controller $controller) {
        $settings = array(
            // Config name                      => Setting name
            'backup_general_users'              => 'users',
            'backup_general_anonymize'          => 'anonymize',
            'backup_general_role_assignments'   => 'role_assignments',
            'backup_general_activities'         => 'activities',
            'backup_general_blocks'             => 'blocks',
            'backup_general_filters'            => 'filters',
            'backup_general_comments'           => 'comments',
            'backup_general_badges'             => 'badges',
            'backup_general_userscompletion'    => 'userscompletion',
            'backup_general_logs'               => 'logs',
            'backup_general_histories'          => 'grade_histories',
            'backup_general_questionbank'       => 'questionbank'
        );
        $plan = $controller->get_plan();
        foreach ($settings as $config=>$settingname) {
            $value = get_config('backup', $config);
            if ($value === false) {
                // Ignore this because the config has not been set. get_config
                // returns false if a setting doesn't exist, '0' is returned when
                // the configuration is set to false.
                $controller->log('Could not find a value for the config ' . $config, BACKUP::LOG_DEBUG);
                continue;
            }
            $locked = (get_config('backup', $config.'_locked') == true);
            if ($plan->setting_exists($settingname)) {
                $setting = $plan->get_setting($settingname);
                if ($setting->get_value() != $value || 1==1) {
                    $setting->set_value($value);
                    if ($locked) {
                        $setting->set_status(base_setting::LOCKED_BY_CONFIG);
                    }
                }
            } else {
                $controller->log('Unknown setting: ' . $setting, BACKUP::LOG_DEBUG);
            }
        }
    }
}
