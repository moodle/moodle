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
 * Question competency rule element.
 *
 * @package     tool_lp
 * @author      2019 Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @author      2016 Damyon Wiese (was based on original code from Damyon Wiese)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use core_competency\api;
use core_competency\external\competency_exporter;
use core_competency\question_competency;

require_once($CFG->libdir . '/form/select.php');

/**
 * Question competency rule element.
 *
 * @package     tool_lp
 * @author      2019 Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @author      2016 Damyon Wiese (was based on original code from Damyon Wiese)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_question_competency_rule_form_element extends MoodleQuickForm_select {

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementName=null, $elementLabel=null, $options=array(), $attributes=null) {
        if ($elementName == null) {
            // This is broken quickforms messing with the constructors.
            return;
        }

        if (!empty($options['qid'])) {
            $qid = $options['qid'];

            $current = \core_competency\api::list_question_competencies_in_question($qid);

            // Note: We just pick the outcome set on the first question_competency - because in our UI are are
            // forcing them to be all the same for each activity.
            if (!empty($current)) {
                $one = array_pop($current);
                $this->setValue($one->get('ruleoutcome'));
            }
        }
        $validoptions = question_competency::get_ruleoutcome_list();
        parent::__construct($elementName, $elementLabel, $validoptions, $attributes);
    }
}
