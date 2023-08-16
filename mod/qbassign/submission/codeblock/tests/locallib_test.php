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
 * Tests for mod/qbassign/submission/codeblock/locallib.php
 *
 * @package   qbassignsubmission_codeblock
 * @copyright 2016 Cameron Ball
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qbassignsubmission_codeblock;

use mod_qbassign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/tests/generator.php');

/**
 * Unit tests for mod/qbassign/submission/codeblock/locallib.php
 *
 * @copyright  2016 Cameron Ball
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_qbassign_test_generator;

    /**
     * Test submission_is_empty
     *
     * @dataProvider submission_is_empty_testcases
     * @param string $submissiontext The code block submission text
     * @param bool $expected The expected return value
     */
    public function test_submission_is_empty($submissiontext, $expected) {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $qbassign = $this->create_instance($course, [
                'qbassignsubmission_codeblock_enabled' => true,
            ]);

        $this->setUser($student->id);

        $plugin = $qbassign->get_submission_plugin_by_type('codeblock');
        $result = $plugin->submission_is_empty((object) [
                'codeblock_editor' => [
                    'text' => $submissiontext,
                ],
            ]);
        $this->assertTrue($result === $expected);
    }

    /**
     * Test new_submission_empty
     *
     * @dataProvider submission_is_empty_testcases
     * @param string $submissiontext The file submission data
     * @param bool $expected The expected return value
     */
    public function test_new_submission_empty($submissiontext, $expected) {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $qbassign = $this->create_instance($course, [
                'qbassignsubmission_codeblock_enabled' => true,
            ]);

        $this->setUser($student->id);

        $result = $qbassign->new_submission_empty((object) [
                'codeblock_editor' => [
                    'text' => $submissiontext,
                ],
            ]);

        $this->assertTrue($result === $expected);
    }

    /**
     * Dataprovider for the test_submission_is_empty testcase
     *
     * @return array of testcases
     */
    public function submission_is_empty_testcases() {
        return [
            'Empty submission string' => ['', true],
            'Empty submission null' => [null, true],
            'Value 0' => [0, false],
            'String 0' => ['0', false],
            'Text' => ['Ai! laurië lantar lassi súrinen, yéni únótimë ve rámar aldaron!', false],
            'Image' => ['<img src="test.jpg" />', false],
            'Video' => ['<video controls="true"><source src="test.mp4"></video>', false],
            'Audio' => ['<audio controls="true"><source src="test.mp3"></audio>', false],
        ];
    }
}
