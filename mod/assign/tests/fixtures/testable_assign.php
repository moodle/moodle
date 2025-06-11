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
 * The testable assign class.
 *
 * @package   mod_assign
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class mod_assign_testable_assign extends assign {

    public function testable_show_intro() {
        return parent::show_intro();
    }

    public function testable_delete_grades() {
        return parent::delete_grades();
    }

    public function testable_apply_grade_to_user($formdata, $userid, $attemptnumber) {
        return parent::apply_grade_to_user($formdata, $userid, $attemptnumber);
    }

    public function testable_get_grading_userid_list() {
        return parent::get_grading_userid_list();
    }

    public function testable_is_graded($userid) {
        return parent::is_graded($userid);
    }

    public function testable_update_submission(stdClass $submission, $userid, $updatetime, $teamsubmission) {
        return parent::update_submission($submission, $userid, $updatetime, $teamsubmission);
    }

    public function testable_process_add_attempt($userid = 0) {
        return parent::process_add_attempt($userid);
    }

    public function testable_process_save_quick_grades($postdata) {
        // Ugly hack to get something into the method.
        global $_POST;
        $_POST = $postdata;
        return parent::process_save_quick_grades();
    }

    public function testable_process_set_batch_marking_allocation($selectedusers, $markerid) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/batchsetallocatedmarkerform.php');

        // Simulate the form submission.
        $data = array();
        $data['id'] = $this->get_course_module()->id;
        $data['selectedusers'] = $selectedusers;
        $data['allocatedmarker'] = $markerid;
        $data['action'] = 'setbatchmarkingallocation';
        mod_assign_batch_set_allocatedmarker_form::mock_submit($data);

        return parent::process_set_batch_marking_allocation();
    }

    public function testable_process_set_batch_marking_workflow_state($selectedusers, $state) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/batchsetmarkingworkflowstateform.php');

        // Simulate the form submission.
        $data = array();
        $data['id'] = $this->get_course_module()->id;
        $data['selectedusers'] = $selectedusers;
        $data['markingworkflowstate'] = $state;
        $data['action'] = 'setbatchmarkingworkflowstate';
        mod_assign_batch_set_marking_workflow_state_form::mock_submit($data);

        return parent::process_set_batch_marking_workflow_state();
    }

    public function testable_submissions_open($userid = 0) {
        return parent::submissions_open($userid);
    }

    public function testable_save_user_extension($userid, $extensionduedate) {
        return parent::save_user_extension($userid, $extensionduedate);
    }

    public function testable_get_graders($userid) {
        // Changed method from protected to public.
        return parent::get_graders($userid);
    }

    public function testable_get_notifiable_users($userid) {
        return parent::get_notifiable_users($userid);
    }

    public function testable_view_batch_set_workflow_state($selectedusers) {
        global $PAGE;
        $PAGE->set_url('/mod/assign/view.php');
        $_POST['selectedusers'] = $selectedusers;
        return parent::view_batch_set_workflow_state();
    }

    public function testable_view_batch_markingallocation($selectedusers) {
        global $PAGE;
        $PAGE->set_url('/mod/assign/view.php');
        $_POST['selectedusers'] = $selectedusers;
        return parent::view_batch_markingallocation();
    }

    public function testable_update_activity_completion_records($teamsubmission,
                                                          $requireallteammemberssubmit,
                                                          $submission,
                                                          $userid,
                                                          $complete,
                                                          $completion) {
        return parent::update_activity_completion_records($teamsubmission,
                                                          $requireallteammemberssubmit,
                                                          $submission,
                                                          $userid,
                                                          $complete,
                                                          $completion);
    }
}
