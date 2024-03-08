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
 * Defines restore_activity_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * abstract activity task that provides all the properties and common tasks to be performed
 * when one activity is being restored
 *
 * TODO: Finish phpdocs
 */
abstract class restore_activity_task extends restore_task {

    protected $info; // info related to activity gathered from backup file
    protected $modulename;  // name of the module
    protected $moduleid;    // new (target) id of the course module
    protected $oldmoduleid; // old (original) id of the course module
    protected $oldmoduleversion; // old (original) version of the module
    protected $contextid;   // new (target) context of the activity
    protected $oldcontextid;// old (original) context of the activity
    protected $activityid;  // new (target) id of the activity
    protected $oldactivityid;// old (original) id of the activity

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $info, $plan = null) {
        $this->info = $info;
        $this->modulename = $this->info->modulename;
        $this->moduleid = 0;
        $this->oldmoduleid = $this->info->moduleid;
        $this->oldmoduleversion = 0;
        $this->contextid = 0;
        $this->oldcontextid = 0;
        $this->activityid = 0;
        $this->oldactivityid = 0;
        parent::__construct($name, $plan);
    }

    /**
     * Activity tasks have their own directory to read files
     */
    public function get_taskbasepath() {
        return $this->get_basepath() . '/' . $this->info->directory;
    }

    public function set_moduleid($moduleid) {
        $this->moduleid = $moduleid;
    }

    public function set_old_moduleversion($oldmoduleversion) {
        $this->oldmoduleversion = $oldmoduleversion;
    }

    public function set_activityid($activityid) {
        $this->activityid = $activityid;
    }

    public function set_old_activityid($activityid) {
        $this->oldactivityid = $activityid;
    }

    public function set_contextid($contextid) {
        $this->contextid = $contextid;
    }

    public function set_old_contextid($contextid) {
        $this->oldcontextid = $contextid;
    }

    public function get_modulename() {
        return $this->modulename;
    }

    public function get_moduleid() {
        return $this->moduleid;
    }

    /**
     * Returns the old course module id (cmid of activity which will be restored)
     *
     * @return int
     */
    public function get_old_moduleid() {
        return $this->oldmoduleid;
    }

    public function get_old_moduleversion() {
        return $this->oldmoduleversion;
    }

    public function get_activityid() {
        return $this->activityid;
    }

    public function get_old_activityid() {
        return $this->oldactivityid;
    }

    public function get_contextid() {
        return $this->contextid;
    }

    public function get_old_contextid() {
        return $this->oldcontextid;
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

        // Load he course_module estructure, generating it (with instance = 0)
        // but allowing the creation of the target context needed in following steps
        $this->add_step(new restore_module_structure_step('module_info', 'module.xml'));

        // Here we add all the common steps for any activity and, in the point of interest
        // we call to define_my_steps() is order to get the particular ones inserted in place.
        $this->define_my_steps();

        // Roles (optionally role assignments and always role overrides)
        $this->add_step(new restore_ras_and_caps_structure_step('course_ras_and_caps', 'roles.xml'));

        // Filters (conditionally)
        if ($this->get_setting_value('filters')) {
            $this->add_step(new restore_filters_structure_step('activity_filters', 'filters.xml'));
        }

        // Comments (conditionally)
        if ($this->get_setting_value('comments')) {
            $this->add_step(new restore_comments_structure_step('activity_comments', 'comments.xml'));
        }

        // Calendar events (conditionally)
        if ($this->get_setting_value('calendarevents')) {
            $this->add_step(new restore_calendarevents_structure_step('activity_calendar', 'calendar.xml'));
        }

        // Grades (module-related, rest of gradebook is restored later if possible: cats, calculations...)
        $this->add_step(new restore_activity_grades_structure_step('activity_grades', 'grades.xml'));

        // Advanced grading methods attached to the module
        $this->add_step(new restore_activity_grading_structure_step('activity_grading', 'grading.xml'));

        // Grade history. The setting 'grade_history' is handled in the step.
        $this->add_step(new restore_activity_grade_history_structure_step('activity_grade_history', 'grade_history.xml'));

        // Userscompletion (conditionally)
        if ($this->get_setting_value('userscompletion')) {
            $this->add_step(new restore_userscompletion_structure_step('activity_userscompletion', 'completion.xml'));
        }

        // Logs (conditionally)
        if ($this->get_setting_value('logs')) {
            // Legacy logs.
            $this->add_step(new restore_activity_logs_structure_step('activity_logs', 'logs.xml'));
            // New log stores.
            $this->add_step(new restore_activity_logstores_structure_step('activity_logstores', 'logstores.xml'));
        }

        // Activity competencies.
        $this->add_step(new restore_activity_competencies_structure_step('activity_competencies', 'competencies.xml'));

        // Search reindexing, if enabled and if not restoring entire course.
        if (\core_search\manager::is_indexing_enabled()) {
            $wholecourse = $this->get_target() == backup::TARGET_NEW_COURSE;
            $wholecourse = $wholecourse || ($this->setting_exists('overwrite_conf') && $this->get_setting_value('overwrite_conf'));
            if (!$wholecourse) {
                $this->add_step(new restore_activity_search_index('activity_search_index'));
            }
        }

        // The xAPI state (conditionally).
        if ($this->get_setting_value('xapistate')) {
            $this->add_step(new restore_xapistate_structure_step('activity_xapistate', 'xapistate.xml'));
        }

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
            $this->plan->set_excluding_activities(); // Inform plan we are excluding actvities

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

    /**
     * Define (add) particular steps that each activity can have
     */
    abstract protected function define_my_steps();

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        throw new coding_exception('define_decode_contents() method needs to be overridden in each subclass of restore_activity_task');
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        throw new coding_exception('define_decode_rules() method needs to be overridden in each subclass of restore_activity_task');
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * activity logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    public static function define_restore_log_rules() {
        throw new coding_exception('define_restore_log_rules() method needs to be overridden in each subclass of restore_activity_task');
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
        $activity_included->get_ui()->set_icon(new image_icon('monologo', get_string('pluginname', $this->modulename),
            $this->modulename, array('class' => 'iconlarge icon-post ml-1')));
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
        // - activity_included setting.
        $settingname = $settingprefix . 'userinfo';
        $defaultvalue = false;
        if (isset($this->info->settings[$settingname]) && $this->info->settings[$settingname]) { // Only enabled when available
            $defaultvalue = true;
        }

        $activity_userinfo = new restore_activity_userinfo_setting($settingname, base_setting::IS_BOOLEAN, $defaultvalue);
        if (!$defaultvalue) {
            // This is a bit hacky, but if there is no user data to restore, then
            // we replace the standard check-box with a select menu with the
            // single choice 'No', and the select menu is clever enough that if
            // there is only one choice, it just displays a static string.
            //
            // It would probably be better design to have a special UI class
            // setting_ui_checkbox_or_no, rather than this hack, but I am not
            // going to do that today.
            $activity_userinfo->set_ui(new backup_setting_ui_select($activity_userinfo, '-',
                    array(0 => get_string('no'))));
        } else {
            $activity_userinfo->get_ui()->set_label('-');
        }

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

        // Look for "activity_included" setting.
        $activity_included->add_dependency($activity_userinfo);

        // End of common activity settings, let's add the particular ones.
        $this->define_my_settings();
    }

    /**
     * Define (add) particular settings that each activity can have
     */
    abstract protected function define_my_settings();
}
