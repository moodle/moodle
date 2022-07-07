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
 * Defines backup_root_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

    protected function converter_deps($main_setting, $converters) {
        foreach ($this->settings as $setting) {
            $name = $setting->get_name();
            if (in_array($name, $converters)) {
                $setvalue = convert_helper::export_converter_dependencies($name, $main_setting->get_name());
                if ($setvalue !== false) {
                    $setting->add_dependency($main_setting, $setvalue, array('value' => $name));
                }
            }
        }
    }

    /**
     * Define the common setting that any backup type will have
     */
    protected function define_settings() {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/helper/convert_helper.class.php');
        // Define filename setting
        $filename = new backup_filename_setting('filename', base_setting::IS_FILENAME, 'backup.mbz');
        $filename->set_ui_filename(get_string('filename', 'backup'), 'backup.mbz', array('size'=>50));
        $this->add_setting($filename);

        // Present converter settings only in type course and mode general backup operations.
        $converters = array();
        if ($this->plan->get_type() == backup::TYPE_1COURSE and $this->plan->get_mode() == backup::MODE_GENERAL) {
            $converters = convert_helper::available_converters(false);
            foreach ($converters as $cnv) {
                $formatcnv = new backup_users_setting($cnv, base_setting::IS_BOOLEAN, false);
                $formatcnv->set_ui(new backup_setting_ui_checkbox($formatcnv, get_string('backupformat'.$cnv, 'backup')));
                $this->add_setting($formatcnv);
            }
        }

        // Define users setting (keeping it on hand to define dependencies)
        $users = new backup_users_setting('users', base_setting::IS_BOOLEAN, true);
        $users->set_ui(new backup_setting_ui_checkbox($users, get_string('rootsettingusers', 'backup')));
        $this->add_setting($users);
        $this->converter_deps($users, $converters);

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
        $this->converter_deps($blocks, $converters);

        // Define files.
        $files = new backup_generic_setting('files', base_setting::IS_BOOLEAN, true);
        $files->set_ui(new backup_setting_ui_checkbox($files, get_string('rootsettingfiles', 'backup')));
        $this->add_setting($files);
        $this->converter_deps($files, $converters);

        // Define filters
        $filters = new backup_generic_setting('filters', base_setting::IS_BOOLEAN, true);
        $filters->set_ui(new backup_setting_ui_checkbox($filters, get_string('rootsettingfilters', 'backup')));
        $this->add_setting($filters);
        $this->converter_deps($filters, $converters);

        // Define comments (dependent of users)
        $comments = new backup_comments_setting('comments', base_setting::IS_BOOLEAN, true);
        $comments->set_ui(new backup_setting_ui_checkbox($comments, get_string('rootsettingcomments', 'backup')));
        $this->add_setting($comments);
        $users->add_dependency($comments);

        // Define badges (dependent of activities).
        $badges = new backup_badges_setting('badges', base_setting::IS_BOOLEAN, true);
        $badges->set_ui(new backup_setting_ui_checkbox($badges, get_string('rootsettingbadges', 'backup')));
        $this->add_setting($badges);
        $activities->add_dependency($badges);
        $users->add_dependency($badges);

        // Define calendar events.
        $events = new backup_calendarevents_setting('calendarevents', base_setting::IS_BOOLEAN, true);
        $events->set_ui(new backup_setting_ui_checkbox($events, get_string('rootsettingcalendarevents', 'backup')));
        $this->add_setting($events);

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
        // The restore does not process the grade histories when some activities are ignored.
        // So let's define a dependency to prevent false expectations from our users.
        $activities->add_dependency($gradehistories);

        // Define question bank inclusion setting.
        $questionbank = new backup_generic_setting('questionbank', base_setting::IS_BOOLEAN, true);
        $questionbank->set_ui(new backup_setting_ui_checkbox($questionbank, get_string('rootsettingquestionbank', 'backup')));
        $this->add_setting($questionbank);

        $groups = new backup_groups_setting('groups', base_setting::IS_BOOLEAN, true);
        $groups->set_ui(new backup_setting_ui_checkbox($groups, get_string('rootsettinggroups', 'backup')));
        $this->add_setting($groups);

        // Define competencies inclusion setting if competencies are enabled.
        $competencies = new backup_competencies_setting();
        $competencies->set_ui(new backup_setting_ui_checkbox($competencies, get_string('rootsettingcompetencies', 'backup')));
        $this->add_setting($competencies);

        // Define custom fields inclusion setting if custom fields are used.
        $customfields = new backup_customfield_setting('customfield', base_setting::IS_BOOLEAN, true);
        $customfields->set_ui(new backup_setting_ui_checkbox($customfields, get_string('rootsettingcustomfield', 'backup')));
        $this->add_setting($customfields);

        // Define content bank content inclusion setting.
        $contentbank = new backup_contentbankcontent_setting('contentbankcontent', base_setting::IS_BOOLEAN, true);
        $contentbank->set_ui(new backup_setting_ui_checkbox($contentbank, get_string('rootsettingcontentbankcontent', 'backup')));
        $this->add_setting($contentbank);

        // Define legacy file inclusion setting.
        $legacyfiles = new backup_generic_setting('legacyfiles', base_setting::IS_BOOLEAN, true);
        $legacyfiles->set_ui(new backup_setting_ui_checkbox($legacyfiles, get_string('rootsettinglegacyfiles', 'backup')));
        $this->add_setting($legacyfiles);
    }
}
