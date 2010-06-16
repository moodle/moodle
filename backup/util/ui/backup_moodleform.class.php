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
 * This file contains the generic moodleform bridge for the backup user interface
 * as well as the individual forms that relate to the different stages the user
 * interface can exist within.
 * 
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Backup moodleform bridge
 *
 * Ahhh the mighty moodleform bridge! Strong enough to take the weight of 682 full
 * grown african swallows all of whom have been carring coconuts for several days.
 * EWWWWW!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class backup_moodleform extends moodleform {
    /**
     * The stage this form belongs to
     * @var backup_ui_stage
     */
    protected $uistage = null;
    /**
     * True if we have a course div open, false otherwise
     * @var bool
     */
    protected $coursediv = false;
    /**
     * True if we have a section div open, false otherwise
     * @var bool
     */
    protected $sectiondiv = false;
    /**
     * True if we have an activity div open, false otherwise
     * @var bool
     */
    protected $activitydiv = false;
    /**
     * Creates the form
     *
     * @param backup_ui_stage $uistage
     * @param moodle_url|string $action
     * @param mixed $customdata
     * @param string $method get|post
     * @param string $target
     * @param array $attributes
     * @param bool $editable
     */
    function __construct(backup_ui_stage $uistage, $action=null, $customdata=null, $method='post', $target='', $attributes=null, $editable=true) {
        $this->uistage = $uistage;
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }
    /**
     * The standard form definition... obviously not much here
     */
    function definition() {
        $mform = $this->_form;
        $stage = $mform->addElement('hidden', 'stage', $this->uistage->get_stage());
        $stage = $mform->addElement('hidden', 'backup', $this->uistage->get_backupid());
        $params = $this->uistage->get_params();
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $name=>$value) {
                $stage = $mform->addElement('hidden', $name, $value);
    }
        }
    }
    /**
     * Definition applied after the data is organised.. why's it here? because I want
     * to add elements on the fly.
     */
    function definition_after_data() {
        $buttonarray=array();
        if ($this->uistage->get_stage() > backup_ui::STAGE_INITIAL) {
            $buttonarray[] = $this->_form->createElement('submit', 'previous', get_string('previousstage','backup'));
        }
        $buttonarray[] = $this->_form->createElement('submit', 'submitbutton', get_string('onstage'.$this->uistage->get_stage().'action', 'backup'));
        $buttonarray[] = $this->_form->createElement('cancel');
        $this->_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $this->_form->closeHeaderBefore('buttonar');
    }
    /**
     * Closes any open divs
     */
    function close_task_divs() {
        if ($this->activitydiv) {
            $this->_form->addElement('html', html_writer::end_tag('div'));
            $this->activitydiv = false;
        }
        if ($this->sectiondiv) {
            $this->_form->addElement('html', html_writer::end_tag('div'));
            $this->sectiondiv = false;
        }
        if ($this->coursediv) {
            $this->_form->addElement('html', html_writer::end_tag('div'));
            $this->coursediv = false;
        }
    }
    /**
     * Adds the backup_setting as a element to the form
     * @param backup_setting $setting
     * @return bool
     */
    function add_setting(backup_setting $setting, backup_task $task=null) {

        // Check if the setting is locked first up
        if ($setting->get_status() !== base_setting::NOT_LOCKED) {
            // If it has no dependencies on other settings we can add it as a
            // fixed setting instead
            if (!$setting->has_dependencies_on_settings()) {
                // Fixed setting it is!
                return $this->add_fixed_setting($setting);
            }
            // Hmm possible to unlock it in the UI so disable instead.
            $setting->get_ui()->disable();
        }

        // First add the formatting for this setting
        $this->add_html_formatting($setting);
        // The call the add method with the get_element_properties array
        call_user_method_array('addElement', $this->_form, $setting->get_ui()->get_element_properties($task));
        $this->_form->setDefault($setting->get_ui_name(), $setting->get_value());
        if ($setting->has_help()) {
            list($identifier, $component) = $setting->get_help();
            $this->_form->addHelpButton($setting->get_ui_name(), $identifier, $component);
        }
        $this->_form->addElement('html', html_writer::end_tag('div'));
        return true;
    }
    /**
     * Adds a heading to the form
     * @param string $name
     * @param string $text
     */
    function add_heading($name , $text) {
        $this->_form->addElement('header', $name, $text);
    }
    /**
     * Adds HTML formatting for the given backup setting, needed to group/segment
     * correctly.
     * @param backup_setting $setting
     */
    protected function add_html_formatting(backup_setting $setting) {
        $mform = $this->_form;
        $isincludesetting = (strpos($setting->get_name(), '_include')!==false);
        if ($isincludesetting && $setting->get_level() != backup_setting::ROOT_LEVEL)  {
            switch ($setting->get_level()) {
                case backup_setting::COURSE_LEVEL:
                    if ($this->activitydiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                        $this->activitydiv = false;
                    }
                    if ($this->sectiondiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                        $this->sectiondiv = false;
                    }
                    if ($this->coursediv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                    }
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'grouped_settings course_level')));
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'include_setting course_level')));
                    $this->coursediv = true;
                    break;
                case backup_setting::SECTION_LEVEL:
                    if ($this->activitydiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                        $this->activitydiv = false;
                    }
                    if ($this->sectiondiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                    }
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'grouped_settings section_level')));
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'include_setting section_level')));
                    $this->sectiondiv = true;
                    break;
                case backup_setting::ACTIVITY_LEVEL:
                    if ($this->activitydiv) {
                        $this->_form->addElement('html', html_writer::end_tag('div'));
                    }
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'grouped_settings activity_level')));
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'include_setting activity_level')));
                    $this->activitydiv = true;
                    break;
                default:
                    $mform->addElement('html', html_writer::start_tag('div', array('class'=>'normal_setting')));
                    break;
            }
        } else if ($setting->get_level() == backup_setting::ROOT_LEVEL) {
            $mform->addElement('html', html_writer::start_tag('div', array('class'=>'root_setting')));
        } else {
            $mform->addElement('html', html_writer::start_tag('div', array('class'=>'normal_setting')));
        }
    }
    /**
     * Adds a fixed or static setting to the form
     * @param backup_setting $setting
     */
    function add_fixed_setting(backup_setting $setting) {
        global $OUTPUT;
        $settingui = $setting->get_ui();
        if ($setting->get_visibility() == backup_setting::VISIBLE) {
            $this->add_html_formatting($setting);
            if ($setting->get_status() != backup_setting::NOT_LOCKED) {
                $this->_form->addElement('static', 'static_'.$settingui->get_name(), $settingui->get_label(),$settingui->get_static_value().' '.$OUTPUT->pix_icon('i/unlock', get_string('locked', 'backup'), 'moodle', array('class'=>'smallicon lockedicon')));
            } else {
                $this->_form->addElement('static','static_'. $settingui->get_name(), $settingui->get_label(), $settingui->get_static_value());
            }
            $this->_form->addElement('html', html_writer::end_tag('div'));
        }
        $this->_form->addElement('hidden', $settingui->get_name(), $settingui->get_value());
    }
    /**
     * Adds dependencies to the form recursively
     * 
     * @param backup_setting $setting
     */
    function add_dependencies(backup_setting $setting) {
        $mform = $this->_form;
        // Apply all dependencies for backup
        foreach ($setting->get_my_dependency_properties() as $key=>$dependency) {
            call_user_method_array('disabledIf', $this->_form, $dependency);
        }
    }
    /**
     * Returns true if the form was cancelled, false otherwise
     * @return bool
     */
    public function is_cancelled() {
        return (optional_param('cancel', false, PARAM_BOOL) || parent::is_cancelled());
    }
}
/**
 * Initial backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 */
class backup_initial_form extends backup_moodleform {}
/**
 * Schema backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 */
class backup_schema_form extends backup_moodleform {}
/**
 * Confirmation backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 */
class backup_confirmation_form extends backup_moodleform {

    public function definition_after_data() {
        parent::definition_after_data();
        $this->_form->addRule('setting_root_filename', get_string('errorfilenamerequired', 'backup'), 'required');
        $this->_form->setType('setting_root_filename', PARAM_FILE);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!array_key_exists('setting_root_filename', $errors)) {
            if (trim($data['setting_root_filename']) == '') {
                $errors['setting_root_filename'] = get_string('errorfilenamerequired', 'backup');
            } else if (!preg_match('#\.zip$#i', $data['setting_root_filename'])) {
                $errors['setting_root_filename'] = get_string('errorfilenamemustbezip', 'backup');
            }
        }

        return $errors;
    }

}