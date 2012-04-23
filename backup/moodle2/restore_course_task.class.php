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
        $this->contextid = get_context_instance(CONTEXT_COURSE, $this->get_courseid())->id;

        // Executed conditionally if restoring to new course or if overwrite_conf setting is enabled
        if ($this->get_target() == backup::TARGET_NEW_COURSE || $this->get_setting_value('overwrite_conf') == true) {
            $this->add_step(new restore_course_structure_step('course_info', 'course.xml'));
        }

        // Restore course role assignments and overrides (internally will observe the role_assignments setting)
        $this->add_step(new restore_ras_and_caps_structure_step('course_ras_and_caps', 'roles.xml'));

        // Restore course enrolments (plugins and membership). Conditionally prevented for any IMPORT/HUB operation
        if ($this->plan->get_mode() != backup::MODE_IMPORT && $this->plan->get_mode() != backup::MODE_HUB) {
            $this->add_step(new restore_enrolments_structure_step('course_enrolments', 'enrolments.xml'));
        }

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

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the course to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('COURSEVIEWBYID', '/course/view.php?id=$1', 'course');

        return $rules;

    }

// Protected API starts here

    /**
     * Define the common setting that any restore course will have
     */
    protected function define_settings() {

        //$name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED
        $fullname = new restore_course_generic_text_setting('course_fullname', base_setting::IS_TEXT, $this->get_info()->original_course_fullname);
        $fullname->get_ui()->set_label(get_string('setting_course_fullname', 'backup'));
        $this->add_setting($fullname);

        $shortname = new restore_course_generic_text_setting('course_shortname', base_setting::IS_TEXT, $this->get_info()->original_course_shortname);
        $shortname->get_ui()->set_label(get_string('setting_course_shortname', 'backup'));
        $this->add_setting($shortname);

        $startdate = new restore_course_generic_text_setting('course_startdate', base_setting::IS_INTEGER, $this->get_info()->original_course_startdate);
        $startdate->set_ui(new backup_setting_ui_dateselector($startdate, get_string('setting_course_startdate', 'backup')));
        $this->add_setting($startdate);

        // Define overwrite_conf to decide if course configuration will be restored over existing one
        $overwrite = new restore_course_overwrite_conf_setting('overwrite_conf', base_setting::IS_BOOLEAN, false);
        $overwrite->set_ui(new backup_setting_ui_select($overwrite, $overwrite->get_name(), array(1=>get_string('yes'), 0=>get_string('no'))));
        $overwrite->get_ui()->set_label(get_string('setting_overwriteconf', 'backup'));
        if ($this->get_target() == backup::TARGET_NEW_COURSE) {
            $overwrite->set_value(true);
            $overwrite->set_status(backup_setting::LOCKED_BY_CONFIG);
            $overwrite->set_visibility(backup_setting::HIDDEN);
        }
        $this->add_setting($overwrite);

    }
}
