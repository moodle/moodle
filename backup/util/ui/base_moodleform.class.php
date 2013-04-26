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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

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
abstract class base_moodleform extends moodleform {
    /**
     * The stage this form belongs to
     * @var base_ui_stage
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
    function __construct(base_ui_stage $uistage, $action=null, $customdata=null, $method='post', $target='', $attributes=null, $editable=true) {
        $this->uistage = $uistage;
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }
    /**
     * The standard form definition... obviously not much here
     */
    function definition() {
        $ui = $this->uistage->get_ui();
        $mform = $this->_form;
        $mform->setDisableShortforms();
        $stage = $mform->addElement('hidden', 'stage', $this->uistage->get_stage());
        $mform->setType('stage', PARAM_INT);
        $stage = $mform->addElement('hidden', $ui->get_name(), $ui->get_uniqueid());
        $mform->setType($ui->get_name(), PARAM_ALPHANUM);
        $params = $this->uistage->get_params();
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $name=>$value) {
                // TODO: Horrible hack, but current backup ui structure does not allow
                // to make this easy (only changing params to objects that would be
                // possible. MDL-38735.
                $intparams = array(
                        'contextid', 'importid', 'target');
                $stage = $mform->addElement('hidden', $name, $value);
                if (in_array($name, $intparams)) {
                    $mform->setType($name, PARAM_INT);
                } else {
                    // Adding setType() to avoid missing setType() warnings.
                    // MDL-39126: support $mform->setType() for additional backup parameters.
                    $mform->setType($name, PARAM_RAW);
                }
            }
        }
    }
    /**
     * Definition applied after the data is organised.. why's it here? because I want
     * to add elements on the fly.
     * @global moodle_page $PAGE
     */
    function definition_after_data() {
        $buttonarray=array();
        $buttonarray[] = $this->_form->createElement('submit', 'submitbutton', get_string($this->uistage->get_ui()->get_name().'stage'.$this->uistage->get_stage().'action', 'backup'), array('class'=>'proceedbutton'));
        if (!$this->uistage->is_first_stage()) {
            $buttonarray[] = $this->_form->createElement('submit', 'previous', get_string('previousstage','backup'));
        }
        $buttonarray[] = $this->_form->createElement('cancel', 'cancel', get_string('cancel'), array('class'=>'confirmcancel'));
        $this->_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $this->_form->closeHeaderBefore('buttonar');

        $this->_definition_finalized = true;
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
    function add_setting(backup_setting $setting, base_task $task=null) {
        return $this->add_settings(array(array($setting, $task)));
    }
    /**
     * Adds multiple backup_settings as elements to the form
     * @param array $settingstasks Consists of array($setting, $task) elements
     * @return bool
     */
    public function add_settings(array $settingstasks) {
        global $OUTPUT;

        $defaults = array();
        foreach ($settingstasks as $st) {
            list($setting, $task) = $st;
            // If the setting cant be changed or isn't visible then add it as a fixed setting.
            if (!$setting->get_ui()->is_changeable() || $setting->get_visibility() != backup_setting::VISIBLE) {
                $this->add_fixed_setting($setting, $task);
                continue;
            }

            // First add the formatting for this setting
            $this->add_html_formatting($setting);

            // Then call the add method with the get_element_properties array
            call_user_func_array(array($this->_form, 'addElement'), $setting->get_ui()->get_element_properties($task, $OUTPUT));
            $this->_form->setType($setting->get_ui_name(), $setting->get_param_validation());
            $defaults[$setting->get_ui_name()] = $setting->get_value();
            if ($setting->has_help()) {
                list($identifier, $component) = $setting->get_help();
                $this->_form->addHelpButton($setting->get_ui_name(), $identifier, $component);
            }
            $this->_form->addElement('html', html_writer::end_tag('div'));
        }
        $this->_form->setDefaults($defaults);
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
    function add_fixed_setting(backup_setting $setting, base_task $task) {
        global $OUTPUT;
        $settingui = $setting->get_ui();
        if ($setting->get_visibility() == backup_setting::VISIBLE) {
            $this->add_html_formatting($setting);
            switch ($setting->get_status()) {
                case backup_setting::LOCKED_BY_PERMISSION:
                    $icon = ' '.$OUTPUT->pix_icon('i/permissionlock', get_string('lockedbypermission', 'backup'), 'moodle', array('class'=>'smallicon lockedicon permissionlock'));
                    break;
                case backup_setting::LOCKED_BY_CONFIG:
                    $icon = ' '.$OUTPUT->pix_icon('i/configlock', get_string('lockedbyconfig', 'backup'), 'moodle', array('class'=>'smallicon lockedicon configlock'));
                    break;
                case backup_setting::LOCKED_BY_HIERARCHY:
                    $icon = ' '.$OUTPUT->pix_icon('i/hierarchylock', get_string('lockedbyhierarchy', 'backup'), 'moodle', array('class'=>'smallicon lockedicon configlock'));
                    break;
                default:
                    $icon = '';
                    break;
            }
            $label = $settingui->get_label($task);
            $labelicon = $settingui->get_icon();
            if (!empty($labelicon)) {
                $label .= '&nbsp;'.$OUTPUT->render($labelicon);
            }
            $this->_form->addElement('static', 'static_'.$settingui->get_name(), $label, $settingui->get_static_value().$icon);
            $this->_form->addElement('html', html_writer::end_tag('div'));
        }
        $this->_form->addElement('hidden', $settingui->get_name(), $settingui->get_value());
        $this->_form->setType($settingui->get_name(), $settingui->get_param_validation());
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
            call_user_func_array(array($this->_form, 'disabledIf'), $dependency);
        }
    }
    /**
     * Returns true if the form was cancelled, false otherwise
     * @return bool
     */
    public function is_cancelled() {
        return (optional_param('cancel', false, PARAM_BOOL) || parent::is_cancelled());
    }

    /**
     * Removes an element from the form if it exists
     * @param string $elementname
     * @return bool
     */
    public function remove_element($elementname) {
        if ($this->_form->elementExists($elementname)) {
            return $this->_form->removeElement($elementname);
        } else {
            return false;
        }
    }

    /**
     * Gets an element from the form if it exists
     *
     * @param string $elementname
     * @return HTML_QuickForm_input|MoodleQuickForm_group
     */
    public function get_element($elementname) {
        if ($this->_form->elementExists($elementname)) {
            return $this->_form->getElement($elementname);
        } else {
            return false;
        }
    }

    /**
     * Displays the form
     */
    public function display() {
        global $PAGE;

        $this->require_definition_after_data();

        $config = new stdClass;
        $config->title = get_string('confirmcancel', 'backup');
        $config->question = get_string('confirmcancelquestion', 'backup');
        $config->yesLabel = get_string('confirmcancelyes', 'backup');
        $config->noLabel = get_string('confirmcancelno', 'backup');
        $config->closeButtonTitle = get_string('close', 'editor');
        $PAGE->requires->yui_module('moodle-backup-confirmcancel', 'M.core_backup.watch_cancel_buttons', array($config));

        $PAGE->requires->yui_module('moodle-backup-backupselectall', 'M.core_backup.select_all_init',
                array(array('select' => get_string('select'), 'all' => get_string('all'), 'none' => get_string('none'))));

        parent::display();
    }

    /**
     * Ensures the the definition after data is loaded
     */
    public function require_definition_after_data() {
        if (!$this->_definition_finalized) {
            $this->definition_after_data();
        }
    }
}
