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

namespace core_question;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Test for question_definition base classes.
 *
 * @package   core_question
 * @copyright  2015 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \question_definition
 */
final class question_definition_test extends \advanced_testcase {
    public function test_make_html_inline(): void {
        // Base class is abstract, so we need to pick one qusetion type to test this method.
        $mc = \test_question_maker::make_a_multichoice_single_question();
        $this->assertEquals('Frog', $mc->make_html_inline('<p>Frog</p>'));
        $this->assertEquals('Frog', $mc->make_html_inline('<p>Frog<br /></p>'));
        $this->assertEquals('Frog<br />Toad', $mc->make_html_inline("<p>Frog</p>\n<p>Toad</p>"));
        $this->assertEquals('<img src="http://example.com/pic.png" alt="Graph" />',
                $mc->make_html_inline(
                    '<p><img src="http://example.com/pic.png" alt="Graph" /></p>'));
        $this->assertEquals("Frog<br />XXX <img src='http://example.com/pic.png' alt='Graph' />",
                $mc->make_html_inline(" <p> Frog </p> \n\r
                    <p> XXX <img src='http://example.com/pic.png' alt='Graph' /> </p> "));
        $this->assertEquals('Frog', $mc->make_html_inline('<p>Frog</p><p></p>'));
        $this->assertEquals('Frog<br />†', $mc->make_html_inline('<p>Frog</p><p>†</p>'));
    }

    public function test_check_file_access_hints(): void {
        // Prepare a shortanswer question with a hint plus default display options.
        $question = \test_question_maker::make_question('shortanswer', 'frogtoad');
        $question->id = 42;
        $question->hints[] = new \question_hint_with_parts(12, 'foo', FORMAT_HTML, false, false);
        $options = new \question_display_options();

        // Prepare and start an interactive question attempt.
        $quba = new \question_usage_by_activity('qtype_shortanswer', \context_system::instance());
        $qa = new \question_attempt($question, $quba->get_id());
        $qa->start('interactive', 1);

        // No answer has been submitted, so we should not have access to files from the 'hint' area.
        $args = [$question->hints[0]->id, 'foo.jpg'];
        $checkresult = $question->check_file_access($qa, $options, 'question', 'hint', $args, false);
        $this->assertFalse($checkresult);
    }
}
