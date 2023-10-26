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
 * Non instantiable helper class providing DB support to the @restore_controller
 *
 * This class contains various static methods available for all the DB operations
 * performed by the restore_controller class
 *
 * TODO: Finish phpdocs
 */
abstract class restore_controller_dbops extends restore_dbops {

    /**
     * Send one restore controller to DB
     *
     * @param restore_controller $controller controller to send to DB
     * @param string $checksum hash of the controller to be checked
     * @param bool $includeobj to decide if the object itself must be updated (true) or no (false)
     * @param bool $cleanobj to decide if the object itself must be cleaned (true) or no (false)
     * @return int id of the controller record in the DB
     * @throws backup_controller_exception|restore_dbops_exception
     */
    public static function save_controller($controller, $checksum, $includeobj = true, $cleanobj = false) {
        global $DB;
        // Check we are going to save one backup_controller
        if (! $controller instanceof restore_controller) {
            throw new backup_controller_exception('restore_controller_expected');
        }
        // Check checksum is ok. Only if we are including object info. Sounds silly but it isn't ;-).
        if ($includeobj and !$controller->is_checksum_correct($checksum)) {
            throw new restore_dbops_exception('restore_controller_dbops_saving_checksum_mismatch');
        }
        // Cannot request to $includeobj and $cleanobj at the same time.
        if ($includeobj and $cleanobj) {
            throw new restore_dbops_exception('restore_controller_dbops_saving_cannot_include_and_delete');
        }
        // Get all the columns
        $rec = new stdclass();
        $rec->backupid     = $controller->get_restoreid();
        $rec->operation    = $controller->get_operation();
        $rec->type         = $controller->get_type();
        $rec->itemid       = $controller->get_courseid();
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

    public static function load_controller($restoreid) {
        global $DB;
        if (! $controllerrec = $DB->get_record('backup_controllers', array('backupid' => $restoreid))) {
            throw new backup_dbops_exception('restore_controller_dbops_nonexisting');
        }
        $controller = unserialize(base64_decode($controllerrec->controller));
        if (!is_object($controller)) {
            // The controller field of the table did not contain a serialized object.
            // It is made empty after it has been used successfully, it is likely that
            // the user has pressed the browser back button at some point.
            throw new backup_dbops_exception('restore_controller_dbops_loading_invalid_controller');
        }
        // Check checksum is ok. Sounds silly but it isn't ;-)
        if (!$controller->is_checksum_correct($controllerrec->checksum)) {
            throw new backup_dbops_exception('restore_controller_dbops_loading_checksum_mismatch');
        }
        return $controller;
    }

    public static function create_restore_temp_tables($restoreid) {
        global $CFG, $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        if ($dbman->table_exists('backup_ids_temp')) { // Table exists, from restore prechecks
            // TODO: Improve this by inserting/selecting some record to see there is restoreid match
            // TODO: If not match, exception, table corresponds to another backup/restore operation
            return true;
        }
        backup_controller_dbops::create_backup_ids_temp_table($restoreid);
        backup_controller_dbops::create_backup_files_temp_table($restoreid);
        return false;
    }

    public static function drop_restore_temp_tables($backupid) {
        global $DB;
        $dbman = $DB->get_manager(); // We are going to use database_manager services

        $targettablenames = array('backup_ids_temp', 'backup_files_temp');
        foreach ($targettablenames as $targettablename) {
            $table = new xmldb_table($targettablename);
            $dbman->drop_table($table); // And drop it
        }
        // Invalidate the backup_ids caches.
        restore_dbops::reset_backup_ids_cached();
    }

    /**
     * Sets the default values for the settings in a restore operation
     *
     * @param restore_controller $controller
     */
    public static function apply_config_defaults(restore_controller $controller) {

        $settings = array(
            'restore_general_users'              => 'users',
            'restore_general_enrolments'         => 'enrolments',
            'restore_general_role_assignments'   => 'role_assignments',
            'restore_general_permissions'        => 'permissions',
            'restore_general_activities'         => 'activities',
            'restore_general_blocks'             => 'blocks',
            'restore_general_filters'            => 'filters',
            'restore_general_comments'           => 'comments',
            'restore_general_badges'             => 'badges',
            'restore_general_calendarevents'     => 'calendarevents',
            'restore_general_userscompletion'    => 'userscompletion',
            'restore_general_logs'               => 'logs',
            'restore_general_histories'          => 'grade_histories',
            'restore_general_questionbank'       => 'questionbank',
            'restore_general_groups'             => 'groups',
            'restore_general_competencies'       => 'competencies',
            'restore_general_contentbankcontent' => 'contentbankcontent',
            'restore_general_legacyfiles'        => 'legacyfiles'
        );
        self::apply_admin_config_defaults($controller, $settings, true);

        $target = $controller->get_target();
        if ($target == backup::TARGET_EXISTING_ADDING || $target == backup::TARGET_CURRENT_ADDING) {
            $settings = array(
                'restore_merge_overwrite_conf'  => 'overwrite_conf',
                'restore_merge_course_fullname'  => 'course_fullname',
                'restore_merge_course_shortname' => 'course_shortname',
                'restore_merge_course_startdate' => 'course_startdate',
            );
            self::apply_admin_config_defaults($controller, $settings, true);
        }

        if ($target == backup::TARGET_EXISTING_DELETING || $target == backup::TARGET_CURRENT_DELETING) {
            $settings = array(
                'restore_replace_overwrite_conf'  => 'overwrite_conf',
                'restore_replace_course_fullname'  => 'course_fullname',
                'restore_replace_course_shortname' => 'course_shortname',
                'restore_replace_course_startdate' => 'course_startdate',
                'restore_replace_keep_roles_and_enrolments' => 'keep_roles_and_enrolments',
                'restore_replace_keep_groups_and_groupings' => 'keep_groups_and_groupings',
            );
            self::apply_admin_config_defaults($controller, $settings, true);
        }
        if ($controller->get_mode() == backup::MODE_IMPORT &&
                (!$controller->get_interactive()) &&
                $controller->get_type() == backup::TYPE_1ACTIVITY) {
            // This is duplicate - there is no concept of defaults - these settings must be on.
            $settings = array(
                    'activities',
                    'blocks',
                    'filters',
                    'questionbank'
            );
            self::force_enable_settings($controller, $settings);
        };

        // Add some dependencies.
        $plan = $controller->get_plan();
        if ($plan->setting_exists('overwrite_conf')) {
            /** @var restore_course_overwrite_conf_setting $overwriteconf */
            $overwriteconf = $plan->get_setting('overwrite_conf');
            if ($overwriteconf->get_visibility()) {
                foreach (['course_fullname', 'course_shortname', 'course_startdate'] as $settingname) {
                    if ($plan->setting_exists($settingname)) {
                        $setting = $plan->get_setting($settingname);
                        $overwriteconf->add_dependency($setting, setting_dependency::DISABLED_FALSE,
                            array('defaultvalue' => $setting->get_value()));
                    }
                }
            }
        }
    }

    /**
     * Returns the default value to be used for a setting from the admin restore config
     *
     * @param string $config
     * @param backup_setting $setting
     * @return mixed
     */
    private static function get_setting_default($config, $setting) {
        $value = get_config('restore', $config);

        if (in_array($setting->get_name(), ['course_fullname', 'course_shortname', 'course_startdate']) &&
                $setting->get_ui() instanceof backup_setting_ui_defaultcustom) {
            // Special case - admin config settings course_fullname, etc. are boolean and the restore settings are strings.
            $value = (bool)$value;
            if ($value) {
                $attributes = $setting->get_ui()->get_attributes();
                $value = $attributes['customvalue'];
            }
        }

        if ($setting->get_ui() instanceof backup_setting_ui_select) {
            // Make sure the value is a valid option in the select element, otherwise just pick the first from the options list.
            // Example: enrolments dropdown may not have the "enrol_withusers" option because users info can not be restored.
            $options = array_keys($setting->get_ui()->get_values());
            if (!in_array($value, $options)) {
                $value = reset($options);
            }
        }

        return $value;
    }

    /**
     * Turn these settings on. No defaults from admin settings.
     *
     * @param restore_controller $controller
     * @param array $settings a map from admin config names to setting names (Config name => Setting name)
     */
    private static function force_enable_settings(restore_controller $controller, array $settings) {
        $plan = $controller->get_plan();
        foreach ($settings as $config => $settingname) {
            $value = true;
            if ($plan->setting_exists($settingname)) {
                $setting = $plan->get_setting($settingname);
                // We do not allow this setting to be locked for a duplicate function.
                if ($setting->get_status() !== base_setting::NOT_LOCKED) {
                    $setting->set_status(base_setting::NOT_LOCKED);
                }
                $setting->set_value($value);
                $setting->set_status(base_setting::LOCKED_BY_CONFIG);
            } else {
                $controller->log('Unknown setting: ' . $settingname, BACKUP::LOG_DEBUG);
            }
        }
    }

    /**
     * Sets the controller settings default values from the admin config.
     *
     * @param restore_controller $controller
     * @param array $settings a map from admin config names to setting names (Config name => Setting name)
     * @param boolean $uselocks whether "locked" admin settings should be honoured
     */
    private static function apply_admin_config_defaults(restore_controller $controller, array $settings, $uselocks) {
        $plan = $controller->get_plan();
        foreach ($settings as $config => $settingname) {
            if ($plan->setting_exists($settingname)) {
                $setting = $plan->get_setting($settingname);
                $value = self::get_setting_default($config, $setting);
                $locked = (get_config('restore',$config . '_locked') == true);

                // Use the original value when this is an import and the setting is unlocked.
                if ($controller->get_mode() == backup::MODE_IMPORT && $controller->get_interactive()) {
                    if (!$uselocks || !$locked) {
                        $value = $setting->get_value();
                    }
                }

                // We can only update the setting if it isn't already locked by config or permission.
                if ($setting->get_status() != base_setting::LOCKED_BY_CONFIG
                        && $setting->get_status() != base_setting::LOCKED_BY_PERMISSION
                        && $setting->get_ui()->is_changeable()) {
                    $setting->set_value($value);
                    if ($uselocks && $locked) {
                        $setting->set_status(base_setting::LOCKED_BY_CONFIG);
                    }
                }
            } else {
                $controller->log('Unknown setting: ' . $settingname, BACKUP::LOG_DEBUG);
            }
        }
    }
}
