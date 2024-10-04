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
final class qbank_preview_helper_test extends \advanced_testcase {

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
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        // Create a course.
        $course = $generator->create_course();
        $qbank = $generator->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = \context_module::instance($qbank->cmid);
        $this->context = $qbankcontext;
        // Create a question in the default category.
        $contexts = new \core_question\local\bank\question_edit_contexts($qbankcontext);
        $cat = question_get_default_category($contexts->lowest()->id, true);
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
    public function test_question_preview_action_url(): void {
        $actionurl = helper::question_preview_action_url($this->questiondata->id, $this->quba->get_id(), $this->options,
                $this->context, $this->returnurl, question_preview_options::ALWAYS_LATEST);
        $params = [
           'id' => $this->questiondata->id,
           'previewid' => $this->quba->get_id(),
           'returnurl' => $this->returnurl,
           'cmid' => $this->context->instanceid,
           'restartversion' => question_preview_options::ALWAYS_LATEST,
        ];
        $params = array_merge($params, $this->options->get_url_params());
        $expectedurl = new moodle_url('/question/bank/previewquestion/preview.php', $params);
        $this->assertEquals($expectedurl, $actionurl);
    }

    /**
     * Test the preview action url from the helper class when no restartversion is passed.
     *
     * @covers ::question_preview_action_url
     */
    public function test_question_preview_action_url_no_restartversion(): void {
        $actionurl = helper::question_preview_action_url($this->questiondata->id, $this->quba->get_id(), $this->options,
                $this->context, $this->returnurl);
        $params = [
            'id' => $this->questiondata->id,
            'previewid' => $this->quba->get_id(),
            'returnurl' => $this->returnurl,
            'cmid' => $this->context->instanceid,
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
    public function test_question_preview_form_url(): void {
        $formurl = helper::question_preview_form_url(
                $this->questiondata->id, $this->context, $this->quba->get_id(), $this->returnurl);
        $params = [
            'id' => $this->questiondata->id,
            'previewid' => $this->quba->get_id(),
            'returnurl' => $this->returnurl,
            'cmid' => $this->context->instanceid,
        ];
        $expectedurl = new moodle_url('/question/bank/previewquestion/preview.php', $params);
        $this->assertEquals($expectedurl, $formurl);
    }

    /**
     * Test the preview url from the helper class.
     *
     * @covers ::question_preview_url
     */
    public function test_question_preview_url(): void {
        $previewurl = helper::question_preview_url($this->questiondata->id, $this->options->behaviour, $this->options->maxmark,
                $this->options, $this->options->variant, $this->context, null, question_preview_options::ALWAYS_LATEST);
        $params = [
            'id' => $this->questiondata->id,
            'behaviour' => $this->options->behaviour,
            'maxmark' => $this->options->maxmark,
            'cmid' => $this->context->instanceid,
            'restartversion' => question_preview_options::ALWAYS_LATEST,
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
     * Test the preview url from the helper class.
     *
     * @covers ::question_preview_url
     */
    public function test_question_preview_url_no_restartversion(): void {
        $previewurl = helper::question_preview_url($this->questiondata->id, $this->options->behaviour, $this->options->maxmark,
                $this->options, $this->options->variant, $this->context, null);
        $params = [
            'id' => $this->questiondata->id,
            'behaviour' => $this->options->behaviour,
            'maxmark' => $this->options->maxmark,
            'cmid' => $this->context->instanceid,
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
    public function test_get_preview_extra_elements(): void {
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
    public function test_load_versions(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat1 = $generator->create_question_category(['name' => 'My category', 'sortorder' => 1, 'idnumber' => 'myqcat']);
        $questiongenerated = $generator->create_question('description', null, ['name' => 'q1', 'category' => $qcat1->id]);

        $qtypeobj = question_bank::get_qtype($questiongenerated->qtype);
        $question = question_bank::load_question($questiongenerated->id);
        $versionids = helper::load_versions($question->questionbankentryid);
        $this->assertEquals([
            $question->id => 1,
        ], $versionids);

        $fromform = new stdClass();
        $fromform->name = 'Name edited';
        $fromform->category = $qcat1->id;
        $questiontwo = $qtypeobj->save_question($questiongenerated, $fromform);
        $versionids = helper::load_versions($question->questionbankentryid);
        $this->assertSame([
            $question->id => 1,
            $questiontwo->id => 2,
        ], $versionids);
    }

    /**
     * Test method get_restart_id().
     *
     * This should return the value of the specified version number, or the latest version if ALWAYS_LATEST is passed.
     *
     * @covers ::get_restart_id
     * @return void
     */
    public function test_get_restart_id(): void {
        $versions = [
            100 => 1,
            200 => 2,
            300 => 3
        ];

        $this->assertEquals(100, helper::get_restart_id($versions, 1));
        $this->assertEquals(200, helper::get_restart_id($versions, 2));
        $this->assertEquals(300, helper::get_restart_id($versions, 3));
        $this->assertEquals(300, helper::get_restart_id($versions, question_preview_options::ALWAYS_LATEST));
        $this->assertNull(helper::get_restart_id($versions, 4));
        $this->assertNull(helper::get_restart_id([], 1));
        $this->assertNull(helper::get_restart_id([], question_preview_options::ALWAYS_LATEST));
    }
}
