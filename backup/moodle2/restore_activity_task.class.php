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
 * abstract activity task that provides all the properties and common tasks to be performed
 * when one activity is being restored
 *
 * TODO: Finish phpdocs
 */
abstract class restore_activity_task extends restore_task {

    protected $info; // info related to activity gathered from backup file

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $info, $plan = null) {

        $this->info = $info;
        parent::__construct($name, $plan);
    }

    /**
     * Activity tasks have their own directory to read files
     */
    public function get_taskbasepath() {
        return $this->get_basepath() . '/activities/' . $this->info->modulename . '_' . $this->info->moduleid;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // If we have decided not to restore activities, prevent anything to be built
        if (!$this->get_setting_value('activities')) {
            $this->built = true;
            return;
        }

        // TODO: Link all the activity steps here

        // At the end, mark it as built
        $this->built = true;
    }

    /**
     * Exceptionally override the execute method, so, based in the activity_included setting, we are able
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
        $namewithprefix = $this->info->modulename . '_' . $this->info->moduleid . '_' . $name;
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
     * Define the common setting that any restore activity will have
     */
    protected function define_settings() {

        // All the settings related to this activity will include this prefix
        $settingprefix = $this->info->modulename . '_' . $this->info->moduleid . '_';

        // All these are common settings to be shared by all activities

        // Define activity_include (to decide if the whole task must be really executed)
        // Dependent of:
        // - activities root setting
        // - section_included setting (if exists)
        $settingname = $settingprefix . 'included';
        $activity_included = new restore_activity_generic_setting($settingname, base_setting::IS_BOOLEAN, true);
        $this->add_setting($activity_included);
        // Look for "activities" root setting
        $activities = $this->plan->get_setting('activities');
        $activities->add_dependency($activity_included);
        // Look for "section_included" section setting (if exists)
        $settingname = 'section_' . $this->info->sectionid . '_included';
        if ($this->plan->setting_exists($settingname)) {
            $section_included = $this->plan->get_setting($settingname);
            $section_included->add_dependency($activity_included);
        }

        // Define activity_userinfo. Dependent of:
        // - users root setting
        // - section_userinfo setting (if exists)
        // - activity_included setting
        $settingname = $settingprefix . 'userinfo';
        $selectvalues = array(0=>get_string('no')); // Safer options
        $defaultvalue = false;                      // Safer default
        if (isset($info->settings[$settingname]) && $info->settings[$settingname]) { // Only enabled when available
            $selectvalues = array(1=>get_string('yes'), 0=>get_string('no'));
            $defaultvalue = true;
        }
        $activity_userinfo = new restore_activity_userinfo_setting($settingname, base_setting::IS_BOOLEAN, $defaultvalue);
        $activity_userinfo->set_ui(new backup_setting_ui_select($activity_userinfo, get_string('includeuserinfo','backup'), $selectvalues));
        $this->add_setting($activity_userinfo);
        // Look for "users" root setting
        $users = $this->plan->get_setting('users');
        $users->add_dependency($activity_userinfo);
        // Look for "section_userinfo" section setting (if exists)
        $settingname = 'section_' . $this->info->sectionid . '_userinfo';
        if ($this->plan->setting_exists($settingname)) {
            $section_userinfo = $this->plan->get_setting($settingname);
            $section_userinfo->add_dependency($activity_userinfo);
        }
        // Look for "activity_included" setting
        $activity_included->add_dependency($activity_userinfo);

        // End of common activity settings, let's add the particular ones
        $this->define_my_settings();
    }

    /**
     * Define (add) particular settings that each activity can have
     */
    abstract protected function define_my_settings();

    /**
     * Define (add) particular steps that each activity can have
     */
    abstract protected function define_my_steps();

     /**
     * Code the transformations to perform by the activity in
     * order to get encoded transformed back to working links
     */
    abstract static public function decode_content_links($content);

}
