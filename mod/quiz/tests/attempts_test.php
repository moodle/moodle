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
 * Quiz attempt overdue handling tests
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/group/lib.php');

/**
 * Unit tests for quiz attempt overdue handling
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_attempt_overdue_testcase extends advanced_testcase {

    /**
     * Test the functions quiz_update_open_attempts(), get_list_of_overdue_attempts() and
     * update_overdue_attempts().
     */
    public function test_bulk_update_functions() {
        global $DB,$CFG;

        require_once($CFG->dirroot.'/mod/quiz/cronlib.php');

        $this->resetAfterTest();

        $this->setAdminUser();

        // Setup course, user and groups

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $this->assertTrue(enrol_try_internal_enrol($course->id, $user1->id, $studentrole->id));
        $group1 = $this->getDataGenerator()->create_group(array('courseid'=>$course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid'=>$course->id));
        $group3 = $this->getDataGenerator()->create_group(array('courseid'=>$course->id));
        $this->assertTrue(groups_add_member($group1, $user1));
        $this->assertTrue(groups_add_member($group2, $user1));

        $usertimes = array();

        /** @var mod_quiz_generator $quiz_generator */
        $quiz_generator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        // Basic quiz settings

        $quiz = $quiz_generator->create_instance(array('course'=> $course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>600, 'message'=>'Test1A', 'time1000state' => 'finished');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>1800));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>1800, 'message'=>'Test1B', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>0));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>0, 'message'=>'Test1C', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>0, 'timelimit'=>600));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>600, 'message'=>'Test1D', 'time1000state' => 'finished');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>0, 'timelimit'=>0));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>0, 'message'=>'Test1E', 'time1000state' => 'inprogress');

        // Group overrides

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>0));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>null));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>0, 'message'=>'Test2A', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>0));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1100, 'timelimit'=>null));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1100, 'timelimit'=>0, 'message'=>'Test2B', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>0, 'timelimit'=>600, 'overduehandling' => 'autoabandon'));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>null, 'timelimit'=>700));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>700, 'message'=>'Test2C', 'time1000state' => 'abandoned');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>0, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>null, 'timelimit'=>500));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>500, 'message'=>'Test2D', 'time1000state' => 'finished');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>0, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>null, 'timelimit'=>0));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>0, '', 'message'=>'Test2E', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>500));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>500, '', 'message'=>'Test2F', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>500, '', 'message'=>'Test2G', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group3->id, 'timeclose'=>1300, 'timelimit'=>500)); // user not in group
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>600, '', 'message'=>'Test2H', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>600, '', 'message'=>'Test2I', 'time1000state' => 'inprogress');

        // Multiple group overrides

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>501));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>1301, 'timelimit'=>500));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>501, '', 'message'=>'Test3A', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>501, '', 'message'=>'Test3B', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1301, 'timelimit'=>500));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>1300, 'timelimit'=>501));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>501, '', 'message'=>'Test3C', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>501, '', 'message'=>'Test3D', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600, 'overduehandling' => 'autoabandon'));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1301, 'timelimit'=>500));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>1300, 'timelimit'=>501));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group3->id, 'timeclose'=>1500, 'timelimit'=>1000)); // user not in group
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>501, '', 'message'=>'Test3E', 'time1000state' => 'abandoned');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>501, '', 'message'=>'Test3F', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>500));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>null, 'timelimit'=>501));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>501, '', 'message'=>'Test3G', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>501, '', 'message'=>'Test3H', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>500));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>1301, 'timelimit'=>null));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>500, '', 'message'=>'Test3I', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>500, '', 'message'=>'Test3J', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>500));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>1301, 'timelimit'=>0));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>0, '', 'message'=>'Test3K', 'time1000state' => 'inprogress');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1301, 'timelimit'=>0, '', 'message'=>'Test3L', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>500));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>0, 'timelimit'=>501));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>501, '', 'message'=>'Test3M', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>501, '', 'message'=>'Test3N', 'time1000state' => 'inprogress');

        // User overrides

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>700));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'timeclose'=>1201, 'timelimit'=>601));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>601, '', 'message'=>'Test4A', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>601, '', 'message'=>'Test4B', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>700));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'timeclose'=>0, 'timelimit'=>601));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>601, '', 'message'=>'Test4C', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>601, '', 'message'=>'Test4D', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>700));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'timeclose'=>1201, 'timelimit'=>0));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>0, '', 'message'=>'Test4E', 'time1000state' => 'inprogress');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>0, '', 'message'=>'Test4F', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600, 'overduehandling' => 'autoabandon'));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>700));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'timeclose'=>null, 'timelimit'=>601));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>601, '', 'message'=>'Test4G', 'time1000state' => 'abandoned');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>601, '', 'message'=>'Test4H', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>null, 'timelimit'=>700));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'timeclose'=>null, 'timelimit'=>601));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>601, '', 'message'=>'Test4I', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>601, '', 'message'=>'Test4J', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>700));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'timeclose'=>1201, 'timelimit'=>null));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>700, '', 'message'=>'Test4K', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>700, '', 'message'=>'Test4L', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>null));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'timeclose'=>1201, 'timelimit'=>null));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>600, '', 'message'=>'Test4M', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1201, 'timelimit'=>600, '', 'message'=>'Test4N', 'time1000state' => 'inprogress');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>700));
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'userid'=>0, 'timeclose'=>1201, 'timelimit'=>601)); // not user
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>700, '', 'message'=>'Test4O', 'time1000state' => 'finished');
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>1000, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz), 'attempt'=>1));
        $usertimes[$attemptid] = array('timeclose'=>1300, 'timelimit'=>700, '', 'message'=>'Test4P', 'time1000state' => 'inprogress');

        // Attempt state overdue

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>600, 'overduehandling'=>'graceperiod', 'graceperiod'=>250));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'overdue', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>1200, 'timelimit'=>600, '', 'message'=>'Test5A', 'time1000state' => 'overdue');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>0, 'timelimit'=>600, 'overduehandling'=>'graceperiod', 'graceperiod'=>250));
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'overdue', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));
        $usertimes[$attemptid] = array('timeclose'=>0, 'timelimit'=>600, '', 'message'=>'Test5B', 'time1000state' => 'overdue');

        // Compute expected end time for each attempt.
        foreach ($usertimes as $attemptid => $times) {
            $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid], '*', MUST_EXIST);

            if ($times['timeclose'] > 0 && $times['timelimit'] > 0) {
                $usertimes[$attemptid]['timedue'] = min($times['timeclose'], $attempt->timestart + $times['timelimit']);
            } else if ($times['timeclose'] > 0) {
                $usertimes[$attemptid]['timedue'] = $times['timeclose'];
            } else if ($times['timelimit'] > 0) {
                $usertimes[$attemptid]['timedue'] = $attempt->timestart + $times['timelimit'];
            }
        }

        //
        // Test quiz_update_open_attempts()
        //

        quiz_update_open_attempts(array('courseid'=>$course->id));
        foreach ($usertimes as $attemptid=>$times) {
            $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid], '*', MUST_EXIST);

            if ($attempt->state == 'overdue') {
                $graceperiod = $DB->get_field('quiz', 'graceperiod', array('id'=>$attempt->quiz));
            } else {
                $graceperiod = 0;
            }
            if (isset($times['timedue'])) {
                $this->assertEquals($times['timedue'] + $graceperiod, $attempt->timecheckstate, $times['message']);
            } else {
                $this->assertNull($attempt->timecheckstate, $times['message']);
            }
        }

        //
        // Test get_list_of_overdue_attempts()
        //

        $overduehander = new mod_quiz_overdue_attempt_updater();

        $attempts = $overduehander->get_list_of_overdue_attempts(100000); // way in the future
        $count = 0;
        foreach ($attempts as $attempt) {
            $this->assertTrue(isset($usertimes[$attempt->id]));
            $times = $usertimes[$attempt->id];
            $this->assertEquals($times['timeclose'], $attempt->usertimeclose, $times['message']);
            $this->assertEquals($times['timelimit'], $attempt->usertimelimit, $times['message']);
            $count++;

        }
        $attempts->close();
        $this->assertEquals($DB->count_records_select('quiz_attempts', 'timecheckstate IS NOT NULL'), $count);

        $attempts = $overduehander->get_list_of_overdue_attempts(0); // before all attempts
        $count = 0;
        foreach ($attempts as $attempt) {
            $count++;
        }
        $attempts->close();
        $this->assertEquals(0, $count);

        //
        // Test update_overdue_attempts()
        //

        [$count, $quizcount] = $overduehander->update_overdue_attempts(1000, 940);

        $attempts = $DB->get_records('quiz_attempts', null, 'quiz, userid, attempt',
                'id, quiz, userid, attempt, state, timestart, timefinish, timecheckstate');
        foreach ($attempts as $attempt) {
            $this->assertTrue(isset($usertimes[$attempt->id]));
            $times = $usertimes[$attempt->id];
            $this->assertEquals($times['time1000state'], $attempt->state, $times['message']);
            switch ($times['time1000state']) {
                case 'finished':
                    $this->assertEquals($times['timedue'], $attempt->timefinish, $times['message']);
                    $this->assertNull($attempt->timecheckstate, $times['message']);
                    break;

                case 'overdue':
                    $this->assertEquals(0, $attempt->timefinish, $times['message']);
                    $graceperiod = $DB->get_field('quiz', 'graceperiod', array('id'=>$attempt->quiz));
                    $this->assertEquals($times['timedue'] + $graceperiod, $attempt->timecheckstate, $times['message']);
                    break;

                case 'abandoned':
                    $this->assertEquals(0, $attempt->timefinish, $times['message']);
                    $this->assertNull($attempt->timecheckstate, $times['message']);
                    break;
            }
        }

        $this->assertEquals(19, $count);
        $this->assertEquals(19, $quizcount);
    }

    /**
     * Make any old question usage for a quiz.
     *
     * The attempts used in test_bulk_update_functions must have some
     * question usage to store in uniqueid, but they don't have to be
     * very realistic.
     *
     * @param stdClass $quiz
     * @return int question usage id.
     */
    protected function make_usage($quiz) {
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz',
                context_module::instance($quiz->cmid));
        $quba->set_preferred_behaviour('deferredfeedback');
        question_engine::save_questions_usage_by_activity($quba);
        return $quba->get_id();
    }

    /**
     * Test the group event handlers
     */
    public function test_group_event_handlers() {
        global $DB,$CFG;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Setup course, user and groups

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $this->assertTrue(enrol_try_internal_enrol($course->id, $user1->id, $studentrole->id));
        $group1 = $this->getDataGenerator()->create_group(array('courseid'=>$course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid'=>$course->id));
        $this->assertTrue(groups_add_member($group1, $user1));
        $this->assertTrue(groups_add_member($group2, $user1));

        /** @var mod_quiz_generator $quiz_generator */
        $quiz_generator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quiz_generator->create_instance(array('course'=>$course->id, 'timeclose'=>1200, 'timelimit'=>0));

        // add a group1 override
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group1->id, 'timeclose'=>1300, 'timelimit'=>null));

        // add an attempt
        $attemptid = $DB->insert_record('quiz_attempts', array('quiz'=>$quiz->id, 'userid'=>$user1->id, 'state'=>'inprogress', 'timestart'=>100, 'timecheckstate'=>0, 'layout'=>'', 'uniqueid' => $this->make_usage($quiz)));

        // update timecheckstate
        quiz_update_open_attempts(array('quizid'=>$quiz->id));
        $this->assertEquals(1300, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));

        // remove from group
        $this->assertTrue(groups_remove_member($group1, $user1));
        $this->assertEquals(1200, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));

        // add back to group
        $this->assertTrue(groups_add_member($group1, $user1));
        $this->assertEquals(1300, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));

        // delete group
        groups_delete_group($group1);
        $this->assertEquals(1200, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));
        $this->assertEquals(0, $DB->count_records('quiz_overrides', array('quiz'=>$quiz->id)));

        // add a group2 override
        $DB->insert_record('quiz_overrides', array('quiz'=>$quiz->id, 'groupid'=>$group2->id, 'timeclose'=>1400, 'timelimit'=>null));
        quiz_update_open_attempts(array('quizid'=>$quiz->id));
        $this->assertEquals(1400, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));

        // delete user1 from all groups
        groups_delete_group_members($course->id, $user1->id);
        $this->assertEquals(1200, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));

        // add back to group2
        $this->assertTrue(groups_add_member($group2, $user1));
        $this->assertEquals(1400, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));

        // delete everyone from all groups
        groups_delete_group_members($course->id);
        $this->assertEquals(1200, $DB->get_field('quiz_attempts', 'timecheckstate', array('id'=>$attemptid)));
    }

    /**
     * Test the functions quiz_create_attempt_handling_errors
     */
    public function test_quiz_create_attempt_handling_errors() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Make a quiz.
        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $quiz = $quizgenerator->create_instance(array('course' => $course->id, 'questionsperpage' => 0, 'grade' => 100.0,
            'sumgrades' => 2));
        // Create questions.
        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $numq = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);
        $quizobj = quiz::create($quiz->id, $user1->id);
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        // Create an attempt.
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user1->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $result = quiz_create_attempt_handling_errors($attempt->id, $quiz->cmid);
        $this->assertEquals($result->get_attemptid(), $attempt->id);
        try {
            $result = quiz_create_attempt_handling_errors($attempt->id, 9999);
            $this->fail('Exception expected due to invalid course module id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidcoursemodule', $e->errorcode);
        }
        try {
            quiz_create_attempt_handling_errors(9999, $result->get_cmid());
            $this->fail('Exception expected due to quiz content change.');
        } catch (moodle_exception $e) {
            $this->assertEquals('attempterrorcontentchange', $e->errorcode);
        }
        try {
            quiz_create_attempt_handling_errors(9999);
            $this->fail('Exception expected due to invalid quiz attempt id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('attempterrorinvalid', $e->errorcode);
        }
        // Set up as normal user without permission to view preview.
        $this->setUser($student->id);
        try {
            quiz_create_attempt_handling_errors(9999, $result->get_cmid());
            $this->fail('Exception expected due to quiz content change for user without permission.');
        } catch (moodle_exception $e) {
            $this->assertEquals('attempterrorcontentchangeforuser', $e->errorcode);
        }
        try {
            quiz_create_attempt_handling_errors($attempt->id, 9999);
            $this->fail('Exception expected due to invalid course module id for user without permission.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidcoursemodule', $e->errorcode);
        }
    }
}
