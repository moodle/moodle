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
 * This file contains tests that walks essay questions through some attempts.
 *
 * @package   qtype_essay
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the essay question type.
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_walkthrough_testcase extends qbehaviour_walkthrough_test_base {

    protected function check_contains_textarea($name, $content = '', $height = 10) {
        $fieldname = $this->quba->get_field_prefix($this->slot) . $name;

        $this->assertTag(array('tag' => 'textarea',
                'attributes' => array('cols' => '60', 'rows' => $height,
                        'name' => $fieldname)),
                $this->currentoutput);

        if ($content) {
            $this->assertRegExp('/' . preg_quote(s($content), '/') . '/', $this->currentoutput);
        }
    }

    /**
     * Helper method: Store a test file with a given name and contents in a
     * draft file area.
     *
     * @param int $usercontextid user context id.
     * @param int $draftitemid draft item id.
     * @param string $filename filename.
     * @param string $contents file contents.
     */
    protected function save_file_to_draft_area($usercontextid, $draftitemid, $filename, $contents) {
        $fs = get_file_storage();

        $filerecord = new stdClass();
        $filerecord->contextid = $usercontextid;
        $filerecord->component = 'user';
        $filerecord->filearea = 'draft';
        $filerecord->itemid = $draftitemid;
        $filerecord->filepath = '/';
        $filerecord->filename = $filename;
        $fs->create_file_from_string($filerecord, $contents);
    }

    public function test_deferred_feedback_html_editor() {

        // Create an essay question.
        $q = test_question_maker::make_question('essay', 'editor');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $prefix = $this->quba->get_field_prefix($this->slot);
        $fieldname = $prefix . 'answer';
        $response = '<p>The <b>cat</b> sat on the mat. Then it ate a <b>frog</b>.</p>';

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_contains_textarea('answer', '');
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save a response.
        $this->quba->process_all_actions(null, array(
            'slots'                    => $this->slot,
            $fieldname                 => $response,
            $fieldname . 'format'      => FORMAT_HTML,
            $prefix . ':sequencecheck' => '1',
        ));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->render();
        $this->check_contains_textarea('answer', $response);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(2);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->render();
        $this->assertRegExp('/' . preg_quote($response, '/') . '/', $this->currentoutput);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_contains_general_feedback_expectation($q));
    }

    public function test_deferred_feedback_plain_text() {

        // Create an essay question.
        $q = test_question_maker::make_question('essay', 'plain');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $prefix = $this->quba->get_field_prefix($this->slot);
        $fieldname = $prefix . 'answer';
        $response = "x < 1\nx > 0\nFrog & Toad were friends.";

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_contains_textarea('answer', '');
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save a response.
        $this->quba->process_all_actions(null, array(
            'slots'                    => $this->slot,
            $fieldname                 => $response,
            $fieldname . 'format'      => FORMAT_HTML,
            $prefix . ':sequencecheck' => '1',
        ));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->render();
        $this->check_contains_textarea('answer', $response);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(2);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->render();
        $this->assertRegExp('/' . preg_quote(s($response), '/') . '/', $this->currentoutput);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_contains_general_feedback_expectation($q));
    }

    public function test_responsetemplate() {

        // Create an essay question.
        $q = test_question_maker::make_question('essay', 'responsetemplate');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $prefix = $this->quba->get_field_prefix($this->slot);
        $fieldname = $prefix . 'answer';

        // Check the initial state.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->render();
        $this->check_contains_textarea('answer', 'Once upon a time');
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(1);

        // Save.
        $this->quba->process_all_actions(null, array(
            'slots'                    => $this->slot,
            $fieldname                 => 'Once upon a time there was a little green frog.',
            $fieldname . 'format'      => FORMAT_HTML,
            $prefix . ':sequencecheck' => '1',
        ));

        // Verify.
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->render();
        $this->check_contains_textarea('answer', 'Once upon a time there was a little green frog.');
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_does_not_contain_feedback_expectation());
        $this->check_step_count(2);

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Verify.
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->render();
        $this->assertRegExp('/' . preg_quote(s('Once upon a time there was a little green frog.'), '/') . '/', $this->currentoutput);
        $this->check_current_output(
                $this->get_contains_question_text_expectation($q),
                $this->get_contains_general_feedback_expectation($q));
    }

    public function test_deferred_feedback_html_editor_with_files_attempt_on_last() {
        global $CFG, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $usercontextid = context_user::instance($USER->id)->id;
        $fs = get_file_storage();

        // Create an essay question in the DB.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('essay', 'editorfilepicker', array('category' => $cat->id));

        // Start attempt at the question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        // First we need to get the draft item ids.
        $this->render();
        if (!preg_match('/env=editor&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('Editor draft item id not found.');
        }
        $editordraftid = $matches[1];
        if (!preg_match('/env=filemanager&amp;action=browse&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('File manager draft item id not found.');
        }
        $attachementsdraftid = $matches[1];

        $this->save_file_to_draft_area($usercontextid, $editordraftid, 'smile.txt', ':-)');
        $this->save_file_to_draft_area($usercontextid, $attachementsdraftid, 'greeting.txt', 'Hello world!');
        $this->process_submission(array(
                'answer' => 'Here is a picture: <img src="' . $CFG->wwwroot .
                                "/draftfile.php/{$usercontextid}/user/draft/{$editordraftid}/smile.txt" .
                                '" alt="smile">.',
                'answerformat' => FORMAT_HTML,
                'answer:itemid' => $editordraftid,
                'attachments' => $attachementsdraftid));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Save the same response again, and verify no new step is created.
        $this->load_quba();

        $this->render();
        if (!preg_match('/env=editor&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('Editor draft item id not found.');
        }
        $editordraftid = $matches[1];
        if (!preg_match('/env=filemanager&amp;action=browse&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('File manager draft item id not found.');
        }
        $attachementsdraftid = $matches[1];

        $this->process_submission(array(
                'answer' => 'Here is a picture: <img src="' . $CFG->wwwroot .
                                "/draftfile.php/{$usercontextid}/user/draft/{$editordraftid}/smile.txt" .
                                '" alt="smile">.',
                'answerformat' => FORMAT_HTML,
                'answer:itemid' => $editordraftid,
                'attachments' => $attachementsdraftid));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);

        // Now submit all and finish.
        $this->finish();
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now start a new attempt based on the old one.
        $this->load_quba();
        $oldqa = $this->get_question_attempt();

        $q = question_bank::load_question($question->id);
        $this->quba = question_engine::make_questions_usage_by_activity('unit_test',
                context_system::instance());
        $this->quba->set_preferred_behaviour('deferredfeedback');
        $this->slot = $this->quba->add_question($q, 1);
        $this->quba->start_question_based_on($this->slot, $oldqa);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->save_quba();

        // Now save the same response again, and ensure that a new step is not created.
        $this->load_quba();

        $this->render();
        if (!preg_match('/env=editor&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('Editor draft item id not found.');
        }
        $editordraftid = $matches[1];
        if (!preg_match('/env=filemanager&amp;action=browse&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('File manager draft item id not found.');
        }
        $attachementsdraftid = $matches[1];

        $this->process_submission(array(
                'answer' => 'Here is a picture: <img src="' . $CFG->wwwroot .
                                "/draftfile.php/{$usercontextid}/user/draft/{$editordraftid}/smile.txt" .
                                '" alt="smile">.',
                'answerformat' => FORMAT_HTML,
                'answer:itemid' => $editordraftid,
                'attachments' => $attachementsdraftid));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(1);
    }

    public function test_deferred_feedback_html_editor_with_files_attempt_on_last_no_files_uploaded() {
        global $CFG, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $usercontextid = context_user::instance($USER->id)->id;
        $fs = get_file_storage();

        // Create an essay question in the DB.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('essay', 'editorfilepicker', array('category' => $cat->id));

        // Start attempt at the question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        // First we need to get the draft item ids.
        $this->render();
        if (!preg_match('/env=editor&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('Editor draft item id not found.');
        }
        $editordraftid = $matches[1];
        if (!preg_match('/env=filemanager&amp;action=browse&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new coding_exception('File manager draft item id not found.');
        }
        $attachementsdraftid = $matches[1];

        $this->process_submission(array(
                'answer' => 'I refuse to draw you a picture, so there!',
                'answerformat' => FORMAT_HTML,
                'answer:itemid' => $editordraftid,
                'attachments' => $attachementsdraftid));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now submit all and finish.
        $this->finish();
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now start a new attempt based on the old one.
        $this->load_quba();
        $oldqa = $this->get_question_attempt();

        $q = question_bank::load_question($question->id);
        $this->quba = question_engine::make_questions_usage_by_activity('unit_test',
                context_system::instance());
        $this->quba->set_preferred_behaviour('deferredfeedback');
        $this->slot = $this->quba->add_question($q, 1);
        $this->quba->start_question_based_on($this->slot, $oldqa);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->save_quba();

        // Check the display.
        $this->load_quba();
        $this->render();
        $this->assertRegExp('/I refuse to draw you a picture, so there!/', $this->currentoutput);
    }
}
