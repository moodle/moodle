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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Start task that provides all the settings common to all restores and other initial steps
 *
 * TODO: Finish phpdocs
 */
class restore_root_task extends restore_task {

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Conditionally create the temp table (can exist from prechecks) and delete old stuff
        $this->add_step(new restore_create_and_clean_temp_stuff('create_and_clean_temp_stuff'));

        // If we haven't preloaded information, load all the included inforef records to temp_ids table
        $this->add_step(new restore_load_included_inforef_records('load_inforef_records'));

        // If we haven't preloaded information, load all the needed roles to temp_ids_table
        $this->add_step(new restore_load_and_map_roles('load_and_map_roles'));

        // If we haven't preloaded information and are restoring user info, load all the needed users to temp_ids table
        $this->add_step(new restore_load_included_users('load_user_records'));

        // If we haven't preloaded information and are restoring user info, process all those needed users
        // creating/mapping them as needed. Any problem here will cause exception as far as prechecks have
        // performed the same process so, it's not possible to have errors here
        $this->add_step(new restore_process_included_users('process_user_records'));

        // Load all the needed files to temp_ids table
        $this->add_step(new restore_load_included_files('load_file_records', 'files.xml'));

        // Unconditionally, create all the needed users calculated in the previous step
        $this->add_step(new restore_create_included_users('create_users'));

        // Unconditionally, load create all the needed groups and groupings
        $this->add_step(new restore_groups_structure_step('create_groups_and_groupings', 'groups.xml'));

        // Unconditionally, load create all the needed scales
        $this->add_step(new restore_scales_structure_step('create_scales', 'scales.xml'));

        // Unconditionally, load create all the needed outcomes
        $this->add_step(new restore_outcomes_structure_step('create_scales', 'outcomes.xml'));

        // At the end, mark it as built
        $this->built = true;
    }

// Protected API starts here

    /**
     * Define the common setting that any restore type will have
     */
    protected function define_settings() {

        // Load all the root settings found in backup file from controller
        $rootsettings = $this->get_info()->root_settings;

        // Define users setting (keeping it on hand to define dependencies)
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['users']) && $rootsettings['users']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $users = new restore_users_setting('users', base_setting::IS_BOOLEAN, $defaultvalue);
        $users->set_ui(new backup_setting_ui_select($users, $users->get_name(), $selectvalues));
        $this->add_setting($users);

        // Define role_assignments (dependent of users)
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['role_assignments']) && $rootsettings['role_assignments']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $roleassignments = new restore_role_assignments_setting('role_assignments', base_setting::IS_BOOLEAN, $defaultvalue);
        $roleassignments->set_ui(new backup_setting_ui_select($roleassignments, $roleassignments->get_name(), $selectvalues));
        $this->add_setting($roleassignments);
        $users->add_dependency($roleassignments);

        // Define user_files (dependent of users)
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['user_files']) && $rootsettings['user_files']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $userfiles = new restore_user_files_setting('user_files', base_setting::IS_BOOLEAN, $defaultvalue);
        $userfiles->set_ui(new backup_setting_ui_select($userfiles, $userfiles->get_name(), $selectvalues));
        $this->add_setting($userfiles);
        $users->add_dependency($userfiles);

        // Define activitites
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['activities']) && $rootsettings['activities']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $activities = new restore_activities_setting('activities', base_setting::IS_BOOLEAN, $defaultvalue);
        $activities->set_ui(new backup_setting_ui_select($activities, $activities->get_name(), $selectvalues));
        $this->add_setting($activities);

        // Define blocks
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['blocks']) && $rootsettings['blocks']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $blocks = new restore_generic_setting('blocks', base_setting::IS_BOOLEAN, $defaultvalue);
        $blocks->set_ui(new backup_setting_ui_select($blocks, $blocks->get_name(), $selectvalues));
        $this->add_setting($blocks);

        // Define filters
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['filters']) && $rootsettings['filters']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $filters = new restore_generic_setting('filters', base_setting::IS_BOOLEAN, $defaultvalue);
        $filters->set_ui(new backup_setting_ui_select($filters, $filters->get_name(), $selectvalues));
        $this->add_setting($filters);

        // Define comments (dependent of users)
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['comments']) && $rootsettings['comments']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $comments = new restore_comments_setting('comments', base_setting::IS_BOOLEAN, $defaultvalue);
        $comments->set_ui(new backup_setting_ui_select($comments, $comments->get_name(), $selectvalues));
        $this->add_setting($comments);
        $users->add_dependency($comments);

        // Define completion (dependent of users)
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['userscompletion']) && $rootsettings['userscompletion']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $completion = new restore_userscompletion_setting('userscompletion', base_setting::IS_BOOLEAN, $defaultvalue);
        $completion->set_ui(new backup_setting_ui_select($completion, $completion->get_name(), $selectvalues));
        $this->add_setting($completion);
        $users->add_dependency($completion);

        // Define logs (dependent of users)
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['logs']) && $rootsettings['logs']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $logs = new restore_logs_setting('logs', base_setting::IS_BOOLEAN, $defaultvalue);
        $logs->set_ui(new backup_setting_ui_select($logs, $logs->get_name(), $selectvalues));
        $this->add_setting($logs);
        $users->add_dependency($logs);

        // Define grade_histories (dependent of users)
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($rootsettings['grade_histories']) && $rootsettings['grade_histories']) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $gradehistories = new restore_grade_histories_setting('grade_histories', base_setting::IS_BOOLEAN, $defaultvalue);
        $gradehistories->set_ui(new backup_setting_ui_select($gradehistories, $gradehistories->get_name(), $selectvalues));
        $this->add_setting($gradehistories);
        $users->add_dependency($gradehistories);
    }
}
