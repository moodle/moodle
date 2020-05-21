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

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz module test data generator class
 *
 * @package    moodlecore
 * @subpackage question
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_generator extends component_generator_base {

    /**
     * @var number of created instances
     */
    protected $categorycount = 0;

    public function reset() {
        $this->categorycount = 0;
    }

    /**
     * Create a new question category.
     * @param array|stdClass $record
     * @return stdClass question_categories record.
     */
    public function create_question_category($record = null) {
        global $DB;

        $this->categorycount++;

        $defaults = array(
            'name'       => 'Test question category ' . $this->categorycount,
            'info'       => '',
            'infoformat' => FORMAT_HTML,
            'stamp'      => make_unique_id_code(),
            'sortorder'  => 999,
            'idnumber'   => null
        );

        $record = $this->datagenerator->combine_defaults_and_record($defaults, $record);

        if (!isset($record['contextid'])) {
            $record['contextid'] = context_system::instance()->id;
        }
        if (!isset($record['parent'])) {
            $record['parent'] = question_get_top_category($record['contextid'], true)->id;
        }
        $record['id'] = $DB->insert_record('question_categories', $record);
        return (object) $record;
    }

    /**
     * Create a new question. The question is initialised using one of the
     * examples from the appropriate {@link question_test_helper} subclass.
     * Then, any files you want to change from the value in the base example you
     * can override using $overrides.
     *
     * @param string $qtype the question type to create an example of.
     * @param string $which as for the corresponding argument of
     *      {@link question_test_helper::get_question_form_data}. null for the default one.
     * @param array|stdClass $overrides any fields that should be different from the base example.
     * @return stdClass the question data.
     */
    public function create_question($qtype, $which = null, $overrides = null) {
        global $CFG;
        require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

        $fromform = test_question_maker::get_question_form_data($qtype, $which);
        $fromform = (object) $this->datagenerator->combine_defaults_and_record(
                (array) $fromform, $overrides);

        $question = new stdClass();
        $question->category  = $fromform->category;
        $question->qtype     = $qtype;
        $question->createdby = 0;
        $question->idnumber = null;

        return $this->update_question($question, $which, $overrides);
    }

    /**
     * Create a tag on a question.
     *
     * @param array $data with two elements ['questionid' => 123, 'tag' => 'mytag'].
     */
    public function create_question_tag(array $data): void {
        $question = question_bank::load_question($data['questionid']);
        core_tag_tag::add_item_tag('core_question', 'question', $question->id,
                context::instance_by_id($question->contextid), $data['tag'], 0);
    }

    /**
     * Update an existing question.
     *
     * @param stdClass $question the question data to update.
     * @param string $which as for the corresponding argument of
     *      {@link question_test_helper::get_question_form_data}. null for the default one.
     * @param array|stdClass $overrides any fields that should be different from the base example.
     * @return stdClass the question data.
     */
    public function update_question($question, $which = null, $overrides = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

        $qtype = $question->qtype;

        $fromform = test_question_maker::get_question_form_data($qtype, $which);
        $fromform = (object) $this->datagenerator->combine_defaults_and_record(
                (array) $question, $fromform);
        $fromform = (object) $this->datagenerator->combine_defaults_and_record(
                (array) $fromform, $overrides);

        $question = question_bank::get_qtype($qtype)->save_question($question, $fromform);

        if ($overrides && array_key_exists('createdby', $overrides)) {
            // Manually update the createdby because questiontypebase forces current user and some tests require a
            // specific user.
            $question->createdby = $overrides['createdby'];
            $DB->update_record('question', $question);
        }

        return $question;
    }

    /**
     * Setup a course category, course, a question category, and 2 questions
     * for testing.
     *
     * @param string $type The type of question category to create.
     * @return array The created data objects
     */
    public function setup_course_and_questions($type = 'course') {
        $datagenerator = $this->datagenerator;
        $category = $datagenerator->create_category();
        $course = $datagenerator->create_course([
            'numsections' => 5,
            'category' => $category->id
        ]);

        switch ($type) {
            case 'category':
                $context = context_coursecat::instance($category->id);
                break;

            case 'system':
                $context = context_system::instance();
                break;

            default:
                $context = context_course::instance($course->id);
                break;
        }

        $qcat = $this->create_question_category(['contextid' => $context->id]);

        $questions = array(
                $this->create_question('shortanswer', null, ['category' => $qcat->id]),
                $this->create_question('shortanswer', null, ['category' => $qcat->id]),
        );

        return array($category, $course, $qcat, $questions);
    }

    /**
     * This method can construct what the post data would be to simulate a user submitting
     * responses to a number of questions within a question usage.
     *
     * In the responses array, the array keys are the slot numbers for which a response will
     * be submitted. You can submit a response to any number of responses within the usage.
     * There is no need to do them all. The values are a string representation of the response.
     * The exact meaning of that depends on the particular question type. These strings
     * are passed to the un_summarise_response method of the question to decode.
     *
     * @param question_usage_by_activity $quba the question usage.
     * @param array $responses the resonses to submit, in the format described above.
     * @param bool $checkbutton if simulate a click on the check button for each question, else simulate save.
     *      This should only be used with behaviours that have a check button.
     * @return array that can be passed to methods like $quba->process_all_actions as simulated POST data.
     */
    public function get_simulated_post_data_for_questions_in_usage(
            question_usage_by_activity $quba, array $responses, $checkbutton) {
        $postdata = [];

        foreach ($responses as $slot => $responsesummary) {
            $postdata += $this->get_simulated_post_data_for_question_attempt(
                    $quba->get_question_attempt($slot), $responsesummary, $checkbutton);
        }

        return $postdata;
    }

    /**
     * This method can construct what the post data would be to simulate a user submitting
     * responses to one particular question attempt.
     *
     * The $responsesummary is a string representation of the response to be submitted.
     * The exact meaning of that depends on the particular question type. These strings
     * are passed to the un_summarise_response method of the question to decode.
     *
     * @param question_attempt $qa the question attempt for which we are generating POST data.
     * @param string $responsesummary a textual summary of the response, as described above.
     * @param bool $checkbutton if simulate a click on the check button, else simulate save.
     *      This should only be used with behaviours that have a check button.
     * @return array the simulated post data that can be passed to $quba->process_all_actions.
     */
    public function get_simulated_post_data_for_question_attempt(
            question_attempt $qa, $responsesummary, $checkbutton) {

        $question = $qa->get_question();
        if (!$question instanceof question_with_responses) {
            return [];
        }

        $postdata = [];
        $postdata[$qa->get_control_field_name('sequencecheck')] = (string)$qa->get_sequence_check_count();
        $postdata[$qa->get_flag_field_name()] = (string)(int)$qa->is_flagged();

        $response = $question->un_summarise_response($responsesummary);
        foreach ($response as $name => $value) {
            $postdata[$qa->get_qt_field_name($name)] = (string)$value;
        }

        // TODO handle behaviour variables better than this.
        if ($checkbutton) {
            $postdata[$qa->get_behaviour_field_name('submit')] = 1;
        }

        return $postdata;
    }
}
