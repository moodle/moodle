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
 * Quiz events tests.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/previewlib.php');

/**
 * Unit tests for question preview.
 *
 * @package    question
 * @category   phpunit
 * @copyright  2016 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_previewlib_testcase extends advanced_testcase {

    /**
     * Setup some convenience test data with a single attempt.
     *
     * @return question_usage_by_activity
     */
    protected function prepare_question_data() {
        $this->resetAfterTest(true);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Create a questions and start the preview.
        $cat = $questiongenerator->create_question_category();

        $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $questiondata = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        $question = question_bank::load_question($questiondata->id);
        $quba->add_question($question);
        $quba->start_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        return $quba;
    }

    /**
     * Test the attempt deleted event.
     */
    public function test_question_preview_cron() {
        global $DB;

        // Create some quiz data.
        // This will create two questions.
        $quba1 = $this->prepare_question_data();

        // Run the cron.
        ob_start();
        question_preview_cron();
        $output = ob_get_clean();
        $this->assertEquals("\n  Cleaning up old question previews...done.\n", $output);

        // The attempt should not have been removed.
        // There should be one question usage with two question attempts.
        $this->assertEquals(1, $DB->count_records('question_usages', array('id' => $quba1->get_id())));
        $this->assertEquals(1, $DB->count_records('question_attempts', array('questionusageid' => $quba1->get_id())));
        $this->assertEquals(1, $DB->count_records('question_attempt_steps'));
        $this->assertEquals(1, $DB->count_records('question_attempt_step_data'));

        // Update the timemodified and timecreated to be in the past.
        $DB->set_field('question_attempts', 'timemodified', time() - WEEKSECS);
        $DB->set_field('question_attempt_steps', 'timecreated', time() - WEEKSECS);

        // Create some quiz data.
        // This will create two questions.
        $quba2 = $this->prepare_question_data();

        // There will now be 2 usages, etc.
        $this->assertEquals(2, $DB->count_records('question_usages'));
        $this->assertEquals(2, $DB->count_records('question_attempts'));
        $this->assertEquals(2, $DB->count_records('question_attempt_steps'));
        $this->assertEquals(2, $DB->count_records('question_attempt_step_data'));

        // Run the cron again.
        // $quba1 will be removed, but $quba2 should still be present.
        ob_start();
        question_preview_cron();
        $output = ob_get_clean();
        $this->assertEquals("\n  Cleaning up old question previews...done.\n", $output);

        $this->assertEquals(0, $DB->count_records('question_usages', array('id' => $quba1->get_id())));
        $this->assertEquals(0, $DB->count_records('question_attempts', array('questionusageid' => $quba1->get_id())));
        $this->assertEquals(1, $DB->count_records('question_usages', array('id' => $quba2->get_id())));
        $this->assertEquals(1, $DB->count_records('question_attempts', array('questionusageid' => $quba2->get_id())));
        $this->assertEquals(1, $DB->count_records('question_attempt_steps'));
        $this->assertEquals(1, $DB->count_records('question_attempt_step_data'));
    }
}
