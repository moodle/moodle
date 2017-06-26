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
 * Drop down form element to select visibility in an activity mod update form
 *
 * Contains HTML class for a drop down element to select visibility in an activity mod update form
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once "$CFG->libdir/form/select.php";

/**
 * Drop down form element to select visibility in an activity mod update form
 *
 * HTML class for a drop down element to select visibility in an activity mod update form
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_modvisible extends MoodleQuickForm_select{

    /** @var int activity state: visible=0, visibleoncoursepage=any */
    const HIDE = 0;

    /** @var int activity state: visible=1, visibleoncoursepage=1 */
    const SHOW = 1;

    /** @var int activity state: visible=1, visibleoncoursepage=0 */
    const STEALTH = -1;

    /**
     * Class constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     * @param array $options ignored
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        parent::__construct($elementName, $elementLabel, null, $attributes);
        $this->_type = 'modvisible';
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_modvisible($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes, $options);
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
        switch ($event) {
            case 'createElement':
                $options = is_array($arg[3]) ? $arg[3] : [];
                $sectionvisible = array_key_exists('sectionvisible', $options) ? $options['sectionvisible'] : 1;
                $cm = !empty($options['cm']) ? cm_info::create($options['cm']) : null;
                $choices = array();
                if (!$sectionvisible) {
                    // If section is not visible the activity is hidden by default but it can also be made available.
                    $choices[self::HIDE] = get_string('hidefromstudents');
                    if (!$cm || $cm->has_view()) {
                        $choices[self::SHOW] = get_string('hideoncoursepage');
                    }
                } else {
                    $choices[self::SHOW] = get_string('showoncoursepage');
                    $choices[self::HIDE] = get_string('hidefromstudents');
                    if (!empty($options['allowstealth']) && (!$cm || $cm->has_view())) {
                        // If allowed in this course/section, add a third visibility option
                        // "Available but not displayed on course page".
                        $choices[self::STEALTH] = get_string('hideoncoursepage');
                    }
                }
                $this->load($choices);
                break;
            case 'updateValue':
                // Given two bool values of 'visible' and 'visibleoncoursepage' convert to a single
                // three-state value (show, hide, hide-on-course-page).
                $name = $this->getName();
                $value = $this->_findValue($caller->_constantValues);
                if (!empty($value) && isset($caller->_constantValues[$name.'oncoursepage']) &&
                        !$caller->_constantValues[$name.'oncoursepage']) {
                    $value = self::STEALTH;
                }
                if (null === $value) {
                    if ($caller->isSubmitted()) {
                        break;
                    }
                    $value = $this->_findValue($caller->_defaultValues);
                    if (!empty($value) && isset($caller->_defaultValues[$name.'oncoursepage']) &&
                            !$caller->_defaultValues[$name.'oncoursepage']) {
                        $value = self::STEALTH;
                    }
                }
                if ($value !== null) {
                    $this->setSelected($value);
                }
                return true;

        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * As usual, to get the group's value we access its elements and call
     * their exportValue() methods
     *
     * @param array $submitvalues submitted values
     * @param bool $assoc if true the retured value is associated array
     * @return mixed
     */
    public function exportValue(&$submitvalues, $assoc = false) {
        if ($assoc) {
            $value = parent::exportValue($submitvalues, $assoc);
            $key = key($value);
            $v = $value[$key];
            // Convert three-state dropdown value (show, hide, hide-on-course-page) into the array of two bool values:
            // array('visible' => x, 'visibleoncoursepage' => y).
            return array($key => ($v == self::HIDE ? 0 : 1),
                $key . 'oncoursepage' => ($v == self::STEALTH ? 0 : 1));
        } else {
            return parent::exportValue($submitvalues, $assoc);
        }
    }
}
