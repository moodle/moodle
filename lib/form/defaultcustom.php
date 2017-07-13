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
 * Creates an element with a dropdown Default/Custom and an input for the value (text or date_selector)
 *
 * @package   core_form
 * @copyright 2017 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/group.php');
require_once($CFG->libdir . '/formslib.php');

/**
 * Creates an element with a dropdown Default/Custom and an input for the value (text or date_selector)
 *
 * @package   core_form
 * @copyright 2017 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_defaultcustom extends MoodleQuickForm_group {

    /**
     * @var array These complement separators, they are appended to the resultant HTML.
     */
    protected $_wrap = array('', '');

    /**
     * @var null|bool Keeps track of whether the date selector was initialised using createElement
     *                or addElement. If true, createElement was used signifying the element has been
     *                added to a group - see MDL-39187.
     */
    protected $_usedcreateelement = true;

    /** @var array */
    protected $_options;

    /**
     * Constructor
     *
     * @param string $elementname Element's name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementname = null, $elementlabel = null, $options = array(), $attributes = null) {
        parent::__construct($elementname, $elementlabel);
        $this->setAttributes($attributes);

        $this->_appendName = true;
        $this->_type = 'defaultcustom';

        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        $this->_options = [
            'type' => 'text', // Type of the element. Supported are 'text' and 'date_selector'.
            'defaultvalue' => null, // Value to be used when not overridden.
            'customvalue' => null, // Value to be used when overwriting.
            'customlabel' => get_string('custom', 'form'), // Label for 'customize' checkbox
            // Other options are the same as the ones that can be passed to 'date_selector' element.
            'timezone' => 99,
            'startyear' => $calendartype->get_min_year(),
            'stopyear' => $calendartype->get_max_year(),
            'defaulttime' => 0,
            'step' => 5,
            'optional' => false,
        ];

        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (array_key_exists($name, $this->_options)) {
                    if ($name === 'type' && !in_array($value, ['text', 'date_selector'])) {
                        throw new coding_exception('Only text and date_selector elements are supported in ' . $this->_type);
                    }
                    if ($name === 'optional' && $value) {
                        throw new coding_exception('Date selector can not be optional in ' . $this->_type);
                    }
                    $this->_options[$name] = $value;
                }
            }
        }
    }

    /**
     * Converts timestamp to the day/month/year array in the current calendar format
     * @param int $value
     * @return array
     */
    protected function timestamp_to_date_array($value) {
        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        $currentdate = $calendartype->timestamp_to_date_array($value, $this->_options['timezone']);
        return array(
            'day' => $currentdate['mday'],
            'month' => $currentdate['mon'],
            'year' => $currentdate['year']);
    }

    /**
     * Should this element have default/custom switch?
     *
     * @return bool
     */
    protected function has_customize_switch() {
        return $this->_options['defaultvalue'] !== null;
    }

    /**
     * This will create all elements in the group
     */
    public function _createElements() {
        if (!$this->has_customize_switch()) {
            $element = $this->createFormElement('hidden', 'customize', 1);
        } else {
            $element = $this->createFormElement('advcheckbox', 'customize', '', $this->_options['customlabel']);
        }
        $this->_elements[] = $element;

        if ($this->_options['type'] === 'text') {
            $element = $this->createFormElement($this->_options['type'], 'value',
                get_string('newvaluefor', 'form', $this->getLabel()), $this->getAttributes());
            $element->setHiddenLabel(true);
        } else if ($this->_options['type'] === 'date_selector') {
            $element = $this->createFormElement($this->_options['type'], 'value', '', $this->_options,
                $this->getAttributes());
        }
        $this->_elements[] = $element;
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        $this->setMoodleForm($caller);
        switch ($event) {
            case 'updateValue':
                // Constant values override both default and submitted ones
                // default values are overriden by submitted.
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    // If no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case.
                    if ($caller->isSubmitted()) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (!is_array($value)) {
                    $customize = ($value !== false || !$this->has_customize_switch());
                    if ($this->_options['type'] === 'text') {
                        $elementvalue = $customize ? $value : $this->_options['defaultvalue'];
                    } else {
                        $elementvalue = $this->timestamp_to_date_array($customize ? $value : $this->_options['defaultvalue']);
                    }
                    $value = [
                        'customize' => $customize,
                        'value' => $elementvalue
                    ];
                }
                $this->setValue($value);
                break;
            case 'createElement':
                $rv = parent::onQuickFormEvent($event, $arg, $caller);
                if ($this->has_customize_switch()) {
                    if ($this->_options['type'] === 'text') {
                        $caller->disabledIf($arg[0] . '[value]', $arg[0] . '[customize]', 'notchecked');
                    } else {
                        $caller->disabledIf($arg[0] . '[value][day]', $arg[0] . '[customize]', 'notchecked');
                        $caller->disabledIf($arg[0] . '[value][month]', $arg[0] . '[customize]', 'notchecked');
                        $caller->disabledIf($arg[0] . '[value][year]', $arg[0] . '[customize]', 'notchecked');
                    }
                }
                return $rv;
            case 'addElement':
                $this->_usedcreateelement = false;
                return parent::onQuickFormEvent($event, $arg, $caller);
                break;
            default:
                return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    public function freeze() {
        parent::freeze();
        $this->setPersistantFreeze(true);
    }

    public function toHtml() {
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer = new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);

        $html = $this->_wrap[0];
        if ($this->_usedcreateelement) {
            $html .= html_writer::tag('span', $renderer->toHtml(), array('class' => 'fdefaultcustom'));
        } else {
            $html .= $renderer->toHtml();
        }
        $html .= $this->_wrap[1];

        return $html;
    }

    public function accept(&$renderer, $required = false, $error = null) {
        global $PAGE;

        if (!$this->_flagFrozen && $this->has_customize_switch()) {
            // Add JS to the default/custom switch.
            $firstelement = reset($this->_elements);
            $defaultvalue = $this->_options['defaultvalue'];
            $customvalue = $this->_options['customvalue'];
            if ($this->_options['type'] === 'date_selector') {
                $defaultvalue = $this->timestamp_to_date_array($defaultvalue);
                $customvalue = $this->timestamp_to_date_array($customvalue);
            }
            $firstelement->updateAttributes(['data-defaultcustom' => 'true',
                'data-type' => $this->_options['type'],
                'data-defaultvalue' => json_encode($defaultvalue),
                'data-customvalue' => json_encode($customvalue)]);
            $PAGE->requires->js_amd_inline("require(['core_form/defaultcustom'], function() {});");
        }

        $renderer->renderElement($this, $required, $error);
    }

    /**
     * Output a value. Give it the name of the group. In case of "default" return false.
     *
     * @param array $submitvalues values submitted.
     * @param bool $assoc specifies if returned array is associative
     * @return array
     */
    public function exportValue(&$submitvalues, $assoc = false) {
        $valuearray = array();
        foreach ($this->_elements as $element) {
            $thisexport = $element->exportValue($submitvalues[$this->getName()], true);
            if ($thisexport != null) {
                $valuearray += $thisexport;
            }
        }
        if (empty($valuearray['customize'])) {
            return $this->_prepareValue(false, $assoc);
        }
        return array_key_exists('value', $valuearray) ? $this->_prepareValue($valuearray['value'], $assoc) : [];
    }
}
