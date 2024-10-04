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

namespace qbank_exporttoxml;

use context_course;
use context_module;
use moodle_url;
use question_bank;

/**
 * Class helper unit tests.
 *
 * @package    qbank_exporttoxml
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_exporttoxml\helper
 */
final class helper_test extends \advanced_testcase {

    /**
     * Test the export single question url.
     *
     * @covers ::question_get_export_single_question_url
     */
    public function test_question_get_export_single_question_url(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create a course and an activity.
        $course = $generator->create_course();
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcontext = \context_module::instance($qbank->cmid);
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);

        // Create a question in each place.
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $qbankqcat = $questiongenerator->create_question_category(['contextid' => $qbankcontext->id]);
        $qbankq = $questiongenerator->create_question('truefalse', null, ['category' => $qbankqcat->id]);
        $quizqcat = $questiongenerator->create_question_category(['contextid' => context_module::instance($quiz->cmid)->id]);
        $quizq = $questiongenerator->create_question('truefalse', null, ['category' => $quizqcat->id]);

        // Verify some URLs.
        $this->assertEquals(new moodle_url('/question/bank/exporttoxml/exportone.php',
                ['id' => $qbankq->id, 'cmid' => $qbank->cmid, 'sesskey' => sesskey()]),
                helper::question_get_export_single_question_url(
                        question_bank::load_question_data($qbankq->id)));

        $this->assertEquals(new moodle_url('/question/bank/exporttoxml/exportone.php',
                ['id' => $quizq->id, 'cmid' => $quiz->cmid, 'sesskey' => sesskey()]),
                helper::question_get_export_single_question_url(
                        question_bank::load_question($quizq->id)));
    }

}
