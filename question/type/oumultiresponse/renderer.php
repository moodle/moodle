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
 * OU multi response question renderer class.
 *
 * @package   qtype_oumultiresponse
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/multichoice/renderer.php');
require_once($CFG->dirroot . '/question/type/oumultiresponse/lib.php');

class qtype_oumultiresponse_renderer extends qtype_multichoice_multi_renderer {

    /**
     * Generate a brief statement of how many sub-parts of this question the
     * student got right.
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    public function num_parts_correct(question_attempt $qa) {
        if ($qa->get_question()->get_num_selected_choices($qa->get_last_qt_data()) >
            $qa->get_question()->get_num_correct_choices()) {
            return get_string('toomanyselected', 'qtype_multichoice');
        }
        $a = new stdClass();
        list($a->num, $a->outof) = $qa->get_question()->get_num_parts_right($qa->get_last_qt_data());
        if (is_null($a->outof)) {
            return '';
        }
        if ($a->num == 1) {
            return get_string('yougot1right', 'qtype_oumultiresponse');
        }
        $f = new NumberFormatter(current_language(), NumberFormatter::SPELLOUT);
        $a->num = $f->format($a->num);
        return get_string('yougotnright', 'qtype_oumultiresponse', $a);
    }
}
