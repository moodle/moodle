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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_iomadmerge_enrolments_testcase extends advanced_testcase {
    /**
     * Setup the test.
     */
    public function setUp(): void {
        global $CFG;
        require_once("$CFG->dirroot/admin/tool/iomadmerge/lib/iomadmergetool.php");
        $this->resetAfterTest(true);
    }

    /**
     * Enrol two users on one unique course each and one shared course
     * then merge them.
     * @group tool_iomadmerge
     * @group tool_iomadmerge_enrolments
     */
    public function test_mergeenrolments() {
        global $DB;

        // Setup two users to merge.
        $user_remove = $this->getDataGenerator()->create_user();
        $user_keep = $this->getDataGenerator()->create_user();

        // Create three courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manual = enrol_get_plugin('manual');

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));

        // Enrol $user_remove on course 1 + 2 and $user_keep on course 2 + 3.
        $manual->enrol_user($maninstance1, $user_remove->id, $studentrole->id);
        $manual->enrol_user($maninstance2, $user_remove->id, $studentrole->id);
        $manual->enrol_user($maninstance2, $user_keep->id, $studentrole->id);
        $manual->enrol_user($maninstance3, $user_keep->id, $studentrole->id);

        // Check initial state of enrolments for $user_remove.
        $courses = enrol_get_all_users_courses($user_remove->id);
        ksort($courses);
        $this->assertCount(2, $courses);
        $this->assertEquals(array($course1->id, $course2->id), array_keys($courses));

        // Check initial state of enrolments for $user_keep.
        $courses = enrol_get_all_users_courses($user_keep->id);
        ksort($courses);
        $this->assertCount(2, $courses);
        $this->assertEquals(array($course2->id, $course3->id), array_keys($courses));

        $mut = new IomadMergeTool();
        list($success, $log, $logid) = $mut->merge($user_keep->id, $user_remove->id);

        // Check $user_remove is suspended.
        $user_remove = $DB->get_record('user', array('id' => $user_remove->id));
        $this->assertEquals(1, $user_remove->suspended);

        // Check $user_keep is now enrolled on all three courses.
        $courses = enrol_get_all_users_courses($user_keep->id);
        ksort($courses);
        $this->assertCount(3, $courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
    }
}
