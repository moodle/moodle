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

namespace qbank_history;

use question_bank;

/**
 * Helper class test.
 *
 * @package    qbank_history
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_history\helper
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
     * @var \moodle_url $returnurl
     */
    public $returnurl;

    /**
     * @var int $courseid
     */
    public $courseid;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        // Create a course.
        $course = $generator->create_course();
        $this->courseid = $course->id;
        $this->context = \context_course::instance($course->id);
        // Create a question in the default category.
        $contexts = new \core_question\local\bank\question_edit_contexts($this->context);
        $cat = question_make_default_categories($contexts->all());
        $question = $questiongenerator->create_question('numerical', null,
            ['name' => 'Example question', 'category' => $cat->id]);
        $this->questiondata = question_bank::load_question($question->id);
        $this->returnurl = new \moodle_url('/question/edit.php');
    }

    /**
     * Test the history action url from the helper class.
     *
     * @covers ::question_history_url
     */
    public function test_question_history_url() {
        $this->resetAfterTest();
        $filter = urlencode('filters[]');
        $actionurl = helper::question_history_url(
            $this->questiondata->questionbankentryid,
            $this->returnurl,
            $this->courseid,
            $filter,
        );
        $params = [
            'entryid' => $this->questiondata->questionbankentryid,
            'returnurl' => $this->returnurl,
            'courseid' => $this->courseid,
            'filter' => $filter,
        ];
        $expectedurl = new \moodle_url('/question/bank/history/history.php', $params);
        $this->assertEquals($expectedurl, $actionurl);
    }

    /**
     * Test the history action url when the filter parameter is null.
     *
     * @covers ::question_history_url
     */
    public function test_question_history_url_null_filter() {
        $this->resetAfterTest();
        $actionurl = helper::question_history_url(
            $this->questiondata->questionbankentryid,
            $this->returnurl,
            $this->courseid,
            null,
        );
        $params = [
            'entryid' => $this->questiondata->questionbankentryid,
            'returnurl' => $this->returnurl,
            'courseid' => $this->courseid,
        ];
        $expectedurl = new \moodle_url('/question/bank/history/history.php', $params);
        $this->assertEquals($expectedurl, $actionurl);
    }
}
