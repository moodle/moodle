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
 * Quiz questions test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\helpers\SettingsHelper;
use stdClass;
use core_table\local\filter\filter;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\setup_helper;
use local_intellidata\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Quiz questions test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class quizquestions_test extends custom_db_client_testcase {

    /**
     * Test quiz question create.
     *
     * @covers \local_intellidata\entities\quizquestions\quizquestion
     * @covers \local_intellidata\entities\quizquestions\migration
     * @covers \local_intellidata\entities\quizquestions\observer::question_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        // No events until version 3.7.
        if (!ParamsHelper::compare_release('3.7.0')) {
            return;
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_quiz_questions_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_quiz_questions_test(0);
    }

    /**
     * Create quiz question test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function create_quiz_questions_test($tracking) {
        $coursedata = [
            'fullname' => 'ibcoursequizquestion1' . $tracking,
            'idnumber' => 'qa3333333' . $tracking,
            'shortname' => 'ibcoursequizquestion1' . $tracking,
        ];
        $course = generator::create_course($coursedata);

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'questionsperpage' => 3, 'grade' => 100.0]);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        quiz_add_quiz_question($question->id, $quiz);
        $this->add_random_questions($quiz, 0, $cat->id, 1);

        $data = [
            'id' => $question->id,
            'name' => $question->name,
            'questiontext' => $question->questiontext,
            'qtype' => $question->qtype,
            'defaultmark' => $question->defaultmark,
        ];

        $entity = new \local_intellidata\entities\quizquestions\quizquestion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'quizquestions']);

        $datarecord = $storage->get_log_entity_data('c', $data);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Test question text limiter.
     *
     * @covers \local_intellidata\entities\quizquestions\quizquestion
     * @covers \local_intellidata\entities\quizquestions\migration
     * @covers \local_intellidata\entities\quizquestions\observer::question_created
     */
    public function test_question_text_limiter() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        // No events until version 3.7.
        if (!ParamsHelper::compare_release('3.7.0')) {
            return;
        }

        $coursedata = [
            'fullname' => 'ibcoursequizquestion2',
            'idnumber' => '33333335',
            'shortname' => 'ibcoursequizquestion2',
        ];
        $course = generator::create_course($coursedata);

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'questionsperpage' => 3, 'grade' => 100.0]);

        $qtext = random_string(50005);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, [
            'category' => $cat->id, 'questiontext' => ['text' => $qtext],
        ]);

        quiz_add_quiz_question($question->id, $quiz);
        $this->add_random_questions($quiz, 0, $cat->id, 1);

        $data = [
            'id' => $question->id,
            'name' => $question->name,
            'questiontext' => $qtext,
            'qtype' => $question->qtype,
            'defaultmark' => $question->defaultmark,
        ];

        $entity = new \local_intellidata\entities\quizquestions\quizquestion((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);
        $storage = StorageHelper::get_storage_service(['name' => 'quizquestions']);

        $cdata = $data;
        unset($cdata['questiontext']);
        $datarecord = $storage->get_log_entity_data('c', $cdata);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $entitydata->questiontext = substr($entitydata->questiontext, 0, 5000);

        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Add random question.
     *
     * @param stdClass $quiz
     * @param int $page
     * @param int $categoryid
     * @param int $number
     * @return void
     * @throws \invalid_parameter_exception
     */
    protected function add_random_questions(stdClass $quiz, int $page, int $categoryid, int $number) {

        if (class_exists('\mod_quiz\structure') && class_exists('\mod_quiz\quiz_settings') &&
                method_exists('\mod_quiz\structure', 'add_random_questions')) {

            $settings = \mod_quiz\quiz_settings::create($quiz->id);
            $structure = \mod_quiz\structure::create_for_quiz($settings);
            $filtercondition = [
                'filter' => [
                    'category' => [
                        'jointype' => filter::JOINTYPE_DEFAULT,
                        'values' => [$categoryid],
                        'filteroptions' => ['includesubcategories' => false],
                    ],
                ],
            ];
            $structure->add_random_questions($page, $number, $filtercondition);
        } else {
            quiz_add_random_questions($quiz, $page, $categoryid, $number, false);
        }
    }
}
