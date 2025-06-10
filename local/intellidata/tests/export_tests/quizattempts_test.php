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
 * Quiz attempt migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\entities\quizzes\attempt;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\setup_helper;
use local_intellidata\test_helper;
use quiz;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Quiz attempt migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class quizattempts_test extends custom_db_client_testcase {

    /**
     * Test quiz attempt create.
     *
     * @covers \local_intellidata\entities\quizzes\attempt
     * @covers \local_intellidata\entities\quizzes\migration
     * @covers \local_intellidata\entities\quizzes\observer::course_module_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_quiz_attempt_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_quiz_attempt_test(0);
    }

    /**
     * Test quiz attempt delete.
     *
     * @covers \local_intellidata\entities\quizzes\activity
     * @covers \local_intellidata\entities\quizzes\migration
     * @covers \local_intellidata\entities\quizzes\observer::course_module_deleted
     */
    public function test_delete() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->delete_activity_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->delete_activity_test(0);
    }

    /**
     * Create quiz attempt test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_quiz_attempt_test($tracking) {

        $coursedata = [
            'fullname' => 'ibcoursequizquestion1a' . $tracking,
            'idnumber' => '3333333a' . $tracking,
            'shortname' => 'ibcoursequizquestion1a' . $tracking,
        ];
        $course = generator::create_course($coursedata);
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        quiz_add_quiz_question($question->id, $quiz);

        if ($this->release >= 4.2) {
            $quizobj = \mod_quiz\quiz_settings::create($quiz->id, $student1->id);
        } else {
            $quizobj = quiz::create($quiz->id);
        }

        $this->setUser($student1);
        // Create attempt for student1.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null, false, [], []);

        $data = [
            'id' => $attempt->id,
            'quiz' => $attempt->quiz,
            'userid' => $attempt->userid,
        ];

        $entity = new attempt((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'quizattempts']);
        $datarecord = $storage->get_log_entity_data('c', $data);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Delete quiz attempt test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function delete_activity_test($tracking) {
        $coursedata = [
            'fullname' => 'ibcoursequizquestion1ad' . $tracking,
            'idnumber' => '3333333ad' . $tracking,
            'shortname' => 'ibcoursequizquestion1ad' . $tracking,
        ];
        $course = generator::create_course($coursedata);
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'grade' => 100.0, 'sumgrades' => 2, 'layout' => '1,0']);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);

        quiz_add_quiz_question($question->id, $quiz);

        if ($this->release >= 4.2) {
            $quizobj = \mod_quiz\quiz_settings::create($quiz->id, $student1->id);
        } else {
            $quizobj = quiz::create($quiz->id);
        }

        $this->setUser($student1);
        // Create attempt for student1.
        $attempt = quiz_prepare_and_start_new_attempt($quizobj, 1, null, false, [], []);

        quiz_delete_attempt($attempt, $quizobj->get_quiz());

        $data = [
            'id' => $attempt->id,
        ];

        $entity = new attempt((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'quizattempts']);
        $datarecord = $storage->get_log_entity_data('d', ['id' => $data['id']]);
        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);

        $this->assertNotEmpty($datarecord);
        $this->assertEquals($entitydata->id, $datarecorddata->id);
    }
}
