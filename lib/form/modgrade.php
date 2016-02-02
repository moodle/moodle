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
 * Drop down form element to select the grade
 *
 * Contains HTML class for a drop down element to select the grade for an activity,
 * used in mod update form
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once "$CFG->libdir/form/select.php";
require_once("HTML/QuickForm/element.php");
require_once($CFG->dirroot.'/lib/form/group.php');
require_once($CFG->dirroot.'/lib/grade/grade_scale.php');

/**
 * Drop down form element to select the grade
 *
 * HTML class for a drop down element to select the grade for an activity,
 * used in mod update form
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_modgrade extends MoodleQuickForm_group{

    /**
     * Constructor
     *
     * @param string $elementname Element's name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display. Not used.
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementname = null, $elementlabel = null, $options = array(), $attributes = null) {
        // TODO MDL-52313 Replace with the call to parent::__construct().
        HTML_QuickForm_element::__construct($elementname, $elementlabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'modgrade';
    }

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function MoodleQuickForm_modgrade($elementname = null, $elementlabel = null, $options = array(), $attributes = null) {
        self::__construct($elementname, $elementlabel, $options, $attributes);
    }

    /**
     * Create elements for this group.
     */
    public function _createElements() {
        global $COURSE, $CFG;
        $attributes = $this->getAttributes();
        if (is_null($attributes)) {
            $attributes = array();
        }

        $this->_elements = array();

        // Create main elements
        // We have to create the scale and point elements first, as we need their IDs.

        // Grade scale select box.
        $scales = get_scales_menu($COURSE->id);
        $langscale = get_string('modgradetypescale', 'grades');
        $scaleselect = @MoodleQuickForm::createElement('select', 'modgrade_scale', $langscale, $scales, $attributes);
        $scaleselect->setHiddenLabel = false;
        $scaleselectid = $this->generate_modgrade_subelement_id('modgrade_scale');
        $scaleselect->updateAttributes(array('id' => $scaleselectid));

        // Maximum grade textbox.
        $langmaxgrade = get_string('modgrademaxgrade', 'grades');
        $maxgrade = @MoodleQuickForm::createElement('text', 'modgrade_point', $langmaxgrade, array());
        $maxgrade->setHiddenLabel = false;
        $maxgradeid = $this->generate_modgrade_subelement_id('modgrade_point');
        $maxgrade->updateAttributes(array('id' => $maxgradeid));

        // Grade type select box.
        $gradetype = array(
            'none' => get_string('modgradetypenone', 'grades'),
            'scale' => get_string('modgradetypescale', 'grades'),
            'point' => get_string('modgradetypepoint', 'grades'),
        );
        $langtype = get_string('modgradetype', 'grades');
        $typeselect = @MoodleQuickForm::createElement('select', 'modgrade_type', $langtype, $gradetype, $attributes, true);
        $typeselect->setHiddenLabel = false;
        $typeselectid = $this->generate_modgrade_subelement_id('modgrade_type');
        $typeselect->updateAttributes(array('id' => $typeselectid));

        // Add elements.

        // Grade type select box.
        $label = html_writer::tag('label', $typeselect->getLabel(), array('for' => $typeselect->getAttribute('id')));
        $this->_elements[] = @MoodleQuickForm::createElement('static', 'gradetypelabel', '', '&nbsp;'.$label);
        $this->_elements[] = $typeselect;
        $this->_elements[] = @MoodleQuickForm::createElement('static', 'gradetypespacer', '', '<br />');

        // Grade scale select box.
        $label = html_writer::tag('label', $scaleselect->getLabel(), array('for' => $scaleselectid));
        $this->_elements[] = @MoodleQuickForm::createElement('static', 'scalelabel', '', $label);
        $this->_elements[] = $scaleselect;
        $this->_elements[] = @MoodleQuickForm::createElement('static', 'scalespacer', '', '<br />');

        // Maximum grade textbox.
        $label = html_writer::tag('label', $maxgrade->getLabel(), array('for' => $maxgradeid));
        $this->_elements[] = @MoodleQuickForm::createElement('static', 'pointlabel', '', $label);
        $this->_elements[] = $maxgrade;
        $this->_elements[] = @MoodleQuickForm::createElement('static', 'pointspacer', '', '<br />');
    }

    /**
     * Calculate the output value for the element as a whole.
     *
     * @param array $submitvalues The incoming values from the form.
     * @param bool $notused Not used.
     * @return array Return value for the element, formatted like field name => value.
     */
    public function exportValue(&$submitvalues, $notused = false) {
        global $COURSE;

        // Get the values from all the child elements.
        $vals = array();
        foreach ($this->_elements as $element) {
            $thisexport = $element->exportValue($submitvalues[$this->getName()], true);
            if (!is_null($thisexport)) {
                $vals += $thisexport;
            }
        }

        $type = (isset($vals['modgrade_type'])) ? $vals['modgrade_type'] : 'none';
        $point = (isset($vals['modgrade_point'])) ? $vals['modgrade_point'] : null;
        $scale = (isset($vals['modgrade_scale'])) ? $vals['modgrade_scale'] : null;
        $return = $this->process_value($type, $scale, $point);
        return array($this->getName() => $return);
    }

    /**
     * Process the value for the group based on the selected grade type, and the input for the scale and point elements.
     *
     * @param  string $type The value of the grade type select box. Can be 'none', 'scale', or 'point'
     * @param  string|int $scale The value of the scale select box.
     * @param  string|int $point The value of the point grade textbox.
     * @return int The resulting value
     */
    protected function process_value($type='none', $scale=null, $point=null) {
        global $COURSE;
        $val = 0;
        switch ($type) {
            case 'point':
                if ($this->validate_point($point) === true) {
                    $val = (int)$point;
                }
                break;

            case 'scale':
                if ($this->validate_scale($scale)) {
                    $val = (int)(-$scale);
                }
                break;
        }
        return $val;
    }

    /**
     * Determines whether a given value is a valid scale selection.
     *
     * @param string|int $val The value to test.
     * @return bool Valid or invalid
     */
    protected function validate_scale($val) {
        global $COURSE;
        $scales = get_scales_menu($COURSE->id);
        return (!empty($val) && isset($scales[(int)$val])) ? true : false;
    }

    /**
     * Determines whether a given value is a valid point selection.
     *
     * @param string|int $val The value to test.
     * @return bool Valid or invalid
     */
    protected function validate_point($val) {
        if (empty($val)) {
            return false;
        }
        $maxgrade = (int)get_config('core', 'gradepointmax');
        $isintlike = ((string)(int)$val === $val) ? true : false;
        return ($isintlike === true && $val > 0 && $val <= $maxgrade) ? true : false;
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element.
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param moodleform $caller calling object
     * @return mixed
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        switch ($event) {
            case 'createElement':
                // The first argument is the name.
                $name = $arg[0];

                // Set disable actions.
                $caller->disabledIf($name.'[modgrade_scale]', $name.'[modgrade_type]', 'neq', 'scale');
                $caller->disabledIf($name.'[modgrade_point]', $name.'[modgrade_type]', 'neq', 'point');

                // Set validation rules for the sub-elements belonging to this element.
                // A handy note: the parent scope of a closure is the function in which the closure was declared.
                // Because of this using $this is safe despite the closures being called statically.
                // A nasty magic hack!
                $checkmaxgrade = function($val) {
                    // Closure to validate a max points value. See the note above about scope if this confuses you.
                    if (isset($val['modgrade_type']) && $val['modgrade_type'] === 'point') {
                        if (!isset($val['modgrade_point'])) {
                            return false;
                        }
                        return $this->validate_point($val['modgrade_point']);
                    }
                    return true;
                };
                $checkvalidscale = function($val) {
                    // Closure to validate a scale value. See the note above about scope if this confuses you.
                    if (isset($val['modgrade_type']) && $val['modgrade_type'] === 'scale') {
                        if (!isset($val['modgrade_scale'])) {
                            return false;
                        }
                        return $this->validate_scale($val['modgrade_scale']);
                    }
                    return true;
                };

                $maxgradeexceeded = get_string('modgradeerrorbadpoint', 'grades', get_config('core', 'gradepointmax'));
                $invalidscale = get_string('modgradeerrorbadscale', 'grades');
                // When creating the rules the sixth arg is $force, we set it to true because otherwise the form
                // will attempt to validate the existence of the element, we don't want this because the element
                // is being created right now and doesn't actually exist as a registered element yet.
                $caller->addRule($name, $maxgradeexceeded, 'callback', $checkmaxgrade, 'server', false, true);
                $caller->addRule($name, $invalidscale, 'callback', $checkvalidscale, 'server', false, true);

                break;

            case 'updateValue':
                // As this is a group element with no value of its own we are only interested in situations where the
                // default value or a constant value are being provided to the actual element.
                // In this case we expect an int that is going to translate to a scale if negative, or to max points
                // if positive.

                // A constant value should be given as an int.
                // The default value should be an int and should really be $CFG->gradepointdefault.
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    if ($caller->isSubmitted()) {
                        break;
                    }
                    $value = $this->_findValue($caller->_defaultValues);
                }

                if (!is_null($value) && !is_scalar($value)) {
                    // Something unexpected (likely an array of subelement values) has been given - this will be dealt
                    // with somewhere else - where exactly... likely the subelements.
                    debugging('An invalid value (type '.gettype($value).') has arrived at '.__METHOD__, DEBUG_DEVELOPER);
                    break;
                }

                // Set element state for existing data.
                // This is really a pretty hacky thing to do, when data is being set the group element is called
                // with the data first and the subelements called afterwards.
                // This means that the subelements data (inc const and default values) can be overridden by form code.
                // So - when we call this code really we can't be sure that will be the end value for the element.
                if (!empty($this->_elements)) {
                    if (!empty($value)) {
                        if ($value < 0) {
                            $this->_elements[1]->setValue('scale');
                            $this->_elements[4]->setValue(($value * -1));
                        } else if ($value > 0) {
                            $this->_elements[1]->setValue('point');
                            $this->_elements[7]->setValue($value);
                        }
                    } else {
                        $this->_elements[1]->setValue('none');
                        $this->_elements[7]->setValue('');
                    }
                }
                break;
        }

        // Always let the parent do its thing!
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Generates the id attribute for the subelement of the modgrade group.
     *
     * Uses algorithm similar to what {@link HTML_QuickForm_element::_generateId()}
     * does but takes the name of the wrapping modgrade group into account.
     *
     * @param string $subname the name of the HTML_QuickForm_element in this modgrade group
     * @return string
     */
    protected function generate_modgrade_subelement_id($subname) {
        $gid = str_replace(array('[', ']'), array('_', ''), $this->getName());
        return clean_param('id_'.$gid.'_'.$subname, PARAM_ALPHANUMEXT);
    }
}
