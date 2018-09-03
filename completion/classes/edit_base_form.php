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
 * Base form for changing completion rules
 *
 * @package     core_completion
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/course/modlib.php');

/**
 * Base form for changing completion rules. Used in bulk editing activity completion and editing default activity completion
 *
 * @package     core_completion
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_completion_edit_base_form extends moodleform {
    /** @var moodleform_mod Do not use directly, call $this->get_module_form() */
    protected $_moduleform = null;
    /** @var bool */
    protected $hascustomrules = false;
    /** @var stdClass */
    protected $course;

    /**
     * Returns list of types of selected module types
     *
     * @return array modname=>modfullname
     */
    abstract protected function get_module_names();

    /**
     * Returns true if all selected modules support tracking view.
     *
     * @return bool
     */
    protected function support_views() {
        foreach ($this->get_module_names() as $modname => $modfullname) {
            if (!plugin_supports('mod', $modname, FEATURE_COMPLETION_TRACKS_VIEWS, false)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns true if all selected modules support grading.
     *
     * @return bool
     */
    protected function support_grades() {
        foreach ($this->get_module_names() as $modname => $modfullname) {
            if (!plugin_supports('mod', $modname, FEATURE_GRADE_HAS_GRADE, false)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns an instance of component-specific module form for the first selected module
     *
     * @return moodleform_mod|null
     */
    abstract protected function get_module_form();

    /**
     * If all selected modules are of the same module type, adds custom completion rules from this module type
     *
     * @return array
     */
    protected function add_custom_completion_rules() {
        $modnames = array_keys($this->get_module_names());
        if (count($modnames) != 1 || !plugin_supports('mod', $modnames[0], FEATURE_COMPLETION_HAS_RULES, false)) {
            return [];
        }

        try {
            // Add completion rules from the module form to this form.
            $moduleform = $this->get_module_form();
            $moduleform->_form = $this->_form;
            if ($customcompletionelements = $moduleform->add_completion_rules()) {
                $this->hascustomrules = true;
            }
            return $customcompletionelements;
        } catch (Exception $e) {
            debugging('Could not add custom completion rule of module ' . $modnames[0] .
                ' to this form, this has to be fixed by the developer', DEBUG_DEVELOPER);
            return [];
        }
    }

    /**
     * Checks if at least one of the custom completion rules is enabled
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are;
     *   default returns false
     */
    protected function completion_rule_enabled($data) {
        if ($this->hascustomrules) {
            return $this->get_module_form()->completion_rule_enabled($data);
        }
        return false;
    }

    /**
     * Returns list of modules that have automatic completion rules that are not shown on this form
     * (because they are not present in at least one other selected module).
     *
     * @return array
     */
    protected function get_modules_with_hidden_rules() {
        $modnames = $this->get_module_names();
        if (count($modnames) <= 1) {
            // No rules definitions conflicts if there is only one module type.
            return [];
        }

        $conflicts = [];

        if (!$this->support_views()) {
            // If we don't display views rule but at least one module supports it - we have conflicts.
            foreach ($modnames as $modname => $modfullname) {
                if (empty($conflicts[$modname]) && plugin_supports('mod', $modname, FEATURE_COMPLETION_TRACKS_VIEWS, false)) {
                    $conflicts[$modname] = $modfullname;
                }
            }
        }

        if (!$this->support_grades()) {
            // If we don't display grade rule but at least one module supports it - we have conflicts.
            foreach ($modnames as $modname => $modfullname) {
                if (empty($conflicts[$modname]) && plugin_supports('mod', $modname, FEATURE_GRADE_HAS_GRADE, false)) {
                    $conflicts[$modname] = $modfullname;
                }
            }
        }

        foreach ($modnames as $modname => $modfullname) {
            // We do not display any custom completion rules, find modules that define them and add to conflicts list.
            if (empty($conflicts[$modname]) && plugin_supports('mod', $modname, FEATURE_COMPLETION_HAS_RULES, false)) {
                $conflicts[$modname] = $modfullname;
            }
        }

        return $conflicts;
    }

    /**
     * Form definition
     */
    public function definition() {
        $mform = $this->_form;

        // Course id.
        $mform->addElement('hidden', 'id', $this->course->id);
        $mform->setType('id', PARAM_INT);

        // Unlock completion automatically (this element can be used in validation).
        $mform->addElement('hidden', 'completionunlocked', 1);
        $mform->setType('completionunlocked', PARAM_INT);

        $mform->addElement('select', 'completion', get_string('completion', 'completion'),
            array(COMPLETION_TRACKING_NONE => get_string('completion_none', 'completion'),
                COMPLETION_TRACKING_MANUAL => get_string('completion_manual', 'completion')));
        $mform->addHelpButton('completion', 'completion', 'completion');
        $mform->setDefault('completion', COMPLETION_TRACKING_NONE);

        // Automatic completion once you view it.
        $autocompletionpossible = false;
        if ($this->support_views()) {
            $mform->addElement('advcheckbox', 'completionview', get_string('completionview', 'completion'),
                get_string('completionview_desc', 'completion'));
            $mform->disabledIf('completionview', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
            $autocompletionpossible = true;
        }

        // Automatic completion once it's graded.
        if ($this->support_grades()) {
            $mform->addElement('advcheckbox', 'completionusegrade', get_string('completionusegrade', 'completion'),
                get_string('completionusegrade_desc', 'completion'));
            $mform->disabledIf('completionusegrade', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
            $mform->addHelpButton('completionusegrade', 'completionusegrade', 'completion');
            $autocompletionpossible = true;
        }

        // Automatic completion according to module-specific rules.
        foreach ($this->add_custom_completion_rules() as $element) {
            $mform->disabledIf($element, 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
            $autocompletionpossible = true;
        }

        // Automatic option only appears if possible.
        if ($autocompletionpossible) {
            $mform->getElement('completion')->addOption(
                get_string('completion_automatic', 'completion'),
                COMPLETION_TRACKING_AUTOMATIC);
        }

        // Completion expected at particular date? (For progress tracking).
        $mform->addElement('date_time_selector', 'completionexpected',
            get_string('completionexpected', 'completion'), ['optional' => true]);
        $mform->addHelpButton('completionexpected', 'completionexpected', 'completion');
        $mform->disabledIf('completionexpected', 'completion', 'eq', COMPLETION_TRACKING_NONE);

        if ($conflicts = $this->get_modules_with_hidden_rules()) {
            $mform->addElement('static', 'qwerty', '', get_string('hiddenrules', 'completion', join(', ', $conflicts)));
        }

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Completion: Don't let them choose automatic completion without turning
        // on some conditions.
        if (array_key_exists('completion', $data) &&
            $data['completion'] == COMPLETION_TRACKING_AUTOMATIC) {
            if (empty($data['completionview']) && empty($data['completionusegrade']) &&
                !$this->completion_rule_enabled($data)) {
                $errors['completion'] = get_string('badautocompletion', 'completion');
            }
        }

        return $errors;
    }

    /**
     * Returns if this form has custom completion rules. This is only possible if all selected modules have the same
     * module type and this module type supports custom completion rules
     *
     * @return bool
     */
    public function has_custom_completion_rules() {
        return $this->hascustomrules;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();
        if ($data && $this->hascustomrules) {
            $this->get_module_form()->data_postprocessing($data);
        }
        return $data;
    }
}