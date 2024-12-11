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

namespace qtype_description;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * This file contains tests that walks a description question through its interaction model.
 *
 * @package    qtype_description
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    public function test_informationitem_feedback_description(): void {

        // Create a description question.
        $description = \test_question_maker::make_question('description');
        $this->start_attempt_at_question($description, 'deferredfeedback');

        // Check the initial state.
        $this->assertEquals('informationitem',
                $this->quba->get_question_attempt($this->slot)->get_behaviour_name());

        $this->check_current_output(
                new \question_contains_tag_with_contents('h4', get_string('informationtext', 'qtype_description'))
        );

        // Further tests of the description qtype are in
        // question/behaviour/informationitem/tests/testwalkthrough.php.
    }
}
