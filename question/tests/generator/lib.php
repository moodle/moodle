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
 * Quiz module test data generator.
 *
 * @package    core_question
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_question\local\bank\question_version_status;

/**
 * Class core_question_generator for generating question data.
 *
 * @package   core_question
 * @copyright 2013 The Open University
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_generator extends component_generator_base {

    /**
     * @var number of created instances
     */
    protected $categorycount = 0;

    /**
     * Make the category count to zero.
     */
    public function reset() {
        $this->categorycount = 0;
    }

    /**
     * Create a new question category.
     *
     * @param array|stdClass $record
     * @return stdClass question_categories record.
     */
    public function create_question_category($record = null) {
        global $DB;

        $this->categorycount ++;

        $defaults = [
            'name'       => 'Test question category ' . $this->categorycount,
            'info'       => '',
            'infoformat' => FORMAT_HTML,
            'stamp'      => make_unique_id_code(),
            'idnumber'   => null,
        ];

        $record = $this->datagenerator->combine_defaults_and_record($defaults, $record);

        if (!isset($record['contextid'])) {
            if (isset($record['parent'])) {
                $record['contextid'] = $DB->get_field('question_categories', 'contextid', ['id' => $record['parent']]);
            } else {
                $qbank = $this->datagenerator->create_module('qbank', ['course' => SITEID]);
                $record['contextid'] = context_module::instance($qbank->cmid)->id;
            }
        } else {
            // Any requests for a question category in a contextlevel that is no longer supported
            // will have a qbank instance created on the associated context and then the category
            // will be made for that context instead.
            $context = context::instance_by_id($record['contextid']);
            if ($context->contextlevel !== CONTEXT_MODULE) {
                $course = match ($context->contextlevel) {
                    CONTEXT_COURSE => get_course($context->instanceid),
                    CONTEXT_SYSTEM => get_site(),
                    CONTEXT_COURSECAT => $this->datagenerator->create_course(['category' => $context->instanceid]),
                    default => throw new \Exception('Invalid context to infer a question bank from.'),
                };
                $qbank = \core_question\local\bank\question_bank_helper::get_default_open_instance_system_type($course, true);
                $bankcontext = context_module::instance($qbank->id);
            } else {
                $bankcontext = $context;
            }
            $record['contextid'] = $bankcontext->id;
        }

        if (!isset($record['parent'])) {
            $record['parent'] = question_get_top_category($record['contextid'], true)->id;
        }
        if (!isset($record['sortorder'])) {
            $manager = new \core_question\category_manager();
            $record['sortorder'] = $manager->get_max_sortorder($record['parent']) + 1;
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
        $question = new stdClass();
        $question->qtype = $qtype;
        $question->createdby = 0;
        $question->idnumber = null;
        $question->status = question_version_status::QUESTION_STATUS_READY;

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
     * @return stdClass the question data, including version info and questionbankentryid
     */
    public function update_question($question, $which = null, $overrides = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
        $question = clone($question);

        $qtype = $question->qtype;

        $fromform = test_question_maker::get_question_form_data($qtype, $which);
        $fromform = (object) $this->datagenerator->combine_defaults_and_record((array) $question, $fromform);
        $fromform = (object) $this->datagenerator->combine_defaults_and_record((array) $fromform, $overrides);
        $fromform->status = $fromform->status ?? $question->status;

        $question = question_bank::get_qtype($qtype)->save_question($question, $fromform);

        $validoverrides = ['createdby', 'modifiedby', 'timemodified'];
        if ($overrides && !empty(array_intersect($validoverrides, array_keys($overrides)))) {
            // Manually update the createdby, modifiedby and timemodified because questiontypebase forces
            // current user and time and some tests require a specific user or time.
            foreach ($validoverrides as $validoverride) {
                if (array_key_exists($validoverride, $overrides)) {
                    $question->{$validoverride} = $overrides[$validoverride];
                }
            }
            $DB->update_record('question', $question);
        }
        $questionversion = $DB->get_record('question_versions', ['questionid' => $question->id], '*', MUST_EXIST);
        $question->versionid = $questionversion->id;
        $question->questionbankentryid = $questionversion->questionbankentryid;
        $question->version = $questionversion->version;
        $question->status = $questionversion->status;

        return $question;
    }

    /**
     * Set up a course category, a course, a mod_qbank instance, a question category for the mod_qbank instance,
     * and 2 questions for testing.
     *
     * @return array of the data objects mentioned above
     */
    public function setup_course_and_questions() {
        $datagenerator = $this->datagenerator;
        $category = $datagenerator->create_category();
        $course = $datagenerator->create_course([
            'numsections' => 5,
            'category' => $category->id
        ]);
        $qbank = $datagenerator->create_module('qbank', ['course' => $course->id]);
        $context = context_module::instance($qbank->cmid);

        $qcat = $this->create_question_category(['contextid' => $context->id]);

        $questions = [
                $this->create_question('shortanswer', null, ['category' => $qcat->id]),
                $this->create_question('shortanswer', null, ['category' => $qcat->id]),
        ];

        return [$category, $course, $qcat, $questions, $qbank];
    }

    /**
     * This method can construct what the post data would be to simulate a user submitting
     * responses to a number of questions within a question usage.
     *
     * In the responses array, the array keys are the slot numbers for which a response will
     * be submitted. You can submit a response to any number of questions within the usage.
     * There is no need to do them all. The values are a string representation of the response.
     * The exact meaning of that depends on the particular question type. These strings
     * are passed to the un_summarise_response method of the question to decode.
     *
     * @param question_usage_by_activity $quba the question usage.
     * @param array $responses the responses to submit, in the format described above.
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
