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
 * File containing the step 1 form.
 *
 * @package     tool_pluginskel
 * @subpackage  util
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Step 1 form.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_step1_form extends moodleform {

    /** @var string The component type. */
    protected $componenttype;

    /**
     * The standard form definiton.
     */
    public function definition () {
        $mform = $this->_form;

        $mform->addElement('html', '
            <div class="alert alert-warning">
            <p><b>Warning: This may or may not work</b></p>
            <p>Defining the plugin skeleton recipe via this interface is known to not work with recent Boost based themes.
            Additionally, some features - such as describing the privacy API - is not supported yet via this interface.</p>
            <p>Please refer to the <tt>cli/example.yaml</tt> and define the recipe manually as a YAML file.</p>
            </div>'
        );

        $recipe = $this->_customdata['recipe'];
        $component = $recipe['component'];
        list($this->componenttype, $componentname) = core_component::normalize_component($component);

        $generalvars = tool_pluginskel\local\util\manager::get_general_variables();
        $componentvars = tool_pluginskel\local\util\manager::get_component_variables($component);
        $featuresvars = tool_pluginskel\local\util\manager::get_features_variables();

        $mform->addElement('header', 'generalhdr', get_string('generalhdr', 'tool_pluginskel'));
        $mform->setExpanded('generalhdr', true);

        foreach ($generalvars as $variable) {

            $type = $variable['type'];
            $elementname = $variable['name'];

            // Template variables that are arrays will be added at the bottom of the page.
            if ($type === 'numeric-array' || $type === 'associative-array') {
                continue;
            }

            if ($type === 'text' || $type === 'int') {
                $this->add_text_element($elementname, $variable, $recipe);
            }

            if ($type === 'boolean') {
                // Non-required boolean variable can also be missing from the recipe.
                // The select element adds an extra field 'undefined' to discard it from the recipe.
                if (empty($variable['required'])) {
                    $this->add_select_element($elementname, $variable, $recipe, true);
                } else {
                    $this->add_advcheckbox_element($elementname, $variable, $recipe);
                }
            }

            if ($type === 'multiple-options') {
                $this->add_select_element($elementname, $variable, $recipe);
            }
        }

        foreach ($featuresvars as $variable) {

            $type = $variable['type'];
            // Features that are not arrays are always under 'features' in the recipe.
            $elementname = 'features['.$variable['name'].']';
            $recipefeatures = empty($recipe['features']) ? array() : $recipe['features'];

            // Array variables will be added at the bottom of the page.
            if ($type === 'numeric-array' || $type === 'associative-array') {
                continue;
            }

            if ($type === 'text' || $type === 'int') {
                $this->add_text_element($elementname, $variable, $recipefeatures);
            }

            if ($type === 'boolean') {
                $this->add_advcheckbox_element($elementname, $variable, $recipefeatures);
            }

            if ($type === 'multiple-options') {
                $this->add_select_element($elementname, $variable, $recipefeatures);
            }
        }

        if (!empty($componentvars)) {
            $mform->addElement('header', 'componenthdr', get_string('componenthdr', 'tool_pluginskel'));
            $mform->setExpanded('componenthdr', true);
        }

        foreach ($componentvars as $variable) {

            $type = $variable['type'];
            $elementname = $this->componenttype.'_features['.$variable['name'].']';
            $componentfeatures = $this->componenttype.'_features';
            $componentrecipe = empty($recipe[$componentfeatures]) ? array() : $recipe[$componentfeatures];

            // Arrays will be added inside their own fieldset.
            if ($type === 'numeric-array' || $type === 'associative-array') {
                continue;
            }

            if ($type === 'text' || $type === 'int') {
                $this->add_text_element($elementname, $variable, $componentrecipe);
            }

            if ($type === 'boolean') {
                // Non-required boolean variable can also be missing from the recipe.
                // The select element adds an extra field 'undefined' to discard it from the recipe.
                if (empty($variable['required'])) {
                    $this->add_select_element($elementname, $variable, $componentrecipe, true);
                } else {
                    $this->add_advcheckbox_element($elementname, $variable, $componentrecipe);
                }
            }

            if ($type === 'multiple-options') {
                $this->add_select_element($elementname, $variable, $componentrecipe);
            }
        }

        foreach ($componentvars as $variable) {

            $type = $variable['type'];
            $parentname = $this->componenttype.'_features';

            if ($type === 'numeric-array') {
                $this->add_numeric_fieldset($variable, $recipe, $parentname);
            } else if ($type === 'associative-array') {
                $this->add_associative_fieldset($variable, $recipe, $parentname);
            }
        }

        foreach ($generalvars as $variable) {
            $type = $variable['type'];
            if ($type === 'numeric-array') {
                $this->add_numeric_fieldset($variable, $recipe);
            } else if ($type === 'associative-array') {
                $this->add_associative_fieldset($variable, $recipe);
            }
        }

        // Adding array features.
        foreach ($featuresvars as $variable) {
            $type = $variable['type'];
            if ($type === 'numeric-array') {
                $this->add_numeric_fieldset($variable, $recipe);
            } else if ($type === 'associative-array') {
                $this->add_associative_fieldset($variable, $recipe);
            }
        }

        $mform->addElement('html', '<hr>');

        $buttonarr = array();
        $buttonarr[] = $mform->createElement('submit', 'buttondownloadskel', get_string('downloadskel', 'tool_pluginskel'));
        $buttonarr[] = $mform->createElement('submit', 'buttondownloadrecipe', get_string('downloadrecipe', 'tool_pluginskel'));
        $buttonarr[] = $mform->createElement('submit', 'buttonshowrecipe', get_string('showrecipe', 'tool_pluginskel'));
        $mform->addGroup($buttonarr, 'buttonarr', '', array(' '), false);
        $mform->closeHeaderBefore('buttonarr');

        $mform->addElement('hidden', 'step', '1');
        $mform->setType('step', PARAM_INT);

        $mform->addElement('hidden', 'component1', $component);
        $mform->setType('component1', PARAM_TEXT);

        $mform->addElement('hidden', 'componenttype1', $this->componenttype);
        $mform->setType('componenttype1', PARAM_TEXT);

        $templatevars = array_merge($generalvars, $componentvars, $featuresvars);
        $arrayvars = $this->get_array_template_variables($templatevars);
        $arrayvarsjson = json_encode($arrayvars);

        $mform->addElement('hidden', 'templatevars', $arrayvarsjson);
        $mform->setType('templatevars', PARAM_TEXT);
    }

    /**
     * Returns only those template variables which are arrays.
     *
     * @param string[] $templatevars All of the template variables.
     * @return string[] Only those variables which are arrays.
     */
    protected function get_array_template_variables($templatevars) {

        $arrayvars = array();
        foreach ($templatevars as $variable) {
            if ($variable['type'] === 'numeric-array') {
                $arrayvars[] = $variable;
            } else if ($variable['type'] === 'associative-array') {
                // Adding associative array variables that have a numeric array value.
                foreach ($variable['values'] as $nestedvariable) {
                    if ($nestedvariable['type'] === 'numeric-array') {
                        $arrayvars[] = $variable;
                        break;
                    }
                }
            }
        }

        return $arrayvars;
    }

    /**
     * Returns the label of an element based on the element name.
     *
     * The function replaces paranthesis by an underscore and removes any array indexes.
     *
     * @param string $elementname
     */
    protected function construct_label($elementname) {

        $namearr = preg_split('/\]\[|\[|\]/', $elementname);
        $lastvalue = end($namearr);
        if (empty($lastvalue)) {
            array_pop($namearr);
        }

        $labelarr = array();
        foreach ($namearr as $atom) {
            if (!is_numeric($atom)) {
                $labelarr[] = $atom;
            }
        }

        $label = implode('_', $labelarr);

        return $label;
    }

    /**
     * Adds a select element to the form.
     *
     * @param string $elementname Element form name.
     * @param string[] $templatevar The template variable
     * @param string[] $recipe The recipe.
     * @param bool $replaceboolean If the select element replaces a boolean non-required variable.
     * @param string $index Optional index for the element in an array variable.
     */
    protected function add_select_element($elementname, $templatevar, $recipe, $replaceboolean = false, $index = null) {

        $mform = $this->_form;
        $variablename = $templatevar['name'];

        if (!$replaceboolean) {
            $selectvalues = $templatevar['values'];
        } else {
            $selectvalues = array('true' => 'true', 'false' => 'false');
            if (isset($recipe[$variablename])) {
                if ($recipe[$variablename] === true) {
                    $recipe[$variablename] = 'true';
                } else {
                    $recipe[$variablename] = 'false';
                }
            }
        }

        if (is_null($index)) {
            $indexprefix = '';
        } else {
            $indexprefix = $index.'. ';
        }

        // Adding 'undefine' option to select element for optional template variable.
        if (empty($templatevar['required'])) {
            $undefined = array('undefined' => get_string('undefined', 'tool_pluginskel'));
            $selectvalues = array_merge($undefined, $selectvalues);
        }

        $label = $this->construct_label($elementname);
        $mform->addElement('select', $elementname, $indexprefix.get_string($label, 'tool_pluginskel'), $selectvalues);
        $mform->addHelpButton($elementname, $label, 'tool_pluginskel');

        if (!empty($recipe[$variablename]) && !empty($selectvalues[$recipe[$variablename]])) {
            $mform->getElement($elementname)->setSelected($recipe[$variablename]);
        }

        if (isset($templatevar['default'])) {
            $mform->setDefault($elementname, $templatevar['default']);
        }
    }

    /**
     * Adds an advcheckbox element to the form.
     *
     * @param string $elementname Element form name.
     * @param string[] $templatevar The template variable
     * @param string[] $recipe The recipe.
     * @param string $index Optional index for the element in an array variable.
     */
    protected function add_advcheckbox_element($elementname, $templatevar, $recipe, $index = null) {

        $mform = $this->_form;
        $variablename = $templatevar['name'];
        $values = array('false', 'true');

        if (is_null($index)) {
            $indexprefix = '';
        } else {
            $indexprefix = $index.'. ';
        }

        $label = $this->construct_label($elementname);
        $mform->addElement('advcheckbox', $elementname, $indexprefix.get_string($label, 'tool_pluginskel'), '', null, $values);
        $mform->addHelpButton($elementname, $label, 'tool_pluginskel');

        if (!empty($recipe[$variablename]) && !empty($values[$recipe[$variablename]])) {
            $mform->getElement($elementname)->setChecked(true);
        }

        if (!empty($recipe[$variablename])) {
            $mform->getElement($elementname)->setChecked(true);
        }

        if (isset($templatevar['default'])) {
            $mform->setDefault($elementname, $templatevar['default']);
        }
    }

    /**
     * Adds a text element to the form.
     *
     * @param string $elementname Element form name.
     * @param string[] $templatevar The template variable
     * @param string[] $recipe The recipe.
     * @param string $index Optional index for the element in an array variable.
     */
    protected function add_text_element($elementname, $templatevar, $recipe, $index = null) {

        $mform = $this->_form;
        $variablename = $templatevar['name'];

        if (is_null($index)) {
            $indexprefix = '';
        } else {
            $indexprefix = $index.'. ';
        }

        $label = $this->construct_label($elementname);
        $mform->addElement('text', $elementname, $indexprefix.get_string($label, 'tool_pluginskel'));
        $mform->addHelpButton($elementname, $label, 'tool_pluginskel');

        if ($templatevar['type'] == 'int') {
            $mform->setType($elementname, PARAM_INT);
        } else {
            $mform->setType($elementname, PARAM_RAW);
        }

        if (!empty($recipe[$variablename])) {
            $mform->getElement($elementname)->setValue($recipe[$variablename]);
        }

        if (!empty($templatevar['required'])) {
            $mform->addRule($elementname, null, 'required', null, 'client', false, true);
        }

        if (isset($templatevar['default'])) {
            $mform->setDefault($elementname, $templatevar['default']);
        }
    }

    /**
     * Adds a fieldset element which describes a numeric array variable.
     *
     * @param string[] $templatevar The template variable
     * @param string[] $recipe The recipe.
     * @param string $parentname The parent name inside the recipe.
     */
    protected function add_numeric_fieldset($templatevar, $recipe, $parentname = '') {

        $mform = $this->_form;
        $templatevalues = $templatevar['values'];
        $variablename = $templatevar['name'];

        if (empty($parentname)) {
            $fieldsetname = $variablename;
            $countname = $variablename.'count';
            $recipevalues = empty($recipe[$variablename]) ? array() : $recipe[$variablename];
        } else {
            $fieldsetname = $parentname.'['.$variablename.']';
            $countname = $parentname.'_'.$variablename.'count';
            $recipevalues = empty($recipe[$parentname][$variablename]) ? array() : $recipe[$parentname][$variablename];
        }

        if (empty($this->_customdata[$countname])) {
            // Create only one entry in the fieldset if more haven't been specified.
            $count = 1;
        } else {
            $count = (int) $this->_customdata[$countname];
        }

        // Constructing the fieldset field values from the recipe.
        $values = array();
        if (!empty($recipevalues)) {
            $values = $this->get_numeric_array_variable_from_recipe($fieldsetname, $templatevalues, $recipevalues);
        }

        $headerlabel = $this->construct_label($fieldsetname);
        $mform->addElement('header', $fieldsetname, get_string($headerlabel, 'tool_pluginskel'));
        if (!empty($templatevar['required'])) {
            $mform->setExpanded($fieldsetname, true);
        } else {
            $mform->setExpanded($fieldsetname, false);
        }

        $this->add_numeric_fieldset_elements($fieldsetname, $templatevalues, $values, $count);

        $buttonarr = array();
        $buttonarr[] = $mform->createElement('button', 'addmore_'.$fieldsetname,
                                              get_string('addmore_'.$variablename, 'tool_pluginskel'));
        $buttonarr[] = $mform->createElement('button', 'delete_'.$fieldsetname,
                                              get_string('delete_'.$variablename, 'tool_pluginskel'));
        $mform->addGroup($buttonarr, 'buttons_'.$fieldsetname, '', array('    '), false);

        $mform->addElement('hidden', $countname, $count);
        $mform->setType($countname, PARAM_INT);
    }

    /**
     * Adds a fieldset element which describes an associative array variable.
     *
     * @param string[] $templatevar The template variable
     * @param string[] $recipe The recipe.
     * @param string $parentname The parent name inside the recipe.
     */
    protected function add_associative_fieldset($templatevar, $recipe, $parentname = '') {

        $mform = $this->_form;
        $templatevalues = $templatevar['values'];
        $variablename = $templatevar['name'];

        if (empty($parentname)) {
            $fieldsetname = $variablename;
            $recipevalues = empty($recipe[$variablename]) ? array() : $recipe[$variablename];
        } else {
            $fieldsetname = $parentname.'['.$variablename.']';
            $recipevalues = empty($recipe[$parentname][$variablename]) ? array() : $recipe[$parentname][$variablename];
        }

        $values = array();
        if (!empty($recipevalues)) {
            $values = $this->get_associative_array_variable_from_recipe($templatevalues, $recipevalues);
        }

        $headerlabel = $this->construct_label($fieldsetname);
        $mform->addElement('header', $fieldsetname, get_string($headerlabel, 'tool_pluginskel'));
        if (!empty($templatevar['required'])) {
            $mform->setExpanded($fieldsetname, true);
        } else {
            $mform->setExpanded($fieldsetname, false);
        }

        // Adding the fieldset elements to the page.
        foreach ($templatevalues as $nestedvariable) {

            $type = $nestedvariable['type'];

            if ($type === 'numeric-array') {
                $this->add_nested_array_variable($fieldsetname, $nestedvariable, $recipevalues);
            } else {
                $elementname = $fieldsetname.'['.$nestedvariable['name'].']';
                $this->add_fieldset_element($elementname, $nestedvariable, $recipevalues);
            }
        }
    }

    /**
     * Returns the values of a numeric array variable from the recipe.
     *
     * @param string $fieldsetname The name of the fieldset.
     * @param string $templatefieldset The template variables associated with the fieldset.
     * @param string[] $fieldsetrecipe The part of the recipe that contains the values for the fieldset.
     */
    protected function get_numeric_array_variable_from_recipe($fieldsetname, $templatefieldset, $fieldsetrecipe) {

        $ret = array();

        foreach ($fieldsetrecipe as $fieldsetvalues) {
            $currentvalue = array();
            foreach ($templatefieldset as $field) {
                $fieldname = $field['name'];

                // Use only the recipe fields that are part of the template.
                if (isset($fieldsetvalues[$fieldname])) {
                    $currentvalue[$fieldname] = $fieldsetvalues[$fieldname];
                }
            }

            if (!empty($currentvalue)) {
                $ret[] = $currentvalue;
            }
        }

        return $ret;
    }

    /**
     * Returns the values of an associative array variable from the recipe.
     *
     * @param string[] $templatevariables The template variables associated with the fieldset.
     * @param string[] $fieldsetrecipe The part of the recipe that contains the values for the fieldset.
     */
    protected function get_associative_array_variable_from_recipe($templatevariables, $fieldsetrecipe) {

        $ret = array();

        foreach ($templatevariables as $variable) {
            $variablename = $variable['name'];
            if (isset($fieldsetrecipe[$variablename])) {
                $ret[$variablename] = $fieldsetrecipe[$variablename];
            }
        }

        return $ret;
    }

    /**
     * Adds an element to a fieldset.
     *
     * @param string $elementname
     * @param string $variable The template variable the element represents.
     * @param string[] $fieldsetvalues The values for all the fieldset elements.
     * @param int|string|null $indexprefix The index of the element in the array.
     */
    protected function add_fieldset_element($elementname, $variable, $fieldsetvalues, $indexprefix = null) {

        $type = $variable['type'];
        $variablename = $variable['name'];

        if ($type == 'text' || $type == 'int') {
            $this->add_text_element($elementname, $variable, $fieldsetvalues, $indexprefix);
        }

        if ($type == 'multiple-options') {
            $this->add_select_element($elementname, $variable, $fieldsetvalues, false, $indexprefix);
        }

        if ($type == 'boolean') {
            if (empty($variable['required'])) {
                $this->add_select_element($elementname, $variable, $fieldsetvalues, true, $indexprefix);
            } else {
                $this->add_advcheckbox_element($elementname, $variable, $fieldsetvalues, $indexprefix);
            }
        }
    }

    /**
     * Adds the elements of a fieldset based on a numeric array variable and the recipe.
     *
     * @param string $fieldsetname The name of the fieldset.
     * @param string[] $templatevars The template variables to add.
     * @param string[] $recipevalues The values for the elements taken from recipe.
     * @param int $count The number of elements to add.
     * @param int $parentindex The index of the parent variable in case of nested array variables.
     */
    protected function add_numeric_fieldset_elements($fieldsetname, $templatevars, $recipevalues, $count, $parentindex = null) {

        $mform = $this->_form;

        // Adding elements which have values specified in the recipe.
        if (!empty($recipevalues)) {
            foreach ($recipevalues as $index => $fieldsetvalues) {

                foreach ($templatevars as $variable) {

                    if ($variable['type'] === 'numeric-array') {
                        $parentname = $fieldsetname.'['.$index.']';
                        $this->add_nested_array_variable($parentname, $variable, $fieldsetvalues, $index);
                    } else {
                        $elementname = $fieldsetname.'['.$index.']['.$variable['name'].']';
                        $indexprefix = is_null($parentindex) ? $index : $parentindex.'.'.$index;
                        $this->add_fieldset_element($elementname, $variable, $fieldsetvalues, $indexprefix);
                    }
                }

                // Add a newline between array values.
                if ($index < count($recipevalues) - 1) {
                    $mform->addElement('html', '<br/>');
                }
            }
        }

        // Adding empty elements until we have the required number of elements in the fieldset.
        $currentcount = count($recipevalues);
        while ($currentcount < $count) {
            foreach ($templatevars as $variable) {

                if ($variable['type'] == 'numeric-array') {
                    $parentname = $fieldsetname.'['.$currentcount.']';
                    $this->add_nested_array_variable($parentname, $variable, array(), $currentcount);
                } else {
                    $elementname = $fieldsetname.'['.$currentcount.']['.$variable['name'].']';
                    $indexprefix = is_null($parentindex) ? $currentcount : ($parentindex.'.'.$currentcount);
                    $this->add_fieldset_element($elementname, $variable, array(), $indexprefix);
                }
            }

            $currentcount += 1;

            // Add a newline between groups of elements.
            if ($currentcount < $count) {
                $mform->addElement('html', '<br/>');
            }
        }
    }

    /**
     * Adds an array variable nested inside a fieldset.
     *
     * @param string $parentname
     * @param string[] $nestedvariable The variable.
     * @param string[] $nestedrecipe The recipe for the nested variable.
     * @param int $parentindex The index of the parent numeric array variable.
     */
    protected function add_nested_array_variable($parentname, $nestedvariable, $nestedrecipe, $parentindex = null) {

        $mform = $this->_form;
        $variablename = $nestedvariable['name'];
        $formname = $parentname.'['.$variablename.']';
        $templatevalues = $nestedvariable['values'];

        $countname = str_replace('][', '_', $parentname);
        $countname = str_replace('[', '_', $countname);
        $countname = str_replace(']', '_', $countname);
        if (substr($countname, -1) !== '_') {
            $countname = $countname.'_';
        }
        $countname = $countname.$variablename.'count';
        if (empty($this->_customdata[$countname])) {
            // Create only one entry in the fieldset if more haven't been specified.
            $count = 1;
        } else {
            $count = (int) $this->_customdata[$countname];
        }

        $label = $this->construct_label($parentname.$variablename);
        $mform->addElement('static', $formname, get_string($label, 'tool_pluginskel').':');

        $recipevalues = array();
        if (!empty($nestedrecipe[$variablename]) && is_array($nestedrecipe[$variablename])) {
            $recipevalues = $this->get_numeric_array_variable_from_recipe($variablename, $templatevalues,
                                                                          $nestedrecipe[$variablename]);
        }

        $this->add_numeric_fieldset_elements($formname, $templatevalues, $recipevalues, $count, $parentindex);

        $mform->addElement('button', 'addmore_'.$formname, get_string('addmore_'.$variablename, 'tool_pluginskel'));

        $mform->addElement('hidden', $countname, $count);
        $mform->setType($countname, PARAM_INT);
    }

    /**
     * Constructs the recipe from the form data.
     *
     * @return string[] The recipe.
     */
    public function get_recipe() {

        $recipe = array();

        $formdata = (array) $this->get_data();
        $component = $this->_customdata['recipe']['component'];

        $generalvars = tool_pluginskel\local\util\manager::get_general_variables();
        $componentvars = tool_pluginskel\local\util\manager::get_component_variables($component);
        $featuresvars = tool_pluginskel\local\util\manager::get_features_variables();

        foreach ($generalvars as $variable) {

            $variablename = $variable['name'];
            $type = $variable['type'];

            if ($type === 'numeric-array') {
                $value = $this->get_numeric_array_variable_from_formdata($formdata[$variablename], $variable);
            } else if ($type === 'associative-array') {
                $value = $this->get_associative_array_variable_from_formdata($formdata[$variablename], $variable);
            } else {
                $value = $this->get_variable_value($formdata[$variablename], $variable);
            }

            if (!empty($value)) {
                $recipe[$variablename] = $value;
            }
        }

        $componentfeatures = $this->componenttype.'_features';
        $componentformdata = isset($formdata[$componentfeatures]) ? $formdata[$componentfeatures] : array();
        foreach ($componentvars as $variable) {

            $variablename = $variable['name'];
            $type = $variable['type'];

            if ($type === 'numeric-array') {
                $value = $this->get_numeric_array_variable_from_formdata($componentformdata[$variablename], $variable);
            } else if ($type === 'associative-array') {
                $value = $this->get_associative_array_variable_from_formdata($componentformdata[$variablename], $variable);
            } else if (!empty($componentformdata[$variablename])) {
                $value = $this->get_variable_value($componentformdata[$variablename], $variable);
            }

            if (!is_null($value)) {
                $recipe[$componentfeatures][$variablename] = $value;
            }
        }

        foreach ($featuresvars as $variable) {

            $variablename = $variable['name'];
            $type = $variable['type'];

            // Only array common features are at the root of the recipe.
            if ($type === 'numeric-array') {
                $value = $this->get_numeric_array_variable_from_formdata($formdata[$variablename], $variable);
                if (!is_null($value)) {
                    $recipe[$variablename] = $value;
                }
            } else if ($type === 'associative-array') {
                $value = $this->get_associative_array_variable_from_formdata($formdata[$variablename], $variable);
                if (!is_null($value)) {
                    $recipe[$variablename] = $value;
                }
            } else {
                $value = $this->get_variable_value($formdata['features'][$variablename], $variable);
                if (!is_null($value)) {
                    $recipe['features'][$variablename] = $value;
                }
            }
        }

        $recipe['component'] = $component;

        return $recipe;
    }

    /**
     * Returns the form value of an associative array variable.
     *
     * @param string[] $variableformdata The form value of the array variable.
     * @param string[] $templatevariable The variable definition from the template.
     * @return string[]|null Null for an empty value.
     */
    protected function get_associative_array_variable_from_formdata($variableformdata, $templatevariable) {

        $ret = array();

        foreach ($templatevariable['values'] as $fieldvariable) {

            $type = $fieldvariable['type'];
            $fieldname = $fieldvariable['name'];

            $value = null;
            if (isset($variableformdata[$fieldname])) {
                if ($type === 'numeric-array') {
                    $value = $this->get_numeric_array_variable_from_formdata($variableformdata[$fieldname], $fieldvariable);
                } else {
                    $value = $this->get_variable_value($variableformdata[$fieldname], $fieldvariable);
                }
            }

            if (!is_null($value)) {
                $ret[$fieldname] = $value;
            }
        }

        if (empty($ret)) {
            return null;
        } else {
            return $ret;
        }
    }

    /**
     * Returns the form value of a numeric array variable.
     *
     * @param string[] $variableformdata The form value of the array variable.
     * @param string[] $templatevariable The variable definition from the template.
     * @return string[]|null Null for an empty value.
     */
    protected function get_numeric_array_variable_from_formdata($variableformdata, $templatevariable) {

        $ret = array();

        foreach ($variableformdata as $formvalues) {
            $currentvalue = array();
            foreach ($formvalues as $field => $value) {

                if (!empty($value)) {
                    foreach ($templatevariable['values'] as $nestedvariable) {
                        if ($nestedvariable['name'] == $field) {
                            $variable = $nestedvariable;
                            break;
                        }
                    }

                    if ($variable['type'] === 'numeric-array') {
                        $value = $this->get_numeric_array_variable_from_formdata($value, $variable);
                    } else {
                        $value = $this->get_variable_value($value, $variable);
                    }

                    if (!is_null($value)) {
                        $currentvalue[$field] = $value;
                    }
                }
            }

            if (!empty($currentvalue)) {
                $ret[] = $currentvalue;
            }
        }

        if (empty($ret)) {
            return null;
        } else {
            return $ret;
        }
    }

    /**
     * Returns the value of a form variable.
     *
     * @param string[] $formvalue The form value of the array variable.
     * @param string[] $templatevariable The variable definition from the template.
     * @return mixed The variable value, with the type based on the variable hint.
     */
    protected function get_variable_value($formvalue, $templatevariable) {

        $value = null;

        $type = $templatevariable['type'];
        $variablename = $templatevariable['name'];

        if ($type === 'numeric-array') {
            return null;
        }

        if ($type === 'multiple-options') {
            // Ignoring 'undefined' select options.
            if ($formvalue !== 'undefined') {
                $value = $formvalue;
            }
        } else if ($type === 'boolean') {
            if ($formvalue !== 'undefined') {
                if ($formvalue === 'true') {
                    $value = true;
                } else {
                    $value = false;
                }
            }
        } else if ($type === 'int') {
            $value = intval($formvalue);
        } else {
            $value = $formvalue;
        }

        return $value;
    }
}
