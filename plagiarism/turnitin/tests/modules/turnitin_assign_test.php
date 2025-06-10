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
 * Unit tests for (some of) plagiarism/turnitin/classes/modules/turnitin_assign.class.php.
 *
 * @package    plagiarism_turnitin
 * @copyright  2017 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot . '/mod/assign/externallib.php');

/**
 * Tests for assign
 *
 * @package turnitin
 */
class plagiarism_turnitin_assign_testcase extends advanced_testcase {

    /** @var stdClass created in setUp. */
    protected $course;

    /** @var stdClass created in setUp. */
    protected $assign;

    /**
     * Create a course and assignment module instance
     */
    public function setUp(): void {
        $this->course = $this->getDataGenerator()->create_course();
        $params = array(
            'course' => $this->course->id,
            'name' => 'assignment',
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 1,
            'assignsubmission_file_maxsizebytes' => 10
        );

        $this->assign = $this->getDataGenerator()->create_module('assign', $params);
    }

    /**
     * Test to check whether resubmissions are allowed.
     */
    public function test_check_is_resubmission_allowed() {
        $this->resetAfterTest(true);

        // Create module object.
        $moduleobject = new turnitin_assign();

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($this->assign->id, 1, 'file', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);
        $this->assertTrue($resubmissionallowed);

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($this->assign->id, 1, 'text_content', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);
        $this->assertTrue($resubmissionallowed);

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($this->assign->id, 1, 'text_content', ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL);
        $this->assertFalse($resubmissionallowed);

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($this->assign->id, 0, 'file', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);
        $this->assertFalse($resubmissionallowed);

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($this->assign->id, 0, 'text_content', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);
        $this->assertFalse($resubmissionallowed);

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($this->assign->id, 1, 'file', ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL);
        $this->assertFalse($resubmissionallowed);
    }


    /**
     * Test that resubmissions are not allowed for files if the maximum files in a submission is more than 1.
     */
    public function test_check_is_resubmission_allowed_maxfiles_above_threshold() {
        $this->resetAfterTest(true);

        $params = array(
            'course' => $this->course->id,
            'name' => 'assignment',
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 2,
            'assignsubmission_file_maxsizebytes' => 10
        );

        $assign = $this->getDataGenerator()->create_module('assign', $params);

        // Create module object.
        $moduleobject = new turnitin_assign();

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($assign->id, 1, 'file', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);
        $this->assertFalse($resubmissionallowed);

        $resubmissionallowed = $moduleobject->is_resubmission_allowed($assign->id, 1, 'text_content', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);
        $this->assertTrue($resubmissionallowed);
    }
}
