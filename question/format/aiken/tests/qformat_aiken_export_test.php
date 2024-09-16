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
 * Unit tests for export/import description (info) for question category in the Moodle XML format.
 *
 * @package    qformat_aiken
 * @copyright  2018 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_question\local\bank\question_version_status;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/aiken/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/editlib.php');

/**
 * Unit tests for the Aiken question format export.
 *
 * @copyright  2018 Jean-Michel vedrine)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qformat_aiken
 */
class qformat_aiken_export_test extends advanced_testcase {
    /**
     * Assert that 2 strings are the same, ignoring ends of line.
     * We need to override this function because we don't want any output
     * @param   string    $expectedtext The expected string.
     * @param   string    $text The actual string.
     */
    public function assert_same_aiken($expectedtext, $text) {
        $this->assertEquals(
            phpunit_util::normalise_line_endings($expectedtext),
            phpunit_util::normalise_line_endings($text)
        );
    }

    public function test_export_questions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        // Create a new course category and and a new course in that.
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(array('category' => $category->id));
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = context_coursecat::instance($category->id);
        $cat = $generator->create_question_category(array('contextid' => $context->id));
        $question1 = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));
        $question2 = $generator->create_question('essay', null,
                array('category' => $cat->id));
        $question3 = $generator->create_question('numerical', null,
                array('category' => $cat->id));
        $question4  = $generator->create_question('multichoice', 'one_of_four',
                array('category' => $cat->id));
        $question4  = $generator->create_question('multichoice', 'two_of_four',
                array('category' => $cat->id));
        $exporter = new qformat_aiken();
        $exporter->category = $cat;
        $exporter->setCourse($course);
        $expectedoutput = <<<EOT
Which is the oddest number?
A) One
B) Two
C) Three
D) Four
ANSWER: A

EOT;
        $this->assert_same_aiken($expectedoutput, $exporter->exportprocess());
    }

    public function test_export_multiline_question(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        // Create a new course category and and a new course in that.
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(array('category' => $category->id));
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = context_coursecat::instance($category->id);
        $cat = $generator->create_question_category(array('contextid' => $context->id));
        $question  = $generator->create_question('multichoice', 'one_of_four',
                array('category' => $cat->id));
        $question->questiontext = <<<EOT
<p>Which is the</p>
<p>oddest number?</p>
EOT;
        $exporter = new qformat_aiken();
        $exporter->category = $cat;
        $exporter->setCourse($course);
        $expectedoutput = <<<EOT
Which is the oddest number?
A) One
B) Two
C) Three
D) Four
ANSWER: A

EOT;
        $this->assert_same_aiken($expectedoutput, $exporter->exportprocess());
    }

    public function test_hidden_question_not_exported(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a new course category and a new course in that.
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $context = context_coursecat::instance($category->id);
        $cat = $generator->create_question_category(['contextid' => $context->id]);

        // Create a visible and a hidden question.
        $generator->create_question('multichoice', 'one_of_four', [
            'category' => $cat->id,
            'questiontext' => ['text' => 'This question is the visible one.', 'format' => FORMAT_HTML],
        ]);
        $generator->create_question('multichoice', 'one_of_four', [
            'category' => $cat->id,
            'questiontext' => ['text' => 'This question is the hidden one.', 'format' => FORMAT_HTML],
            'status' => question_version_status::QUESTION_STATUS_HIDDEN,
        ]);

        // Prepared the expected result.
        $expectedoutput = <<<EOT
This question is the visible one.
A) One
B) Two
C) Three
D) Four
ANSWER: A

EOT;

        // Do the export and verify.
        $exporter = new qformat_aiken();
        $exporter->category = $cat;
        $exporter->setCourse($course);
        $this->assert_same_aiken($expectedoutput, $exporter->exportprocess());
    }
}
