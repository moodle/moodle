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
class MoodleQuickForm_modgrade extends MoodleQuickForm_select{

    /** @var bool if true the hides grade */
    var $_hidenograde = false;

    /**
     * Class constructor
     *
     * @param string $elementName (optional) name attribute
     * @param mixed $elementLabel (optional) Label for the drop down
     * @param mixed $attributes (optional) Either a typical HTML attribute string or an associative array
     * @param mixed $hidenograde (optional) hide grade
     */
    function MoodleQuickForm_modgrade($elementName=null, $elementLabel=null, $attributes=null, $hidenograde=false)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes, null);
        $this->_type = 'modgrade';
        $this->_hidenograde = $hidenograde;

    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return mixed
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        global $COURSE, $CFG, $OUTPUT;
        switch ($event) {
            case 'createElement':
                // Need to call superclass first because we want the constructor
                // to run.
                $result = parent::onQuickFormEvent($event, $arg, $caller);
                $strscale = get_string('scale');
                $strscales = get_string('scales');
                $scales = get_scales_menu($COURSE->id);
                foreach ($scales as $i => $scalename) {
                    $grades[-$i] = $strscale .': '. $scalename;
                }
                if (!$this->_hidenograde) {
                    $grades[0] = get_string('nograde');
                }
                for ($i=100; $i>=1; $i--) {
                    $grades[$i] = $i;
                }
                $this->load($grades);
                //TODO: rewrite mod grading support in modforms
                return $result;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

}
