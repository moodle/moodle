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
 * Helper for privacy tests.
 *
 * @package    core_question
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\writer;

/**
 * Helper for privacy tests.
 *
 * @package    core_question
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait core_question_privacy_helper {
    /**
     * Assert that the question usage in the supplied slot matches the expected format
     * and usage for a question.
     *
     * @param   \question_usage_by_activity $quba The Question Usage to test against.
     * @param   int                         $slotno The slot number to compare
     * @param   \question_display_options   $options    The display options used for formatting.
     * @param   \stdClass                   $data The data to check.
     */
    public function assert_question_slot_equals(
            \question_usage_by_activity $quba,
            $slotno,
            \question_display_options $options,
            $data
        ) {
        $attempt = $quba->get_question_attempt($slotno);
        $question = $attempt->get_question();

        // Check the question data exported.
        $this->assertEquals($data->name, $question->name);
        $this->assertEquals($data->question, $question->questiontext);

        // Check the answer exported.
        $this->assertEquals($attempt->get_response_summary(), $data->answer);

        if ($options->marks != \question_display_options::HIDDEN) {
            $this->assertEquals($attempt->get_mark(), $data->mark);
        } else {
            $this->assertFalse(isset($data->mark));
        }

        if ($options->flags != \question_display_options::HIDDEN) {
            $this->assertEquals($attempt->is_flagged(), (int) $data->flagged);
        } else {
            $this->assertFalse(isset($data->flagged));
        }

        if ($options->generalfeedback != \question_display_options::HIDDEN) {
            $this->assertEquals($question->format_generalfeedback($attempt), $data->generalfeedback);
        } else {
            $this->assertFalse(isset($data->generalfeedback));
        }
    }

    /**
     * Assert that a question attempt was exported.
     *
     * @param   \context    $context The context which the attempt should be in
     * @param   array       $subcontext The base of the export
     * @param   question_usage_by_activity  $quba The question usage expected
     * @param   \question_display_options   $options    The display options used for formatting.
     * @param   \stdClass   $user The user exported
     */
    public function assert_question_attempt_exported(\context $context, array $subcontext, $quba, $options, $user) {
        $usagecontext = array_merge(
            $subcontext,
            [get_string('questions', 'core_question')]
        );

        $writer = writer::with_context($context);

        foreach ($quba->get_slots() as $slotno) {
            $data = $writer->get_data(array_merge($usagecontext, [$slotno]));
            $this->assert_question_slot_equals($quba, $slotno, $options, $data);
        }
    }
}
