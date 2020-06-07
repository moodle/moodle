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
 * Defines restore_root_task class
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

        // Now make sure the user that is running the restore can actually access the course
        // before executing any other step (potentially performing permission checks)
        $this->add_step(new restore_fix_restorer_access_step('fix_restorer_access'));

        // If we haven't preloaded information, load all the included inforef records to temp_ids table
        $this->add_step(new restore_load_included_inforef_records('load_inforef_records'));

        // Load all the needed files to temp_ids table
        $this->add_step(new restore_load_included_files('load_file_records', 'files.xml'));

        // If we haven't preloaded information, load all the needed roles to temp_ids_table
        $this->add_step(new restore_load_and_map_roles('load_and_map_roles'));

        // If we haven't preloaded information and are restoring user info, load all the needed users to temp_ids table
        $this->add_step(new restore_load_included_users('load_user_records'));

        // If we haven't preloaded information and are restoring user info, process all those needed users
        // marking for create/map them as needed. Any problem here will cause exception as far as prechecks have
        // performed the same process so, it's not possible to have errors here
        $this->add_step(new restore_process_included_users('process_user_records'));

        // Unconditionally, create all the needed users calculated in the previous step
        $this->add_step(new restore_create_included_users('create_users'));

        // Unconditionally, load create all the needed groups and groupings
        $this->add_step(new restore_groups_structure_step('create_groups_and_groupings', 'groups.xml'));

        // Unconditionally, load create all the needed scales
        $this->add_step(new restore_scales_structure_step('create_scales', 'scales.xml'));

        // Unconditionally, load create all the needed outcomes
        $this->add_step(new restore_outcomes_structure_step('create_scales', 'outcomes.xml'));

        // If we haven't preloaded information, load all the needed categories and questions (reduced) to temp_ids_table
        $this->add_step(new restore_load_categories_and_questions('load_categories_and_questions'));

        // If we haven't preloaded information, process all the loaded categories and questions
        // marking them for creation/mapping as needed. Any problem here will cause exception
        // because this same process has been executed and reported by restore prechecks, so
        // it is not possible to have errors here.
        $this->add_step(new restore_process_categories_and_questions('process_categories_and_questions'));

        // Unconditionally, create and map all the categories and questions
        $this->add_step(new restore_create_categories_and_questions('create_categories_and_questions', 'questions.xml'));

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
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['users']) && $rootsettings['users']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $users = new restore_users_setting('users', base_setting::IS_BOOLEAN, $defaultvalue);
        $users->set_ui(new backup_setting_ui_checkbox($users, get_string('rootsettingusers', 'backup')));
        $users->get_ui()->set_changeable($changeable);
        $this->add_setting($users);

        // Restore enrolment methods.
        if ($changeable) {
            $options = [
                backup::ENROL_NEVER     => get_string('rootsettingenrolments_never', 'backup'),
                backup::ENROL_WITHUSERS => get_string('rootsettingenrolments_withusers', 'backup'),
                backup::ENROL_ALWAYS    => get_string('rootsettingenrolments_always', 'backup'),
            ];
            $enroldefault = backup::ENROL_WITHUSERS;
        } else {
            // Users can not be restored, simplify the dropdown.
            $options = [
                backup::ENROL_NEVER     => get_string('no'),
                backup::ENROL_ALWAYS    => get_string('yes')
            ];
            $enroldefault = backup::ENROL_NEVER;
        }
        $enrolments = new restore_users_setting('enrolments', base_setting::IS_INTEGER, $enroldefault);
        $enrolments->set_ui(new backup_setting_ui_select($enrolments, get_string('rootsettingenrolments', 'backup'),
            $options));
        $this->add_setting($enrolments);

        // Define role_assignments (dependent of users)
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['role_assignments']) && $rootsettings['role_assignments']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $roleassignments = new restore_role_assignments_setting('role_assignments', base_setting::IS_BOOLEAN, $defaultvalue);
        $roleassignments->set_ui(new backup_setting_ui_checkbox($roleassignments,get_string('rootsettingroleassignments', 'backup')));
        $roleassignments->get_ui()->set_changeable($changeable);
        $this->add_setting($roleassignments);
        $users->add_dependency($roleassignments);

        // Define activitites
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['activities']) && $rootsettings['activities']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $activities = new restore_activities_setting('activities', base_setting::IS_BOOLEAN, $defaultvalue);
        $activities->set_ui(new backup_setting_ui_checkbox($activities, get_string('rootsettingactivities', 'backup')));
        $activities->get_ui()->set_changeable($changeable);
        $this->add_setting($activities);

        // Define blocks
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['blocks']) && $rootsettings['blocks']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $blocks = new restore_generic_setting('blocks', base_setting::IS_BOOLEAN, $defaultvalue);
        $blocks->set_ui(new backup_setting_ui_checkbox($blocks, get_string('rootsettingblocks', 'backup')));
        $blocks->get_ui()->set_changeable($changeable);
        $this->add_setting($blocks);

        // Define filters
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['filters']) && $rootsettings['filters']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $filters = new restore_generic_setting('filters', base_setting::IS_BOOLEAN, $defaultvalue);
        $filters->set_ui(new backup_setting_ui_checkbox($filters, get_string('rootsettingfilters', 'backup')));
        $filters->get_ui()->set_changeable($changeable);
        $this->add_setting($filters);

        // Define comments (dependent of users)
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['comments']) && $rootsettings['comments']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $comments = new restore_comments_setting('comments', base_setting::IS_BOOLEAN, $defaultvalue);
        $comments->set_ui(new backup_setting_ui_checkbox($comments, get_string('rootsettingcomments', 'backup')));
        $comments->get_ui()->set_changeable($changeable);
        $this->add_setting($comments);
        $users->add_dependency($comments);

        // Define badges (dependent of activities).
        $defaultvalue = false;                      // Safer default.
        $changeable = false;
        if (isset($rootsettings['badges']) && $rootsettings['badges']) { // Only enabled when available.
            $defaultvalue = true;
            $changeable = true;
        }
        $badges = new restore_badges_setting('badges', base_setting::IS_BOOLEAN, $defaultvalue);
        $badges->set_ui(new backup_setting_ui_checkbox($badges, get_string('rootsettingbadges', 'backup')));
        $badges->get_ui()->set_changeable($changeable);
        $this->add_setting($badges);
        $activities->add_dependency($badges);
        $users->add_dependency($badges);

        // Define Calendar events.
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['calendarevents']) && $rootsettings['calendarevents']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $events = new restore_calendarevents_setting('calendarevents', base_setting::IS_BOOLEAN, $defaultvalue);
        $events->set_ui(new backup_setting_ui_checkbox($events, get_string('rootsettingcalendarevents', 'backup')));
        $events->get_ui()->set_changeable($changeable);
        $this->add_setting($events);

        // Define completion (dependent of users)
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['userscompletion']) && $rootsettings['userscompletion']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $completion = new restore_userscompletion_setting('userscompletion', base_setting::IS_BOOLEAN, $defaultvalue);
        $completion->set_ui(new backup_setting_ui_checkbox($completion, get_string('rootsettinguserscompletion', 'backup')));
        $completion->get_ui()->set_changeable($changeable);
        $this->add_setting($completion);
        $users->add_dependency($completion);

        // Define logs (dependent of users)
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['logs']) && $rootsettings['logs']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $logs = new restore_logs_setting('logs', base_setting::IS_BOOLEAN, $defaultvalue);
        $logs->set_ui(new backup_setting_ui_checkbox($logs, get_string('rootsettinglogs', 'backup')));
        $logs->get_ui()->set_changeable($changeable);
        $this->add_setting($logs);
        $users->add_dependency($logs);

        // Define grade_histories (dependent of users)
        $defaultvalue = false;                      // Safer default
        $changeable = false;
        if (isset($rootsettings['grade_histories']) && $rootsettings['grade_histories']) { // Only enabled when available
            $defaultvalue = true;
            $changeable = true;
        }
        $gradehistories = new restore_grade_histories_setting('grade_histories', base_setting::IS_BOOLEAN, $defaultvalue);
        $gradehistories->set_ui(new backup_setting_ui_checkbox($gradehistories, get_string('rootsettinggradehistories', 'backup')));
        $gradehistories->get_ui()->set_changeable($changeable);
        $this->add_setting($gradehistories);
        $users->add_dependency($gradehistories);

        // The restore does not process the grade histories when some activities are ignored.
        // So let's define a dependency to prevent false expectations from our users.
        $activities->add_dependency($gradehistories);

        // Define groups and groupings.
        $defaultvalue = false;
        $changeable = false;
        if (isset($rootsettings['groups']) && $rootsettings['groups']) { // Only enabled when available.
            $defaultvalue = true;
            $changeable = true;
        } else if (!isset($rootsettings['groups'])) {
            // It is likely this is an older backup that does not contain information on the group setting,
            // in which case groups should be restored and this setting can be changed.
            $defaultvalue = true;
            $changeable = true;
        }
        $groups = new restore_groups_setting('groups', base_setting::IS_BOOLEAN, $defaultvalue);
        $groups->set_ui(new backup_setting_ui_checkbox($groups, get_string('rootsettinggroups', 'backup')));
        $groups->get_ui()->set_changeable($changeable);
        $this->add_setting($groups);

        // Competencies restore setting. Show when competencies is enabled and the setting is available.
        $hascompetencies = !empty($rootsettings['competencies']);
        $competencies = new restore_competencies_setting($hascompetencies);
        $competencies->set_ui(new backup_setting_ui_checkbox($competencies, get_string('rootsettingcompetencies', 'backup')));
        $this->add_setting($competencies);

        $customfields = new restore_customfield_setting('customfields', base_setting::IS_BOOLEAN, $defaultvalue);
        $customfields->set_ui(new backup_setting_ui_checkbox($customfields, get_string('rootsettingcustomfield', 'backup')));
        $this->add_setting($customfields);

        // Define Content bank content.
        $defaultvalue = false;
        $changeable = false;
        if (isset($rootsettings['contentbankcontent']) && $rootsettings['contentbankcontent']) { // Only enabled when available.
            $defaultvalue = true;
            $changeable = true;
        }
        $contents = new restore_contentbankcontent_setting('contentbankcontent', base_setting::IS_BOOLEAN, $defaultvalue);
        $contents->set_ui(new backup_setting_ui_checkbox($contents, get_string('rootsettingcontentbankcontent', 'backup')));
        $contents->get_ui()->set_changeable($changeable);
        $this->add_setting($contents);
    }
}
