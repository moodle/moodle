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
 * Renderer for outputting parts of a question belonging to the legacy adaptive (no penalties) behaviour.
 *
 * @package    qbehaviour_regexpadaptivewithhelpnopenalty
 * @subpackage adaptivenopenalty
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../regexpadaptivewithhelp/renderer.php');

/**
 * Renderer for outputting parts of a question belonging to the legacy adaptive (no penalties) behaviour.
 *
 * @copyright  2011 Joseph Rezeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_regexpadaptivewithhelpnopenalty_renderer extends qbehaviour_regexpadaptivewithhelp_renderer {
    /**
     * description
     * @param qbehaviour_adaptive_mark_details $details the details.
     * @param question_display_options $options the options
     */
    protected function grading_details(qbehaviour_adaptive_mark_details $details,
                    question_display_options $options) {
        $mark = $details->get_formatted_marks($options->markdp);
        return get_string('gradingdetails', 'qbehaviour_adaptive', $mark);
    }

    /**
     * description
     */
    protected function disregarded_info() {
        return '';
    }
    /**
     * Moved help strings to this function to get appropriate strings for the nopenalty behaviour.
     */
    public function help_msg() {
        $helptexts = [];
        $helptexts[1] = get_string('getletter', 'qbehaviour_regexpadaptivewithhelpnopenalty');
        $helptexts[2] = get_string('getword', 'qbehaviour_regexpadaptivewithhelpnopenalty');
        $helptexts[3] = get_string('getwordorpunctuation', 'qbehaviour_regexpadaptivewithhelpnopenalty');
        return $helptexts;
    }

}
