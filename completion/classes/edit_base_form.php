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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/course/modlib.php');

/**
 * Base form for changing completion rules. Used in bulk editing activity completion and editing default activity completion
 *
 * @package     core_completion
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_completion_edit_base_form extends moodleform {

    use \core_completion\form\form_trait;

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
     * Get the module name. If the form have more than one modules, it will return the first one.
     *
     * @return string|null The module name or null if there is no modules associated to this form.
     */
    protected function get_module_name(): ?string {
        $modnames = $this->get_module_names();
        if (empty($modnames)) {
            return null;
        }

        $modnamekeys = array_keys($modnames);
        return reset($modnamekeys);
    }

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
    protected function add_custom_completion(string $function): array {
        $modnames = array_keys($this->get_module_names());

        if (count($modnames) != 1 || !plugin_supports('mod', $modnames[0], FEATURE_COMPLETION_HAS_RULES, false)) {
            return [false, []];
        }

        $component = "mod_{$modnames[0]}";
        $itemnames = \core_grades\component_gradeitems::get_itemname_mapping_for_component($component);
        $hascustomrules = count($itemnames) > 1;

        try {
            // Add completion rules from the module form to this form.
            $moduleform = $this->get_module_form();
            $moduleform->_form = $this->_form;
            if ($customcompletionelements = $moduleform->{$function}()) {
                $hascustomrules = true;
                foreach ($customcompletionelements as $customcompletionelement) {
                    // Instead of checking for the suffix at the end of the element name, we need to check for its presence
                    // because some modules, like SCORM, are adding things at the end.
                    if (!str_contains($customcompletionelement, $this->get_suffix())) {
                        debugging(
                            'Custom completion rule '  . $customcompletionelement . ' of module ' . $modnames[0] .
                            ' has wrong suffix and has been removed from the form. This has to be fixed by the developer',
                            DEBUG_DEVELOPER
                        );
                        if ($moduleform->_form->elementExists($customcompletionelement)) {
                            $moduleform->_form->removeElement($customcompletionelement);
                        }
                    }
                }
            }
            return [$hascustomrules, $customcompletionelements];
        } catch (Exception $e) {
            debugging('Could not add custom completion rule of module ' . $modnames[0] .
                ' to this form, this has to be fixed by the developer', DEBUG_DEVELOPER);
            return [$hascustomrules, $customcompletionelements];
        }
    }

    /**
     * If all selected modules are of the same module type, adds custom completion rules from this module type
     *
     * @return array
     */
    protected function add_completion_rules() {
        list($hascustomrules, $customcompletionelements) = $this->add_custom_completion('add_completion_rules');
        if (!$this->hascustomrules && $hascustomrules) {
            $this->hascustomrules = true;
        }

        $component = "mod_{$this->get_module_name()}";
        $itemnames = \core_grades\component_gradeitems::get_itemname_mapping_for_component($component);
        if (count($itemnames) > 1) {
            $customcompletionelements[] = 'completiongradeitemnumber';
        }

        return $customcompletionelements;
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
     * If all selected modules are of the same module type, adds custom completion rules from this module type
     *
     * @return array
     */
    public function add_completiongrade_rules(): array {
        list($hascustomrules, $customcompletionelements) = $this->add_custom_completion('add_completiongrade_rules');
        if (!$this->hascustomrules && $hascustomrules) {
            $this->hascustomrules = true;
        }

        return $customcompletionelements;
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

        // Add the completion elements to the form.
        $this->add_completion_elements(
            $this->get_module_name(),
            $this->support_views(),
            $this->support_grades(),
            false,
            $this->course->id
        );

        if ($conflicts = $this->get_modules_with_hidden_rules()) {
            $mform->addElement('static', 'qwerty', '', get_string('hiddenrules', 'completion', join(', ', $conflicts)));
        }

        // Whether to show the cancel button or not in the form.
        $displaycancel = $this->_customdata['displaycancel'] ?? true;
        $this->add_action_buttons($displaycancel);
    }

    /**
     * Return the course module of the form, if any.
     *
     * @return cm_info|null
     */
    protected function get_cm(): ?cm_info {
        return null;
    }

    /**
     * Each module which defines definition_after_data() must call this method.
     */
    public function definition_after_data() {
        $this->definition_after_data_completion($this->get_cm());
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

        // Completion: Check completion fields don't have errors.
        $errors = array_merge($errors, $this->validate_completion($data));

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
