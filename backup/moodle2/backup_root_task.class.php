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
 * Start task that provides all the settings common to all backups and some initialization steps
 *
 * TODO: Finish phpdocs
 */
class backup_root_task extends backup_task {

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Add all the steps needed to prepare any moodle2 backup to work
        $this->add_step(new create_and_clean_temp_stuff('create_and_clean_temp_stuff'));

        $this->built = true;
    }

// Protected API starts here

    /**
     * Define the common setting that any backup type will have
     */
    protected function define_settings() {

        // Define filename setting
        $filename = new backup_filename_setting('filename', base_setting::IS_FILENAME, 'backup.mbz');
        $filename->set_ui(get_string('filename', 'backup'), 'backup.mbz', array('size'=>50));
        $this->add_setting($filename);

        // Define users setting (keeping it on hand to define dependencies)
        $users = new backup_users_setting('users', base_setting::IS_BOOLEAN, true);
        $users->set_ui(new backup_setting_ui_checkbox($users, get_string('rootsettingusers', 'backup')));
        $this->add_setting($users);

        // Define anonymize (dependent of users)
        $anonymize = new backup_anonymize_setting('anonymize', base_setting::IS_BOOLEAN, false);
        $anonymize->set_ui(new backup_setting_ui_checkbox($anonymize, get_string('rootsettinganonymize', 'backup')));
        $this->add_setting($anonymize);
        $users->add_dependency($anonymize);

        // Define role_assignments (dependent of users)
        $roleassignments = new backup_role_assignments_setting('role_assignments', base_setting::IS_BOOLEAN, true);
        $roleassignments->set_ui(new backup_setting_ui_checkbox($roleassignments, get_string('rootsettingroleassignments', 'backup')));
        $this->add_setting($roleassignments);
        $users->add_dependency($roleassignments);

        // Define activities
        $activities = new backup_activities_setting('activities', base_setting::IS_BOOLEAN, true);
        $activities->set_ui(new backup_setting_ui_checkbox($activities, get_string('rootsettingactivities', 'backup')));
        $this->add_setting($activities);

        // Define blocks
        $blocks = new backup_generic_setting('blocks', base_setting::IS_BOOLEAN, true);
        $blocks->set_ui(new backup_setting_ui_checkbox($blocks, get_string('rootsettingblocks', 'backup')));
        $this->add_setting($blocks);

        // Define filters
        $filters = new backup_generic_setting('filters', base_setting::IS_BOOLEAN, true);
        $filters->set_ui(new backup_setting_ui_checkbox($filters, get_string('rootsettingfilters', 'backup')));
        $this->add_setting($filters);

        // Define comments (dependent of users)
        $comments = new backup_comments_setting('comments', base_setting::IS_BOOLEAN, true);
        $comments->set_ui(new backup_setting_ui_checkbox($comments, get_string('rootsettingcomments', 'backup')));
        $this->add_setting($comments);
        $users->add_dependency($comments);

        // Define calendar events (dependent of users)
        $events = new backup_calendarevents_setting('calendarevents', base_setting::IS_BOOLEAN, true);
        $events->set_ui(new backup_setting_ui_checkbox($events, get_string('rootsettingcalendarevents', 'backup')));
        $this->add_setting($events);
        $users->add_dependency($events);

        // Define completion (dependent of users)
        $completion = new backup_userscompletion_setting('userscompletion', base_setting::IS_BOOLEAN, true);
        $completion->set_ui(new backup_setting_ui_checkbox($completion, get_string('rootsettinguserscompletion', 'backup')));
        $this->add_setting($completion);
        $users->add_dependency($completion);

        // Define logs (dependent of users)
        $logs = new backup_logs_setting('logs', base_setting::IS_BOOLEAN, true);
        $logs->set_ui(new backup_setting_ui_checkbox($logs, get_string('rootsettinglogs', 'backup')));
        $this->add_setting($logs);
        $users->add_dependency($logs);

        // Define grade_histories (dependent of users)
        $gradehistories = new backup_generic_setting('grade_histories', base_setting::IS_BOOLEAN, true);
        $gradehistories->set_ui(new backup_setting_ui_checkbox($gradehistories, get_string('rootsettinggradehistories', 'backup')));
        $this->add_setting($gradehistories);
        $users->add_dependency($gradehistories);
    }
}
