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
 * Defines restore_course_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * course task that provides all the properties and common steps to be performed
 * when one course is being restored
 *
 * TODO: Finish phpdocs
 */
class restore_course_task extends restore_task {

    protected $info; // info related to course gathered from backup file
    protected $contextid; // course context id

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $info, $plan = null) {
        $this->info = $info;
        parent::__construct($name, $plan);
    }

    /**
     * Course tasks have their own directory to read files
     */
    public function get_taskbasepath() {

        return $this->get_basepath() . '/course';
    }

    public function get_contextid() {
        return $this->contextid;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Define the task contextid (the course one)
        $this->contextid = context_course::instance($this->get_courseid())->id;

        // Executed conditionally if restoring to new course or if overwrite_conf setting is enabled
        if ($this->get_target() == backup::TARGET_NEW_COURSE || $this->get_setting_value('overwrite_conf') == true) {
            $this->add_step(new restore_course_structure_step('course_info', 'course.xml'));

            // Search reindexing (if enabled).
            if (\core_search\manager::is_indexing_enabled()) {
                $this->add_step(new restore_course_search_index('course_search_index'));
            }
        }

        $this->add_step(new restore_course_legacy_files_step('legacy_files'));

        // Deal with enrolment methods and user enrolments.
        if ($this->plan->get_mode() == backup::MODE_IMPORT) {
            // No need to do anything with enrolments.

        } else if (!$this->get_setting_value('users') or $this->plan->get_mode() == backup::MODE_HUB) {
            if ($this->get_setting_value('enrolments') == backup::ENROL_ALWAYS && $this->plan->get_mode() != backup::MODE_HUB) {
                // Restore enrolment methods.
                $this->add_step(new restore_enrolments_structure_step('course_enrolments', 'enrolments.xml'));
            } else if ($this->get_target() == backup::TARGET_CURRENT_ADDING or $this->get_target() == backup::TARGET_EXISTING_ADDING) {
                // Keep current enrolments unchanged.
            } else {
                // If no instances yet add default enrol methods the same way as when creating new course in UI.
                $this->add_step(new restore_default_enrolments_step('default_enrolments'));
            }

        } else {
            // Restore course enrolment data.
            $this->add_step(new restore_enrolments_structure_step('course_enrolments', 'enrolments.xml'));
        }

        // Populate groups, this must be done after enrolments because only enrolled users may be in groups.
        $this->add_step(new restore_groups_members_structure_step('create_groups_members', '../groups.xml'));

        // Restore course role assignments and overrides (internally will observe the role_assignments setting),
        // this must be done after all users are enrolled.
        $this->add_step(new restore_ras_and_caps_structure_step('course_ras_and_caps', 'roles.xml'));

        // Restore course filters (conditionally)
        if ($this->get_setting_value('filters')) {
            $this->add_step(new restore_filters_structure_step('course_filters', 'filters.xml'));
        }

        // Restore course comments (conditionally)
        if ($this->get_setting_value('comments')) {
            $this->add_step(new restore_comments_structure_step('course_comments', 'comments.xml'));
        }

        // Calendar events (conditionally)
        if ($this->get_setting_value('calendarevents')) {
            $this->add_step(new restore_calendarevents_structure_step('course_calendar', 'calendar.xml'));
        }

        // Course competencies.
        $this->add_step(new restore_course_competencies_structure_step('course_competencies', 'competencies.xml'));

        // Activity completion defaults.
        $this->add_step(new restore_completion_defaults_structure_step('course_completion_defaults', 'completiondefaults.xml'));

        // At the end, mark it as built
        $this->built = true;
    }

    /**
     * Define the contents in the course that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('course', 'summary');
        $contents[] = new restore_decode_content('event', 'description');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the course to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        // Link to the course main page (it also covers "&topic=xx" and "&week=xx"
        // because they don't become transformed (section number) in backup/restore.
        $rules[] = new restore_decode_rule('COURSEVIEWBYID',       '/course/view.php?id=$1',        'course');

        // A few other key course links.
        $rules[] = new restore_decode_rule('GRADEINDEXBYID',       '/grade/index.php?id=$1',        'course');
        $rules[] = new restore_decode_rule('GRADEREPORTINDEXBYID', '/grade/report/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('BADGESVIEWBYID',       '/badges/view.php?type=2&id=$1', 'course');
        $rules[] = new restore_decode_rule('USERINDEXVIEWBYID',    '/user/index.php?id=$1',         'course');

        return $rules;
    }

// Protected API starts here

    /**
     * Define the common setting that any restore course will have
     */
    protected function define_settings() {

        // Define overwrite_conf to decide if course configuration will be restored over existing one.
        $overwrite = new restore_course_overwrite_conf_setting('overwrite_conf', base_setting::IS_BOOLEAN, false);
        $overwrite->set_ui(new backup_setting_ui_select($overwrite, $overwrite->get_name(),
            array(1 => get_string('yes'), 0 => get_string('no'))));
        $overwrite->get_ui()->set_label(get_string('setting_overwrite_conf', 'backup'));
        if ($this->get_target() == backup::TARGET_NEW_COURSE) {
            $overwrite->set_value(true);
            $overwrite->set_status(backup_setting::LOCKED_BY_CONFIG);
            $overwrite->set_visibility(backup_setting::HIDDEN);
            $course = (object)['fullname' => null, 'shortname' => null, 'startdate' => null];
        } else {
            $course = get_course($this->get_courseid());
        }
        $this->add_setting($overwrite);

        $fullnamedefaultvalue = $this->get_info()->original_course_fullname;
        $fullname = new restore_course_defaultcustom_setting('course_fullname', base_setting::IS_TEXT, $fullnamedefaultvalue);
        $fullname->set_ui(new backup_setting_ui_defaultcustom($fullname, get_string('setting_course_fullname', 'backup'),
            ['customvalue' => $fullnamedefaultvalue, 'defaultvalue' => $course->fullname]));
        $this->add_setting($fullname);

        $shortnamedefaultvalue = $this->get_info()->original_course_shortname;
        $shortname = new restore_course_defaultcustom_setting('course_shortname', base_setting::IS_TEXT, $shortnamedefaultvalue);
        $shortname->set_ui(new backup_setting_ui_defaultcustom($shortname, get_string('setting_course_shortname', 'backup'),
            ['customvalue' => $shortnamedefaultvalue, 'defaultvalue' => $course->shortname]));
        $this->add_setting($shortname);

        $startdatedefaultvalue = $this->get_info()->original_course_startdate;
        $startdate = new restore_course_defaultcustom_setting('course_startdate', base_setting::IS_INTEGER, $startdatedefaultvalue);
        $startdate->set_ui(new backup_setting_ui_defaultcustom($startdate, get_string('setting_course_startdate', 'backup'),
            ['customvalue' => $startdatedefaultvalue, 'defaultvalue' => $course->startdate, 'type' => 'date_selector']));
        $this->add_setting($startdate);

        $keep_enrols = new restore_course_generic_setting('keep_roles_and_enrolments', base_setting::IS_BOOLEAN, false);
        $keep_enrols->set_ui(new backup_setting_ui_select($keep_enrols, $keep_enrols->get_name(), array(1=>get_string('yes'), 0=>get_string('no'))));
        $keep_enrols->get_ui()->set_label(get_string('setting_keep_roles_and_enrolments', 'backup'));
        if ($this->get_target() != backup::TARGET_CURRENT_DELETING and $this->get_target() != backup::TARGET_EXISTING_DELETING) {
            $keep_enrols->set_value(false);
            $keep_enrols->set_status(backup_setting::LOCKED_BY_CONFIG);
            $keep_enrols->set_visibility(backup_setting::HIDDEN);
        }
        $this->add_setting($keep_enrols);

        $keep_groups = new restore_course_generic_setting('keep_groups_and_groupings', base_setting::IS_BOOLEAN, false);
        $keep_groups->set_ui(new backup_setting_ui_select($keep_groups, $keep_groups->get_name(), array(1=>get_string('yes'), 0=>get_string('no'))));
        $keep_groups->get_ui()->set_label(get_string('setting_keep_groups_and_groupings', 'backup'));
        if ($this->get_target() != backup::TARGET_CURRENT_DELETING and $this->get_target() != backup::TARGET_EXISTING_DELETING) {
            $keep_groups->set_value(false);
            $keep_groups->set_status(backup_setting::LOCKED_BY_CONFIG);
            $keep_groups->set_visibility(backup_setting::HIDDEN);
        }
        $this->add_setting($keep_groups);

    }
}
