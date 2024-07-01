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
 * Defines backup_section_task class
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
 * when one section is being backup
 *
 * TODO: Finish phpdocs
 */
class backup_section_task extends backup_task {

    protected $sectionid;

    /**
     * @var stdClass $section The database section object.
     */
    protected stdClass $section;

    /**
     * @var int|null $delegatedcmid the course module that is delegating this section (if any)
     */
    protected ?int $delegatedcmid = null;

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $sectionid, $plan = null) {
        global $DB;

        // Check section exists
        if (!$section = $DB->get_record('course_sections', array('id' => $sectionid))) {
            throw new backup_task_exception('section_task_section_not_found', $sectionid);
        }

        $this->section = $section;
        $this->sectionid  = $sectionid;

        parent::__construct($name, $plan);
    }

    /**
     * Set the course module that is delegating this section.
     *
     * Delegated section can belong to any kind of plugin. However, when a delegated
     * section belongs to a course module, the UI will present all settings according.
     *
     * @param int $cmid the course module id that is delegating this section
     */
    public function set_delegated_cm(int $cmid) {
        $this->delegatedcmid = $cmid;
    }

    /**
     * Get the course module that is delegating this section.
     *
     * @return int|null the course module id that is delegating this section
     */
    public function get_delegated_cm(): ?int {
        return $this->delegatedcmid;
    }

    /**
     * Get the delegate activity modname (if any).
     *
     * @return string|null the modname of the delegated activity
     */
    public function get_modname(): ?string {
        if (empty($this->section->component)) {
            return null;
        }
        return core_component::normalize_component($this->section->component)[1];
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
        $coursectxid = context_course::instance($this->get_courseid())->id;
        $this->add_section_setting(backup::VAR_CONTEXTID, base_setting::IS_INTEGER, $coursectxid);

        // Add some extra settings that related processors are going to need
        $this->add_section_setting(backup::VAR_SECTIONID, base_setting::IS_INTEGER, $this->sectionid);
        $this->add_section_setting(backup::VAR_COURSEID, base_setting::IS_INTEGER, $this->get_courseid());

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
     * Define the common setting that any backup section will have.
     */
    protected function define_settings() {
        global $DB;

        // All the settings related to this activity will include this prefix.
        $settingprefix = 'section_' . $this->sectionid . '_';

        $incudefield = $this->add_section_included_setting($settingprefix);
        $this->add_section_userinfo_setting($settingprefix, $incudefield);
    }

    /**
     * Add a setting to the task. This method is used to add a setting to the task
     *
     * @param int|string $identifier the identifier of the setting
     * @param string $type the type of the setting
     * @param string|int $value the value of the setting
     * @return section_backup_setting the setting added
     */
    protected function add_section_setting(int|string $identifier, string $type, string|int $value): section_backup_setting {
        if ($this->get_delegated_cm()) {
            $setting = new backup_subsection_generic_setting($identifier, $type, $value);
        } else {
            $setting = new backup_section_generic_setting($identifier, $type, $value);
        }
        $this->add_setting($setting);
        return $setting;
    }

    /**
     * Add the section included setting to the task.
     *
     * @param string $settingprefix the identifier of the setting
     * @return section_backup_setting the setting added
     */
    protected function add_section_included_setting(string $settingprefix): section_backup_setting {
        global $DB;
        $course = $DB->get_record('course', ['id' => $this->section->course], '*', MUST_EXIST);

        // Define sectionincluded (to decide if the whole task must be really executed).
        $settingname = $settingprefix . 'included';

        $delegatedcmid = $this->get_delegated_cm();
        if ($delegatedcmid) {
            $sectionincluded = new backup_subsection_included_setting($settingname, base_setting::IS_BOOLEAN, true);
            // Subsections depends on the parent activity included setting.
            $settingname = $this->get_modname() . '_' . $delegatedcmid . '_included';
            if ($this->plan->setting_exists($settingname)) {
                $cmincluded = $this->plan->get_setting($settingname);
                $cmincluded->add_dependency(
                    $sectionincluded,
                );
            }
            $sectionincluded->get_ui()->set_label(get_string('subsectioncontent', 'backup'));
        } else {
            $sectionincluded = new backup_section_included_setting($settingname, base_setting::IS_BOOLEAN, true);
            $sectionincluded->get_ui()->set_label(get_section_name($course, $this->section));
        }

        $this->add_setting($sectionincluded);

        return $sectionincluded;
    }

    /**
     * Add the section userinfo setting to the task.
     *
     * @param string $settingprefix the identifier of the setting
     * @param section_backup_setting $includefield the setting to depend on
     * @return section_backup_setting the setting added
     */
    protected function add_section_userinfo_setting(
        string $settingprefix,
        section_backup_setting $includefield
    ): section_backup_setting {
        // Define sectionuserinfo. Dependent of:
        // - users root setting.
        // - section_included setting.
        $settingname = $settingprefix . 'userinfo';

        $delegatedcmid = $this->get_delegated_cm();
        if ($delegatedcmid) {
            $sectionuserinfo = new backup_subsection_userinfo_setting($settingname, base_setting::IS_BOOLEAN, true);
            // Subsections depends on the parent activity included setting.
            $settingname = $this->get_modname() . '_' . $delegatedcmid . '_userinfo';
            if ($this->plan->setting_exists($settingname)) {
                $cmincluded = $this->plan->get_setting($settingname);
                $cmincluded->add_dependency(
                    $sectionuserinfo,
                );
            }
        } else {
            $sectionuserinfo = new backup_section_userinfo_setting($settingname, base_setting::IS_BOOLEAN, true);
        }

        $sectionuserinfo->get_ui()->set_label(get_string('includeuserinfo', 'backup'));
        $sectionuserinfo->get_ui()->set_visually_hidden_label(
            get_string('section_prefix', 'core_backup', $this->section->name ?: $this->section->section)
        );
        $this->add_setting($sectionuserinfo);
        // Look for "users" root setting.
        $users = $this->plan->get_setting('users');
        $users->add_dependency($sectionuserinfo);
        // Look for "section_included" section setting.
        $includefield->add_dependency($sectionuserinfo);

        return $sectionuserinfo;
    }
}
