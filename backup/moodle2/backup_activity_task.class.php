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
 * Defines backup_activity_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides all the settings and steps to perform one complete backup of the activity
 *
 * Activities are supposed to provide the subclass of this class in their file
 * mod/MODULENAME/backup/moodle2/backup_MODULENAME_activity_task.class.php
 * The expected name of the subclass is backup_MODULENAME_activity_task
 */
abstract class backup_activity_task extends backup_task {

    protected $moduleid;
    protected $sectionid;
    protected $modulename;
    protected $activityid;
    protected $contextid;

    /**
     * Constructor - instantiates one object of this class
     *
     * @param string $name the task identifier
     * @param int $moduleid course module id (id in course_modules table)
     * @param backup_plan|null $plan the backup plan instance this task is part of
     */
    public function __construct($name, $moduleid, $plan = null) {

        // Check moduleid exists
        if (!$coursemodule = get_coursemodule_from_id(false, $moduleid)) {
            throw new backup_task_exception('activity_task_coursemodule_not_found', $moduleid);
        }
        // Check activity supports this moodle2 backup format
        if (!plugin_supports('mod', $coursemodule->modname, FEATURE_BACKUP_MOODLE2)) {
            throw new backup_task_exception('activity_task_activity_lacks_moodle2_backup_support', $coursemodule->modname);
        }

        $this->moduleid   = $moduleid;
        $this->sectionid  = $coursemodule->section;
        $this->modulename = $coursemodule->modname;
        $this->activityid = $coursemodule->instance;
        $this->contextid  = context_module::instance($this->moduleid)->id;

        parent::__construct($name, $plan);
    }

    /**
     * @return int the course module id (id in the course_modules table)
     */
    public function get_moduleid() {
        return $this->moduleid;
    }

    /**
     * @return int the course section id (id in the course_sections table)
     */
    public function get_sectionid() {
        return $this->sectionid;
    }

    /**
     * @return string the name of the module, eg 'workshop' (from the modules table)
     */
    public function get_modulename() {
        return $this->modulename;
    }

    /**
     * @return int the id of the activity instance (id in the activity's instances table)
     */
    public function get_activityid() {
        return $this->activityid;
    }

    /**
     * @return int the id of the associated CONTEXT_MODULE instance
     */
    public function get_contextid() {
        return $this->contextid;
    }

    /**
     * @return string full path to the directory where this task writes its files
     */
    public function get_taskbasepath() {
        return $this->get_basepath() . '/activities/' . $this->modulename . '_' . $this->moduleid;
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // If we have decided not to backup activities, prevent anything to be built
        if (!$this->get_setting_value('activities')) {
            $this->built = true;
            return;
        }

        // Add some extra settings that related processors are going to need
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_MODID, base_setting::IS_INTEGER, $this->moduleid));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_COURSEID, base_setting::IS_INTEGER, $this->get_courseid()));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_SECTIONID, base_setting::IS_INTEGER, $this->sectionid));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_MODNAME, base_setting::IS_FILENAME, $this->modulename));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_ACTIVITYID, base_setting::IS_INTEGER, $this->activityid));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_CONTEXTID, base_setting::IS_INTEGER, $this->contextid));

        // Create the activity directory
        $this->add_step(new create_taskbasepath_directory('create_activity_directory'));

        // Generate the module.xml file, containing general information for the
        // activity and from its related course_modules record and availability
        $this->add_step(new backup_module_structure_step('module_info', 'module.xml'));

        // Annotate the groups used in already annotated groupings if groups are to be backed up.
        if ($this->get_setting_value('groups')) {
            $this->add_step(new backup_annotate_groups_from_groupings('annotate_groups'));
        }

        // Here we add all the common steps for any activity and, in the point of interest
        // we call to define_my_steps() is order to get the particular ones inserted in place.
        $this->define_my_steps();

        // Generate the roles file (optionally role assignments and always role overrides)
        $this->add_step(new backup_roles_structure_step('activity_roles', 'roles.xml'));

        // Generate the filter file (conditionally)
        if ($this->get_setting_value('filters')) {
            $this->add_step(new backup_filters_structure_step('activity_filters', 'filters.xml'));
        }

        // Generate the comments file (conditionally)
        if ($this->get_setting_value('comments')) {
            $this->add_step(new backup_comments_structure_step('activity_comments', 'comments.xml'));
        }

        // Generate the userscompletion file (conditionally)
        if ($this->get_setting_value('userscompletion')) {
            $this->add_step(new backup_userscompletion_structure_step('activity_userscompletion', 'completion.xml'));
        }

        // Generate the logs file (conditionally)
        if ($this->get_setting_value('logs')) {
            // Legacy logs.
            $this->add_step(new backup_activity_logs_structure_step('activity_logs', 'logs.xml'));
            // New log stores.
            $this->add_step(new backup_activity_logstores_structure_step('activity_logstores', 'logstores.xml'));
        }

        // Generate the calendar events file (conditionally)
        if ($this->get_setting_value('calendarevents')) {
            $this->add_step(new backup_calendarevents_structure_step('activity_calendar', 'calendar.xml'));
        }

        // Fetch all the activity grade items and put them to backup_ids
        $this->add_step(new backup_activity_grade_items_to_ids('fetch_activity_grade_items'));

        // Generate the grades file
        $this->add_step(new backup_activity_grades_structure_step('activity_grades', 'grades.xml'));

        // Generate the grading file (conditionally)
        $this->add_step(new backup_activity_grading_structure_step('activity_grading', 'grading.xml'));

        // Generate the grade history file. The setting 'grade_histories' is handled in the step.
        $this->add_step(new backup_activity_grade_history_structure_step('activity_grade_history', 'grade_history.xml'));

        // Generate the competency file.
        $this->add_step(new backup_activity_competencies_structure_step('activity_competencies', 'competencies.xml'));

        // Annotate the scales used in already annotated outcomes
        $this->add_step(new backup_annotate_scales_from_outcomes('annotate_scales'));

        // NOTE: Historical grade information is saved completely at course level only (see 1.9)
        // not per activity nor per selected activities (all or nothing).

        // Generate the inforef file (must be after ALL steps gathering annotations of ANY type)
        $this->add_step(new backup_inforef_structure_step('activity_inforef', 'inforef.xml'));

        // Migrate the already exported inforef entries to final ones
        $this->add_step(new move_inforef_annotations_to_final('migrate_inforef'));

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
            $this->plan->set_excluding_activities();
        } else { // Setting tells us it's ok to execute
            parent::execute();
        }
    }


    /**
     * Tries to look for the instance specific setting value, task specific setting value or the
     * common plan setting value - in that order
     *
     * @param string $name the name of the setting
     * @return mixed|null the value of the setting or null if not found
     */
    public function get_setting($name) {
        $namewithprefix = $this->modulename . '_' . $this->moduleid . '_' . $name;
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
     * Defines the common setting that any backup activity will have
     */
    protected function define_settings() {
        global $CFG;
        require_once($CFG->libdir.'/questionlib.php');

        // All the settings related to this activity will include this prefix
        $settingprefix = $this->modulename . '_' . $this->moduleid . '_';

        // All these are common settings to be shared by all activities

        // Define activity_include (to decide if the whole task must be really executed)
        // Dependent of:
        // - activities root setting
        // - section_included setting (if exists)
        $settingname = $settingprefix . 'included';
        $activity_included = new backup_activity_generic_setting($settingname, base_setting::IS_BOOLEAN, true);
        $activity_included->get_ui()->set_icon(new image_icon('icon', get_string('pluginname', $this->modulename),
            $this->modulename, array('class' => 'iconlarge icon-post')));
        $this->add_setting($activity_included);
        // Look for "activities" root setting
        $activities = $this->plan->get_setting('activities');
        $activities->add_dependency($activity_included);

        if (question_module_uses_questions($this->modulename)) {
            $questionbank = $this->plan->get_setting('questionbank');
            $questionbank->add_dependency($activity_included);
        }

        // Look for "section_included" section setting (if exists)
        $settingname = 'section_' . $this->sectionid . '_included';
        if ($this->plan->setting_exists($settingname)) {
            $section_included = $this->plan->get_setting($settingname);
            $section_included->add_dependency($activity_included);
        }

        // Define activity_userinfo. Dependent of:
        // - users root setting
        // - section_userinfo setting (if exists)
        // - activity_included setting
        $settingname = $settingprefix . 'userinfo';
        $activity_userinfo = new backup_activity_userinfo_setting($settingname, base_setting::IS_BOOLEAN, true);
        //$activity_userinfo->get_ui()->set_label(get_string('includeuserinfo','backup'));
        $activity_userinfo->get_ui()->set_label('-');
        $this->add_setting($activity_userinfo);
        // Look for "users" root setting
        $users = $this->plan->get_setting('users');
        $users->add_dependency($activity_userinfo);
        // Look for "section_userinfo" section setting (if exists)
        $settingname = 'section_' . $this->sectionid . '_userinfo';
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
     * Defines activity specific settings to be added to the common ones
     *
     * This method is called from {@link self::define_settings()}. The activity module
     * author may use it to define additional settings that influence the execution of
     * the backup.
     *
     * Most activities just leave the method empty.
     *
     * @see self::define_settings() for the example how to define own settings
     */
    abstract protected function define_my_settings();

    /**
     * Defines activity specific steps for this task
     *
     * This method is called from {@link self::build()}. Activities are supposed
     * to call {self::add_step()} in it to include their specific steps in the
     * backup plan.
     */
    abstract protected function define_my_steps();

    /**
     * Encodes URLs to the activity instance's scripts into a site-independent form
     *
     * The current instance of the activity may be referenced from other places in
     * the course by URLs like http://my.moodle.site/mod/workshop/view.php?id=42
     * Obvisouly, such URLs are not valid any more once the course is restored elsewhere.
     * For this reason the backup file does not store the original URLs but encodes them
     * into a transportable form. During the restore, the reverse process is applied and
     * the encoded URLs are replaced with the new ones valid for the target site.
     *
     * Every plugin must override this method in its subclass.
     *
     * @see backup_xml_transformer class that actually runs the transformation
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        throw new coding_exception('encode_content_links() method needs to be overridden in each subclass of backup_activity_task');
    }
}
