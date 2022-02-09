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

namespace qbank_previewquestion;

use context_course;
use moodle_url;
use core\plugininfo\qbank;
use question_bank;
use question_engine;
use stdClass;

/**
 * Helper tests for question preview.
 *
 * @package    qbank_previewquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_previewquestion\helper
 */
class helper_test extends \advanced_testcase {

    /**
     * @var bool|\context|\context_course $context
     */
    public $context;

    /**
     * @var object $questiondata;
     */
    public $questiondata;

    /**
     * @var \question_usage_by_activity $quba
     */
    public $quba;

    /**
     * @var question_preview_options $options
     */
    public $options;

    /**
     * @var \moodle_url $returnurl
     */
    public $returnurl;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        // Create a course.
        $course = $generator->create_course();
        $this->context = context_course::instance($course->id);
        // Create a question in the default category.
        $contexts = new \core_question\local\bank\question_edit_contexts($this->context);
        $cat = question_make_default_categories($contexts->all());
        $this->questiondata = $questiongenerator->create_question('numerical', null,
                ['name' => 'Example question', 'category' => $cat->id]);
        $this->quba = question_engine::make_questions_usage_by_activity('core_question_preview',
            \context_user::instance($USER->id));
        $this->options = new question_preview_options($this->questiondata);
        $this->options->load_user_defaults();
        $this->options->set_from_request();
        $this->returnurl = new moodle_url('/question/edit.php');
    }

    /**
     * Test the preview action url from the helper class.
     *
     * @covers ::question_preview_action_url
     */
    public function test_question_preview_action_url() {
        $actionurl = helper::question_preview_action_url($this->questiondata->id, $this->quba->get_id(), $this->options,
                $this->context, $this->returnurl);
        $params = [
           'id' => $this->questiondata->id,
           'previewid' => $this->quba->get_id(),
           'returnurl' => $this->returnurl,
           'courseid' => $this->context->instanceid
        ];
        $params = array_merge($params, $this->options->get_url_params());
        $expectedurl = new moodle_url('/question/bank/previewquestion/preview.php', $params);
        $this->assertEquals($expectedurl, $actionurl);
    }

    /**
     * Test the preview form url from the helper class.
     *
     * @covers ::question_preview_form_url
     */
    public function test_question_preview_form_url() {
        $formurl = helper::question_preview_form_url($this->questiondata->id, $this->context, $this->quba->get_id(), $this->returnurl);
        $params = [
            'id' => $this->questiondata->id,
            'previewid' => $this->quba->get_id(),
            'returnurl' => $this->returnurl,
            'courseid' => $this->context->instanceid
        ];
        $expectedurl = new moodle_url('/question/bank/previewquestion/preview.php', $params);
        $this->assertEquals($expectedurl, $formurl);
    }

    /**
     * Test the preview url from the helper class.
     *
     * @covers ::question_preview_url
     */
    public function test_question_preview_url() {
        $previewurl = helper::question_preview_url($this->questiondata->id, $this->options->behaviour, $this->options->maxmark,
                $this->options, $this->options->variant, $this->context);
        $params = [
            'id' => $this->questiondata->id,
            'behaviour' => $this->options->behaviour,
            'maxmark' => $this->options->maxmark,
            'courseid' => $this->context->instanceid
        ];
        // Extra params for options.
        $params['correctness']     = $this->options->correctness;
        $params['marks']           = $this->options->marks;
        $params['markdp']          = $this->options->markdp;
        $params['feedback']        = (bool) $this->options->feedback;
        $params['generalfeedback'] = (bool) $this->options->generalfeedback;
        $params['rightanswer']     = (bool) $this->options->rightanswer;
        $params['history']         = (bool) $this->options->history;
        $expectedurl = new moodle_url('/question/bank/previewquestion/preview.php', $params);
        $this->assertEquals($expectedurl, $previewurl);
    }

    /**
     * Test the preview comment callback if available.
     *
     * @covers ::get_preview_extra_elements
     */
    public function test_get_preview_extra_elements() {
        global $PAGE;
        $PAGE->set_url('/');

        $question = \question_bank::load_question($this->questiondata->id);
        list($comment, $extraelements) = helper::get_preview_extra_elements($question, $this->context->instanceid);
        if (qbank::is_plugin_enabled('qbank_comment')) {
            $this->assertStringContainsString("comment-area", $comment);
        } else {
            $this->assertEquals('', $comment);
        }
    }

    /**
     * Test method load_versions().
     *
     * @covers ::load_versions
     */
    public function test_load_versions() {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat1 = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1, 'idnumber' => 'myqcat']);
        $questiongenerated = $generator->create_question('description', null, ['name' => 'q1', 'category' => $qcat1->id]);
        $qtypeobj = question_bank::get_qtype($questiongenerated->qtype);
        $question = question_bank::load_question($questiongenerated->id);
        $versionids = helper::load_versions($question->questionbankentryid);
        $this->assertCount(1, $versionids);
        $fromform = new stdClass();
        $fromform->name = 'Name edited';
        $fromform->category = $qcat1->id;
        $qtypeobj->save_question($questiongenerated, $fromform);
        $versionids = helper::load_versions($question->questionbankentryid);
        $this->assertCount(2, $versionids);
    }
}
