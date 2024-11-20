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
 * Defines restore_section_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

    /**
     * Get the course module that is delegating this section.
     *
     * @return int|null the course module id that is delegating this section
     */
    public function get_delegated_cm(): ?int {
        if (!isset($this->info->parentcmid) || empty($this->info->parentcmid)) {
            return null;
        }
        return intval($this->info->parentcmid);
    }

    /**
     * Get the delegated activity modname if any.
     *
     * @return string|null the modname of the delegated activity
     */
    public function get_modname(): ?string {
        if (!isset($this->info->modname) || empty($this->info->modname)) {
            return null;
        }
        return $this->info->modname;
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
        $this->contextid = context_course::instance($this->get_courseid())->id;

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
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('course_sections', 'summary', 'course_section');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the sections to be executed by the link decoder
     */
    public static function define_decode_rules() {
        return array();
    }

// Protected API starts here

    /**
     * Define the common setting that any restore section will have
     */
    protected function define_settings() {
        // All the settings related to this activity will include this prefix
        $settingprefix = 'section_' . $this->info->sectionid . '_';

        // All these are common settings to be shared by all sections.
        $sectionincluded = $this->add_section_included_setting($settingprefix);
        $this->add_section_userinfo_setting($settingprefix, $sectionincluded);
    }

    /**
     * Add the section included setting to the task.
     *
     * @param string $settingprefix the identifier of the setting
     * @return section_backup_setting the setting added
     */
    protected function add_section_included_setting(string $settingprefix): section_backup_setting {
        global $DB;
        // Define sectionincluded (to decide if the whole task must be really executed).
        $settingname = $settingprefix . 'included';

        $delegatedcmid = $this->get_delegated_cm();
        if ($delegatedcmid) {
            $sectionincluded = new restore_subsection_included_setting($settingname, base_setting::IS_BOOLEAN, true);
            // Subsections depends on the parent activity included setting.
            $settingname = $this->get_modname() . '_' . $delegatedcmid . '_included';
            if ($this->plan->setting_exists($settingname)) {
                $cmincluded = $this->plan->get_setting($settingname);
                $cmincluded->add_dependency(
                    $sectionincluded,
                );
            }
            $label = get_string('subsectioncontent', 'backup');
        } else {
            $sectionincluded = new restore_section_included_setting($settingname, base_setting::IS_BOOLEAN, true);

            if (is_number($this->info->title)) {
                $label = get_string('includesection', 'backup', $this->info->title);
            } else if (empty($this->info->title)) { // Don't throw error if title is empty, gracefully continue restore.
                $this->log(
                    'Section title missing in backup for section id ' . $this->info->sectionid,
                    backup::LOG_WARNING,
                    $this->name
                );
                $label = get_string('unnamedsection', 'backup');
            } else {
                $label = $this->info->title;
            }
        }

        $sectionincluded->get_ui()->set_label($label);
        $this->add_setting($sectionincluded);

        return $sectionincluded;
    }

    /**
     * Add the section userinfo setting to the task.
     *
     * @param string $settingprefix the identifier of the setting
     * @param section_backup_setting $includefield the section included setting
     * @return section_backup_setting the setting added
     */
    protected function add_section_userinfo_setting(
        string $settingprefix,
        section_backup_setting $includefield
    ): section_backup_setting {
        // Define sectionuserinfo. Dependent of:
        // - users root setting.
        // - sectionincluded setting.
        $settingname = $settingprefix . 'userinfo';
        $defaultvalue = false;
        if (isset($this->info->settings[$settingname]) && $this->info->settings[$settingname]) { // Only enabled when available
            $defaultvalue = true;
        }

        $delegatedcmid = $this->get_delegated_cm();
        if ($delegatedcmid) {
            $sectionuserinfo = new restore_subsection_userinfo_setting($settingname, base_setting::IS_BOOLEAN, $defaultvalue);
            // Subsections depends on the parent activity included setting.
            $settingname = $this->get_modname() . '_' . $delegatedcmid . '_userinfo';
            if ($this->plan->setting_exists($settingname)) {
                $cmincluded = $this->plan->get_setting($settingname);
                $cmincluded->add_dependency(
                    $sectionuserinfo,
                );
            }
        } else {
            $sectionuserinfo = new restore_section_userinfo_setting($settingname, base_setting::IS_BOOLEAN, $defaultvalue);
        }

        if (!$defaultvalue) {
            // This is a bit hacky, but if there is no user data to restore, then
            // we replace the standard check-box with a select menu with the
            // single choice 'No', and the select menu is clever enough that if
            // there is only one choice, it just displays a static string.
            //
            // It would probably be better design to have a special UI class
            // setting_ui_checkbox_or_no, rather than this hack, but I am not
            // going to do that today.
            $sectionuserinfo->set_ui(
                new backup_setting_ui_select($sectionuserinfo,
                    get_string('includeuserinfo', 'backup'), [0 => get_string('no')])
            );
        } else {
            $sectionuserinfo->get_ui()->set_label(get_string('includeuserinfo', 'backup'));
        }

        $this->add_setting($sectionuserinfo);

        // Look for "users" root setting.
        $users = $this->plan->get_setting('users');
        $users->add_dependency($sectionuserinfo);

        // Look for "section included" section setting.
        $includefield->add_dependency($sectionuserinfo);

        return $sectionuserinfo;
    }
}
