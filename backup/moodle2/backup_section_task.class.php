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
 * section task that provides all the properties and common steps to be performed
 * when one section is being backup
 *
 * TODO: Finish phpdocs
 */
class backup_section_task extends backup_task {

    protected $sectionid;

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $sectionid, $plan = null) {
        global $DB;

        // Check section exists
        if (!$section = $DB->get_record('course_sections', array('id' => $sectionid))) {
            throw new backup_task_exception('section_task_section_not_found', $sectionid);
        }

        $this->sectionid  = $sectionid;

        parent::__construct($name, $plan);
    }

    public function get_sectionid() {
        return $this->sectionid;
    }

    /**
     * Section tasks have their own directory to write files
     */
    public function get_taskbasepath() {

        return $this->get_basepath() . '/sections/section_' . $this->sectionid;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Set the backup::VAR_CONTEXTID setting to course context as far as next steps require that
        $coursectxid = get_context_instance(CONTEXT_COURSE, $this->get_courseid())->id;
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_CONTEXTID, base_setting::IS_INTEGER, $coursectxid));

        // Add some extra settings that related processors are going to need
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_SECTIONID, base_setting::IS_INTEGER, $this->sectionid));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_COURSEID, base_setting::IS_INTEGER, $this->get_courseid()));

        // Create the section directory
        $this->add_step(new create_taskbasepath_directory('create_section_directory'));

        // Create the section.xml common file (course_sections)
        $this->add_step(new backup_section_structure_step('section_commons', 'section.xml'));

        // Generate the inforef file (must be after ALL steps gathering annotations of ANY type)
        $this->add_step(new backup_inforef_structure_step('section_inforef', 'inforef.xml'));

        // Migrate the already exported inforef entries to final ones
        $this->add_step(new move_inforef_annotations_to_final('migrate_inforef'));

        // At the end, mark it as built
        $this->built = true;
    }

    /**
     * Exceptionally override the execute method, so, based in the section_included setting, we are able
     * to skip the execution of one task completely
     */
    public function execute() {

        // Find section_included_setting
        if (!$this->get_setting_value('included')) {
            $this->log('section skipped by _included setting', backup::LOG_DEBUG, $this->name);

        } else { // Setting tells us it's ok to execute
            parent::execute();
        }
    }

    /**
     * Specialisation that, first of all, looks for the setting within
     * the task with the the prefix added and later, delegates to parent
     * without adding anything
     */
    public function get_setting($name) {
        $namewithprefix = 'section_' . $this->sectionid . '_' . $name;
        $result = null;
        foreach ($this->settings as $key => $setting) {
            if ($setting->get_name() == $namewithprefix) {
                if ($result != null) {
                    throw new base_task_exception('multiple_settings_by_name_found', $namewithprefix);
                } else {
                    $result = $setting;
                }
            }
        }
        if ($result) {
            return $result;
        } else {
            // Fallback to parent
            return parent::get_setting($name);
        }
    }

// Protected API starts here

    /**
     * Define the common setting that any backup section will have
     */
    protected function define_settings() {
        global $DB;

        // All the settings related to this activity will include this prefix
        $settingprefix = 'section_' . $this->sectionid . '_';

        // All these are common settings to be shared by all sections

        $section = $DB->get_record('course_sections', array('id' => $this->sectionid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $section->course), '*', MUST_EXIST);

        // Define section_included (to decide if the whole task must be really executed)
        $settingname = $settingprefix . 'included';
        $section_included = new backup_section_included_setting($settingname, base_setting::IS_BOOLEAN, true);
        $section_included->get_ui()->set_label(get_section_name($course, $section));
        $this->add_setting($section_included);

        // Define section_userinfo. Dependent of:
        // - users root setting
        // - section_included setting
        $settingname = $settingprefix . 'userinfo';
        $section_userinfo = new backup_section_userinfo_setting($settingname, base_setting::IS_BOOLEAN, true);
        $section_userinfo->get_ui()->set_label(get_string('includeuserinfo','backup'));
        $this->add_setting($section_userinfo);
        // Look for "users" root setting
        $users = $this->plan->get_setting('users');
        $users->add_dependency($section_userinfo);
        // Look for "section_included" section setting
        $section_included->add_dependency($section_userinfo);
    }
}
