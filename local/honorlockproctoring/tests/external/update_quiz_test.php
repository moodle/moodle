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

namespace local_honorlockproctoring;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use local_honorlockproctoring\external\update_quiz;

/**
 * Honorlock proctoring test for module.
 *
 * @package   local_honorlockproctoring
 * @copyright 2023 Honorlock (https://honorlock.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_quiz_test extends \externallib_advanced_testcase {
    /**
     * @var update_quiz Keeps the update_quiz Class.
     */
    protected $updatequiz;

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
        $this->update_quiz = new update_quiz();
    }

    /**
     * Test the class creation
     *
     * @covers \local_honorlockproctoring\external\update_quiz
     */
    public function test_class_creation(): void {
        $updatequiz = new update_quiz();

        $this->assertInstanceOf(update_quiz::class, $updatequiz);
    }

    /**
     * Test the execute_parameters method
     *
     * @covers \local_honorlockproctoring\external\update_quiz::execute_parameters
     */
    public function test_update_quiz_values_parameters(): void {
        $result = $this->update_quiz->execute_parameters();

        $this->assertIsObject($result);
    }

    /**
     * Test the update_quiz_values_returns method
     *
     * @covers \local_honorlockproctoring\external\update_quiz::execute_returns
     */
    public function test_update_quiz_values_returns(): void {
        $result = $this->update_quiz->execute_returns();

        $this->assertIsObject($result);
    }

    /**
     * Test the update_quiz_values method
     *
     * @covers \local_honorlockproctoring\external\update_quiz::execute
     */
    public function test_update_quiz_values(): void {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $this->setAdminUser();

        $result = $this->update_quiz->execute($quiz->id, ['password' => 'password']);

        $this->assertIsArray($result);
        $this->assertTrue($result['updatedvalues']['password']);
    }

    /**
     * Test the update_quiz_values method returns false if quiz doesn't exist
     *
     * @covers \local_honorlockproctoring\external\update_quiz::execute
     */
    public function test_update_quiz_values_returns_false(): void {
        $this->setAdminUser();

        $nonexistentquizid = random_int(1, 5000);

        $result = $this->update_quiz->execute($nonexistentquizid, ['password' => 'password']);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test the update_quiz_values method
     *
     * @covers \local_honorlockproctoring\external\update_quiz::execute
     */
    public function test_update_quiz_values_when_value_not_in_allowedquizupdates(): void {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $this->setAdminUser();

        $result = $this->update_quiz->execute($quiz->id, ['notpassword' => 'password']);

        $this->assertIsArray($result);
        $this->assertFalse($result['updatedvalues']['notpassword']);
    }

    /**
     * Test the get_quiz_questions_parameters method
     *
     * @covers \local_honorlockproctoring\external\update_quiz::execute_parameters
     */
    public function test_get_quiz_questions_parameters(): void {
        $result = $this->update_quiz->execute_parameters();

        $this->assertIsObject($result);
    }

    /**
     * Test the get_quiz_questions_returns method
     *
     * @covers \local_honorlockproctoring\external\update_quiz::execute_returns
     */
    public function test_get_quiz_questions_returns(): void {
        $result = $this->update_quiz->execute_returns();

        $this->assertIsObject($result);
    }
}
