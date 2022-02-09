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
 * Helper functions and callbacks.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Question status fragment callback.
 *
 * @param array $args
 * @return string rendered output
 */
function qbank_editquestion_output_fragment_question_status($args): string {
    global $CFG;
    require_once($CFG->dirroot . '/question/engine/bank.php');
    $question = question_bank::load_question($args['questionid']);
    $mform = new \qbank_editquestion\form\question_status_form();
    $data = ['status' => $question->status];
    $mform->set_data($data);

    return $mform->render();
}
