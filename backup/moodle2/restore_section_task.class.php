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
 * when one section is being restored
 *
 * TODO: Finish phpdocs
 */
class restore_section_task extends restore_task {

    protected $info; // info related to section gathered from backup file
    protected $contextid; // course context id
    protected $sectionid; // new (target) id of the course section

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $info, $plan = null) {
        $this->info = $info;
        $this->sectionid = 0;
        parent::__construct($name, $plan);
    }

    /**
     * Section tasks have their own directory to read files
     */
    public function get_taskbasepath() {

        return $this->get_basepath() . '/sections/section_' . $this->info->sectionid;
    }

    public function set_sectionid($sectionid) {
        $this->sectionid = $sectionid;
    }

    public function get_contextid() {
        return $this->contextid;
    }

    public function get_sectionid() {
        return $this->sectionid;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Define the task contextid (the course one)
        $this->contextid = get_context_instance(CONTEXT_COURSE, $this->get_courseid())->id;

        // We always try to restore as much info from sections as possible, no matter of the type
        // of restore (new, existing, deleting, import...). MDL-27764
        $this->add_step(new restore_section_structure_step('course_info', 'section.xml'));

        // At the end, mark it as built
        $this->built = true;
    }

    /**
     * Exceptionally override the execute method, so, based in the section_included setting, we are able
     * to skip the execution of one task completely
     */
    public function execute() {

        // Find activity_included_setting
        if (!$this->get_setting_value('included')) {
            $this->log('activity skipped by _included setting', backup::LOG_DEBUG, $this->name);

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
        $namewithprefix = 'section_' . $this->info->sectionid . '_' . $name;
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

    /**
     * Define the contents in the course that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('course_sections', 'summary', 'course_section');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the sections to be executed by the link decoder
     */
    static public function define_decode_rules() {
        return array();
    }

// Protected API starts here

    /**
     * Define the common setting that any restore section will have
     */
    protected function define_settings() {

        // All the settings related to this activity will include this prefix
        $settingprefix = 'section_' . $this->info->sectionid . '_';

        // All these are common settings to be shared by all sections

        // Define section_included (to decide if the whole task must be really executed)
        $settingname = $settingprefix . 'included';
        $section_included = new restore_section_included_setting($settingname, base_setting::IS_BOOLEAN, true);
        if (is_number($this->info->title)) {
            $label = get_string('includesection', 'backup', $this->info->title);
        } else {
            $label = $this->info->title;
        }
        $section_included->get_ui()->set_label($label);
        $this->add_setting($section_included);

        // Define section_userinfo. Dependent of:
        // - users root setting
        // - section_included setting
        $settingname = $settingprefix . 'userinfo';
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($this->info->settings[$settingname]) && $this->info->settings[$settingname]) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $section_userinfo = new restore_section_userinfo_setting($settingname, base_setting::IS_BOOLEAN, $defaultvalue);
        $section_userinfo->set_ui(new backup_setting_ui_select($section_userinfo, get_string('includeuserinfo','backup'), $selectvalues));
        $this->add_setting($section_userinfo);
        // Look for "users" root setting
        $users = $this->plan->get_setting('users');
        $users->add_dependency($section_userinfo);
        // Look for "section_included" section setting
        $section_included->add_dependency($section_userinfo);
    }
}
