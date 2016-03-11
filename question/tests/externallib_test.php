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
 * Question external functions tests.
 *
 * @package    core_question
 * @category   external
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');

/**
 * Wiki module external functions tests
 *
 * @package    core_question
 * @category   external
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class core_question_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $this->course->id));

        // Create users.
        $this->student = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');

        // Create first page.
        $this->firstpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($this->wiki);
    }

    /**
     * Test update question flag
     */
    public function test_core_question_update_flag() {

        $question = test_question_maker::make_question('shortanswer');
        $quba = question_engine::make_questions_usage_by_activity('test', context_system::instance());
        $slot = $quba->add_question($question);
        $quba->start_all_questions();
        $qa = $quba->get_question_attempt($slot);

        $qubaid = $quba->get_id();
        $questionid = $question->id;
        $qaid = $qa->id;
        $checksum = md5($qubaid . "_" . $this->student->secret . "_" . $questionid . "_" . $qaid . "_" . $slot);

        $flag = core_question_external::update_flag($qubaid, $questionid, $qaid, $slot, $checksum, true);
        $this->assertTrue($flag['status']);
    }
}
