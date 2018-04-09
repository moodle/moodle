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
class MoodleQuickForm_modgrade extends MoodleQuickForm_group {

    /** @var boolean $isupdate Is this an add or an update ? */
    public $isupdate = false;

    /** @var float $currentgrade The current grademax for the grade_item */
    public $currentgrade = false;

    /** @var boolean $hasgrades Has this grade_item got any real grades (with values) */
    public $hasgrades = false;

    /** @var boolean $canrescale Does this activity support rescaling grades? */
    public $canrescale = false;

    /** @var int $currentscaleid The current scale id */
    public $currentscaleid = null;

    /** @var string $currentgradetype The current gradetype - can either be 'none', 'scale', or 'point' */
    public $currentgradetype = 'none';

    /** @var boolean $useratings Set to true if the activity is using ratings, false otherwise */
    public $useratings = false;

    /** @var MoodleQuickForm_select $gradetypeformelement */
    private $gradetypeformelement;

    /** @var MoodleQuickForm_select $scaleformelement */
    private $scaleformelement;

    /** @var MoodleQuickForm_text $maxgradeformelement */
    private $maxgradeformelement;

    /**
     * Constructor
     *
     * @param string $elementname Element's name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display. Required - must contain the following options:
     *              'isupdate' - is this a new module or are we editing an existing one?
     *              'currentgrade' - the current grademax in the database for this gradeitem
     *              'hasgrades' - whether or not the grade_item has existing grade_grades
     *              'canrescale' - whether or not the activity supports rescaling grades
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementname = null, $elementlabel = null, $options = array(), $attributes = null) {
        // TODO MDL-52313 Replace with the call to parent::__construct().
        HTML_QuickForm_element::__construct($elementname, $elementlabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'modgrade';
        $this->isupdate = !empty($options['isupdate']);
        if (isset($options['currentgrade'])) {
            $this->currentgrade = $options['currentgrade'];
        }
        if (isset($options['currentgradetype'])) {
            $gradetype = $options['currentgradetype'];
            switch ($gradetype) {
                case GRADE_TYPE_NONE :
                    $this->currentgradetype = 'none';
                    break;
                case GRADE_TYPE_SCALE :
                    $this->currentgradetype = 'scale';
                    break;
                case GRADE_TYPE_VALUE :
                    $this->currentgradetype = 'point';
                    break;
            }
        }
        if (isset($options['currentscaleid'])) {
            $this->currentscaleid = $options['currentscaleid'];
        }
        $this->hasgrades = !empty($options['hasgrades']);
        $this->canrescale = !empty($options['canrescale']);
        $this->useratings = !empty($options['useratings']);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_modgrade($elementname = null, $elementlabel = null, $options = array(), $attributes = null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementname, $elementlabel, $options, $attributes);
    }

    /**
     * Create elements for this group.
     */
    public function _createElements() {
        global $COURSE, $CFG, $OUTPUT;
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
        $this->scaleformelement = $this->createFormElement('select', 'modgrade_scale', $langscale,
            $scales, $attributes);
        $this->scaleformelement->setHiddenLabel(true);
        $scaleformelementid = $this->generate_modgrade_subelement_id('modgrade_scale');
        $this->scaleformelement->updateAttributes(array('id' => $scaleformelementid));

        // Maximum grade textbox.
        $langmaxgrade = get_string('modgrademaxgrade', 'grades');
        $this->maxgradeformelement = $this->createFormElement('text', 'modgrade_point', $langmaxgrade, array());
        $this->maxgradeformelement->setHiddenLabel(true);
        $maxgradeformelementid = $this->generate_modgrade_subelement_id('modgrade_point');
        $this->maxgradeformelement->updateAttributes(array('id' => $maxgradeformelementid));

        // Grade type select box.
        $gradetype = array(
            'none' => get_string('modgradetypenone', 'grades'),
            'scale' => get_string('modgradetypescale', 'grades'),
            'point' => get_string('modgradetypepoint', 'grades'),
        );
        $langtype = get_string('modgradetype', 'grades');
        $this->gradetypeformelement = $this->createFormElement('select', 'modgrade_type', $langtype, $gradetype,
            $attributes, true);
        $this->gradetypeformelement->setHiddenLabel(true);
        $gradetypeformelementid = $this->generate_modgrade_subelement_id('modgrade_type');
        $this->gradetypeformelement->updateAttributes(array('id' => $gradetypeformelementid));

        if ($this->isupdate && $this->hasgrades) {
            $this->gradetypeformelement->updateAttributes(array('disabled' => 'disabled'));
            $this->scaleformelement->updateAttributes(array('disabled' => 'disabled'));

            // Check box for options for processing existing grades.
            if ($this->canrescale) {
                $langrescalegrades = get_string('modgraderescalegrades', 'grades');
                $choices = array();
                $choices[''] = get_string('choose');
                $choices['no'] = get_string('no');
                $choices['yes'] = get_string('yes');
                $rescalegradesselect = $this->createFormElement('select',
                    'modgrade_rescalegrades',
                    $langrescalegrades,
                    $choices);
                $rescalegradesselect->setHiddenLabel(true);
                $rescalegradesselectid = $this->generate_modgrade_subelement_id('modgrade_rescalegrades');
                $rescalegradesselect->updateAttributes(array('id' => $rescalegradesselectid));
            }
        }

        // Add elements.
        if ($this->isupdate && $this->hasgrades) {
            // Set a message so the user knows why they can not alter the grade type or scale.
            if ($this->currentgradetype == 'scale') {
                $gradesexistmsg = get_string('modgradecantchangegradetyporscalemsg', 'grades');
            } else if ($this->canrescale) {
                $gradesexistmsg = get_string('modgradecantchangegradetypemsg', 'grades');
            } else {
                $gradesexistmsg = get_string('modgradecantchangegradetype', 'grades');
            }

            $gradesexisthtml = '<div class=\'alert\'>' . $gradesexistmsg . '</div>';
            $this->_elements[] = $this->createFormElement('static', 'gradesexistmsg', '', $gradesexisthtml);
        }

        // Grade type select box.
        $label = html_writer::tag('label', $this->gradetypeformelement->getLabel(),
            array('for' => $this->gradetypeformelement->getAttribute('id')));
        $this->_elements[] = $this->createFormElement('static', 'gradetypelabel', '', '&nbsp;'.$label);
        $this->_elements[] = $this->gradetypeformelement;
        $this->_elements[] = $this->createFormElement('static', 'gradetypespacer', '', '<br />');

        // Only show the grade scale select box when applicable.
        if (!$this->isupdate || !$this->hasgrades || $this->currentgradetype == 'scale') {
            $label = html_writer::tag('label', $this->scaleformelement->getLabel(),
                array('for' => $this->scaleformelement->getAttribute('id')));
            $this->_elements[] = $this->createFormElement('static', 'scalelabel', '', $label);
            $this->_elements[] = $this->scaleformelement;
            $this->_elements[] = $this->createFormElement('static', 'scalespacer', '', '<br />');
        }

        if ($this->isupdate && $this->hasgrades && $this->canrescale && $this->currentgradetype == 'point') {
            // We need to know how to apply any changes to maxgrade - ie to either update, or don't touch exising grades.
            $label = html_writer::tag('label', $rescalegradesselect->getLabel(),
                array('for' => $rescalegradesselect->getAttribute('id')));
            $labelhelp = new help_icon('modgraderescalegrades', 'grades');
            $this->_elements[] = $this->createFormElement('static', 'scalelabel', '', $label . $OUTPUT->render($labelhelp));
            $this->_elements[] = $rescalegradesselect;
            $this->_elements[] = $this->createFormElement('static', 'scalespacer', '', '<br />');
        }

        // Only show the max points form element when applicable.
        if (!$this->isupdate || !$this->hasgrades || $this->currentgradetype == 'point') {
            $label = html_writer::tag('label', $this->maxgradeformelement->getLabel(),
                array('for' => $this->maxgradeformelement->getAttribute('id')));
            $this->_elements[] = $this->createFormElement('static', 'pointlabel', '', $label);
            $this->_elements[] = $this->maxgradeformelement;
            $this->_elements[] = $this->createFormElement('static', 'pointspacer', '', '<br />');
        }
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
        $rescalegrades = (isset($vals['modgrade_rescalegrades'])) ? $vals['modgrade_rescalegrades'] : null;

        $return = $this->process_value($type, $scale, $point, $rescalegrades);
        return array($this->getName() => $return, $this->getName() . '_rescalegrades' => $rescalegrades);
    }

    /**
     * Process the value for the group based on the selected grade type, and the input for the scale and point elements.
     *
     * @param  string $type The value of the grade type select box. Can be 'none', 'scale', or 'point'
     * @param  string|int $scale The value of the scale select box.
     * @param  string|int $point The value of the point grade textbox.
     * @param  string $rescalegrades The value of the rescalegrades select.
     * @return int The resulting value
     */
    protected function process_value($type='none', $scale=null, $point=null, $rescalegrades=null) {
        global $COURSE;
        $val = 0;
        if ($this->isupdate && $this->hasgrades && $this->canrescale && $this->currentgradetype == 'point' && empty($rescalegrades)) {
            // If the maxgrade field is disabled with javascript, no value is sent with the form and mform assumes the default.
            // If the user was forced to choose a rescale option - and they haven't - prevent any changes to the max grade.
            return (string)unformat_float($this->currentgrade);
        }
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
        $this->setMoodleForm($caller);
        switch ($event) {
            case 'createElement':
                // The first argument is the name.
                $name = $arg[0];

                // Set disable actions.
                $caller->disabledIf($name.'[modgrade_scale]', $name.'[modgrade_type]', 'neq', 'scale');
                $caller->disabledIf($name.'[modgrade_point]', $name.'[modgrade_type]', 'neq', 'point');
                $caller->disabledIf($name.'[modgrade_rescalegrades]', $name.'[modgrade_type]', 'neq', 'point');

                // Set validation rules for the sub-elements belonging to this element.
                // A handy note: the parent scope of a closure is the function in which the closure was declared.
                // Because of this using $this is safe despite the closures being called statically.
                // A nasty magic hack!
                $checkgradetypechange = function($val) {
                    // Nothing is affected by changes to the grade type if there are no grades yet.
                    if (!$this->hasgrades) {
                        return true;
                    }
                    // Check if we are changing the grade type when grades are present.
                    if (isset($val['modgrade_type']) && $val['modgrade_type'] !== $this->currentgradetype) {
                        return false;
                    }
                    return true;
                };
                $checkscalechange = function($val) {
                    // Nothing is affected by changes to the scale if there are no grades yet.
                    if (!$this->hasgrades) {
                        return true;
                    }
                    // Check if we are changing the scale type when grades are present.
                    // If modgrade_type is empty then use currentgradetype.
                    $gradetype = isset($val['modgrade_type']) ? $val['modgrade_type'] : $this->currentgradetype;
                    if ($gradetype === 'scale') {
                        if (isset($val['modgrade_scale']) && ($val['modgrade_scale'] !== $this->currentscaleid)) {
                            return false;
                        }
                    }
                    return true;
                };
                $checkmaxgradechange = function($val) {
                    // Nothing is affected by changes to the max grade if there are no grades yet.
                    if (!$this->hasgrades) {
                        return true;
                    }
                    // If we are not using ratings we can change the max grade.
                    if (!$this->useratings) {
                        return true;
                    }
                    // Check if we are changing the max grade if we are using ratings and there is a grade.
                    // If modgrade_type is empty then use currentgradetype.
                    $gradetype = isset($val['modgrade_type']) ? $val['modgrade_type'] : $this->currentgradetype;
                    if ($gradetype === 'point') {
                        if (isset($val['modgrade_point']) &&
                            grade_floats_different($this->currentgrade, $val['modgrade_point'])) {
                            return false;
                        }
                    }
                    return true;
                };
                $checkmaxgrade = function($val) {
                    // Closure to validate a max points value. See the note above about scope if this confuses you.
                    // If modgrade_type is empty then use currentgradetype.
                    $gradetype = isset($val['modgrade_type']) ? $val['modgrade_type'] : $this->currentgradetype;
                    if ($gradetype === 'point') {
                        if (isset($val['modgrade_point'])) {
                            return $this->validate_point($val['modgrade_point']);
                        }
                    }
                    return true;
                };
                $checkvalidscale = function($val) {
                    // Closure to validate a scale value. See the note above about scope if this confuses you.
                    // If modgrade_type is empty then use currentgradetype.
                    $gradetype = isset($val['modgrade_type']) ? $val['modgrade_type'] : $this->currentgradetype;
                    if ($gradetype === 'scale') {
                        if (isset($val['modgrade_scale'])) {
                            return $this->validate_scale($val['modgrade_scale']);
                        }
                    }
                    return true;
                };

                $checkrescale = function($val) {
                    // Nothing is affected by changes to grademax if there are no grades yet.
                    if (!$this->isupdate || !$this->hasgrades || !$this->canrescale) {
                        return true;
                    }
                    // Closure to validate a scale value. See the note above about scope if this confuses you.
                    // If modgrade_type is empty then use currentgradetype.
                    $gradetype = isset($val['modgrade_type']) ? $val['modgrade_type'] : $this->currentgradetype;
                    if ($gradetype === 'point' && isset($val['modgrade_point'])) {
                        // Work out if the value was actually changed in the form.
                        if (grade_floats_different($this->currentgrade, $val['modgrade_point'])) {
                            if (empty($val['modgrade_rescalegrades'])) {
                                // This was an "edit", the grademax was changed and the process existing setting was not set.
                                return false;
                            }
                        }
                    }
                    return true;
                };

                $cantchangegradetype = get_string('modgradecantchangegradetype', 'grades');
                $cantchangemaxgrade = get_string('modgradecantchangeratingmaxgrade', 'grades');
                $maxgradeexceeded = get_string('modgradeerrorbadpoint', 'grades', get_config('core', 'gradepointmax'));
                $invalidscale = get_string('modgradeerrorbadscale', 'grades');
                $cantchangescale = get_string('modgradecantchangescale', 'grades');
                $mustchooserescale = get_string('mustchooserescaleyesorno', 'grades');
                // When creating the rules the sixth arg is $force, we set it to true because otherwise the form
                // will attempt to validate the existence of the element, we don't want this because the element
                // is being created right now and doesn't actually exist as a registered element yet.
                $caller->addRule($name, $cantchangegradetype, 'callback', $checkgradetypechange, 'server', false, true);
                $caller->addRule($name, $cantchangemaxgrade, 'callback', $checkmaxgradechange, 'server', false, true);
                $caller->addRule($name, $maxgradeexceeded, 'callback', $checkmaxgrade, 'server', false, true);
                $caller->addRule($name, $invalidscale, 'callback', $checkvalidscale, 'server', false, true);
                $caller->addRule($name, $cantchangescale, 'callback', $checkscalechange, 'server', false, true);
                $caller->addRule($name, $mustchooserescale, 'callback', $checkrescale, 'server', false, true);

                break;

            case 'updateValue':
                // As this is a group element with no value of its own we are only interested in situations where the
                // default value or a constant value are being provided to the actual element.
                // In this case we expect an int that is going to translate to a scale if negative, or to max points
                // if positive.

                // Set the maximum points field to disabled if the rescale option has not been chosen and there are grades.
                $caller->disabledIf($this->getName() . '[modgrade_point]', $this->getName() .
                        '[modgrade_rescalegrades]', 'eq', '');

                // A constant value should be given as an int.
                // The default value should be an int and be either $CFG->gradepointdefault or whatever was set in set_data().
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    if ($caller->isSubmitted() && $this->_findValue($caller->_submitValues) !== null) {
                        // Submitted values are array, one value for each individual element in this group.
                        // When there is submitted data let parent::onQuickFormEvent() process it.
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
                            $this->gradetypeformelement->setValue('scale');
                            $this->scaleformelement->setValue(($value * -1));
                        } else if ($value > 0) {
                            $this->gradetypeformelement->setValue('point');
                            $maxvalue = !empty($this->currentgrade) ? (string)unformat_float($this->currentgrade) : $value;
                            $this->maxgradeformelement->setValue($maxvalue);
                        }
                    } else {
                        $this->gradetypeformelement->setValue('none');
                        $this->maxgradeformelement->setValue('');
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
