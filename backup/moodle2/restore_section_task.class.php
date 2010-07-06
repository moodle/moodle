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

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $info, $plan = null) {
        $this->info = $info;
        parent::__construct($name, $plan);
    }

    /**
     * Section tasks have their own directory to read files
     */
    public function get_taskbasepath() {

        return $this->get_basepath() . '/sections/section_' . $info->sectionid;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // TODO: Link all the section steps here

        // At the end, mark it as built
        $this->built = true;
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
        $this->add_setting($section_included);

        // Define section_userinfo. Dependent of:
        // - users root setting
        // - section_included setting
        $settingname = $settingprefix . 'userinfo';
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($info->settings[$settingname]) && $info->settings[$settingname]) { // Only enabled when available
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
