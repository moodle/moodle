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
 * Course competency rule element.
 *
 * @package   tool_lp
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use tool_lp\api;
use tool_lp\external\competency_exporter;
use tool_lp\course_module_competency;

require_once($CFG->libdir . '/form/select.php');

/**
 * Course competency rule element.
 *
 * @package   tool_lp
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_course_competency_rule_form_element extends MoodleQuickForm_select {

    /**
     * Constructor
     *
     * @TODO: Convert this to a real constructor when the PHP7 changes are merged.
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    function tool_lp_course_competency_rule_form_element($elementName=null, $elementLabel=null, $options=array(), $attributes=null) {
        global $OUTPUT;

        if ($elementName == null) {
            // This is broken quickforms messing with the constructors.
            return;
        }

        if (!empty($options['cmid'])) {
            $cmid = $options['cmid'];

            $current = \tool_lp\api::list_course_module_competencies_in_course_module($cmid);
            if (!empty($current)) {
                $one = array_pop($current);
                $this->setValue($one->get_ruleoutcome());
            }
        }
        $validoptions = course_module_competency::get_ruleoutcome_list();
        parent::__construct($elementName, $elementLabel, $validoptions, $attributes);
    }
}
