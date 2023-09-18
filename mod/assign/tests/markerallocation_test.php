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

namespace mod_assign;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/accesslib.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Unit tests for (some of) mod/assign/markerallocaion_test.php.
 *
 * @package    mod_assign
 * @category   test
 * @copyright  2017 Andrés Melo <andres.torres@blackboard.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class markerallocation_test extends \advanced_testcase {

    /** @var \stdClass course record. */
    private $course;

    /**
     * Create all the needed elements to test the difference between both functions.
     */
    public function test_markerusers() {
        $this->resetAfterTest();
        global $DB;

        // Create a course, by default it is created with 5 sections.
        $this->course = $this->getDataGenerator()->create_course();

        // Setting assing module, markingworkflow and markingallocation set to 1 to enable marker allocation.
        $record = new \stdClass();
        $record->course = $this->course;

        $modulesettings = array(
            'alwaysshowdescription'             => 1,
            'submissiondrafts'                  => 1,
            'requiresubmissionstatement'        => 0,
            'sendnotifications'                 => 0,
            'sendstudentnotifications'          => 1,
            'sendlatenotifications'             => 0,
            'duedate'                           => 0,
            'allowsubmissionsfromdate'          => 0,
            'grade'                             => 100,
            'cutoffdate'                        => 0,
            'teamsubmission'                    => 0,
            'requireallteammemberssubmit'       => 0,
            'teamsubmissiongroupingid'          => 0,
            'blindmarking'                      => 0,
            'attemptreopenmethod'               => 'none',
            'maxattempts'                       => -1,
            'markingworkflow'                   => 1,
            'markingallocation'                 => 1,
        );

        $assignelement = $this->getDataGenerator()->create_module('assign', $record, $modulesettings);

        $coursesectionid = course_add_cm_to_section($this->course->id, $assignelement->id, 1);

        // Adding users to the course.
        $userdata = array();
        $userdata['firstname'] = 'teacher1';
        $userdata['lasttname'] = 'lastname_teacher1';

        $user1 = $this->getDataGenerator()->create_user($userdata);

        $this->getDataGenerator()->enrol_user($user1->id, $this->course->id, 'teacher');

        $userdata = array();
        $userdata['firstname'] = 'teacher2';
        $userdata['lasttname'] = 'lastname_teacher2';

        $user2 = $this->getDataGenerator()->create_user($userdata);

        $this->getDataGenerator()->enrol_user($user2->id, $this->course->id, 'teacher');

        $userdata = array();
        $userdata['firstname'] = 'student';
        $userdata['lasttname'] = 'lastname_student';

        $user3 = $this->getDataGenerator()->create_user($userdata);

        $this->getDataGenerator()->enrol_user($user3->id, $this->course->id, 'student');

        // Adding manager to the system.
        $userdata = array();
        $userdata['firstname'] = 'Manager';
        $userdata['lasttname'] = 'lastname_Manager';

        $user4 = $this->getDataGenerator()->create_user($userdata);

        // Getting id of manager role.
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));
        if (!empty($managerrole)) {
            // By default the context of the system is assigned.
            $idassignment = $this->getDataGenerator()->role_assign($managerrole->id, $user4->id);
        }

        $oldusers = array($user1, $user2, $user4);
        $newusers = array($user1, $user2);

        list($sort, $params) = users_order_by_sql('u');

        // Old code, it must return 3 users: teacher1, teacher2 and Manger.
        $oldmarkers = get_users_by_capability(\context_course::instance($this->course->id), 'mod/assign:grade', '', $sort);
        // New code, it must return 2 users: teacher1 and teacher2.
        $newmarkers = get_enrolled_users(\context_course::instance($this->course->id), 'mod/assign:grade', 0, 'u.*', $sort);

        // Test result quantity.
        $this->assertEquals(count($oldusers), count($oldmarkers));
        $this->assertEquals(count($newusers), count($newmarkers));
        $this->assertEquals(count($oldmarkers) > count($newmarkers), true);

        // Elements expected with new code.
        foreach ($newmarkers as $key => $nm) {
            $this->assertEquals($nm, $newusers[array_search($nm, $newusers)]);
        }

        // Elements expected with old code.
        foreach ($oldusers as $key => $os) {
            $this->assertEquals($os->id, $oldmarkers[$os->id]->id);
            unset($oldmarkers[$os->id]);
        }

        $this->assertEquals(count($oldmarkers), 0);

    }
}
