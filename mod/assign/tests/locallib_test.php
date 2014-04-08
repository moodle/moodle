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
 * Unit tests for (some of) mod/assign/locallib.php.
 *
 * @package    mod_assign
 * @category   phpunit
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/upgradelib.php');
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Unit tests for (some of) mod/assign/locallib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_locallib_testcase extends mod_assign_base_testcase {

    public function test_return_links() {
        global $PAGE;
        $this->setUser($this->editingteachers[0]);
        $returnaction = 'RETURNACTION';
        $returnparams = array('param'=>'1');
        $assign = $this->create_instance();
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));
        $assign->register_return_link($returnaction, $returnparams);
        $this->assertEquals($returnaction, $assign->get_return_action());
        $this->assertEquals($returnparams, $assign->get_return_params());
    }

    public function test_get_feedback_plugins() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();
        $installedplugins = array_keys(core_component::get_plugin_list('assignfeedback'));

        foreach ($assign->get_feedback_plugins() as $plugin) {
            $this->assertContains($plugin->get_type(), $installedplugins, 'Feedback plugin not in list of installed plugins');
        }
    }

    public function test_get_submission_plugins() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();
        $installedplugins = array_keys(core_component::get_plugin_list('assignsubmission'));

        foreach ($assign->get_submission_plugins() as $plugin) {
            $this->assertContains($plugin->get_type(), $installedplugins, 'Submission plugin not in list of installed plugins');
        }
    }

    public function test_is_blind_marking() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('blindmarking'=>1));
        $this->assertEquals(true, $assign->is_blind_marking());

        // Test cannot see student names.
        $gradingtable = new assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, get_string('hiddenuser', 'assign')));

        // Test students cannot reveal identities.
        $nopermission = false;
        $this->students[0]->ignoresesskey = true;
        $this->setUser($this->students[0]);
        $this->setExpectedException('required_capability_exception');
        $assign->reveal_identities();
        $this->students[0]->ignoresesskey = false;

        // Test teachers cannot reveal identities.
        $nopermission = false;
        $this->teachers[0]->ignoresesskey = true;
        $this->setUser($this->teachers[0]);
        $this->setExpectedException('required_capability_exception');
        $assign->reveal_identities();
        $this->teachers[0]->ignoresesskey = false;

        // Test sesskey is required.
        $this->setUser($this->editingteachers[0]);
        $this->setExpectedException('moodle_exception');
        $assign->reveal_identities();

        // Test editingteacher can reveal identities if sesskey is ignored.
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);
        $assign->reveal_identities();
        $this->assertEquals(false, $assign->is_blind_marking());
        $this->editingteachers[0]->ignoresesskey = false;

        // Test student names are visible.
        $gradingtable = new assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(false, strpos($output, get_string('hiddenuser', 'assign')));

        // Set this back to default.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_show_intro() {
        // Test whether we are showing the intro at the correct times.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('alwaysshowdescription'=>1));

        $this->assertEquals(true, $assign->testable_show_intro());

        $tomorrow = time() + (24*60*60);

        $assign = $this->create_instance(array('alwaysshowdescription'=>0,
                                               'allowsubmissionsfromdate'=>$tomorrow));
        $this->assertEquals(false, $assign->testable_show_intro());
        $yesterday = time() - (24*60*60);
        $assign = $this->create_instance(array('alwaysshowdescription'=>0,
                                               'allowsubmissionsfromdate'=>$yesterday));
        $this->assertEquals(true, $assign->testable_show_intro());
    }

    public function test_has_submissions_or_grades() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        $instance = $assign->get_instance();

        // Should start empty.
        $this->assertEquals(false, $assign->has_submissions_or_grades());

        // Simulate a submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Now test again.
        $this->assertEquals(true, $assign->has_submissions_or_grades());
        // Set this back to default.
        $this->students[0]->ignoresesskey = false;
    }

    public function test_delete_grades() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Now see if the data is in the gradebook.
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id);

        $this->assertNotEquals(0, count($gradinginfo->items));

        $assign->testable_delete_grades();
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id);

        $this->assertEquals(0, count($gradinginfo->items));
    }

    public function test_delete_instance() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Simulate a submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Now try and delete.
        $this->assertEquals(true, $assign->delete_instance());
    }

    public function test_reset_userdata() {
        global $DB;

        $now = time();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1,
                                               'duedate'=>$now));

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Simulate a submission.
        $this->setUser($this->students[0]);
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        $this->assertEquals(true, $assign->has_submissions_or_grades());
        // Now try and reset.
        $data = new stdClass();
        $data->reset_assign_submissions = 1;
        $data->reset_gradebook_grades = 1;
        $data->courseid = $this->course->id;
        $data->timeshift = 24*60*60;
        $this->setUser($this->editingteachers[0]);
        $assign->reset_userdata($data);
        $this->assertEquals(false, $assign->has_submissions_or_grades());

        // Reload the instance data.
        $instance = $DB->get_record('assign', array('id'=>$assign->get_instance()->id));
        $this->assertEquals($now + 24*60*60, $instance->duedate);

        // Test reset using assign_reset_userdata().
        $assignduedate = $instance->duedate; // Keep old updated value for comparison.
        $data->timeshift = 2*24*60*60;
        assign_reset_userdata($data);
        $instance = $DB->get_record('assign', array('id' => $assign->get_instance()->id));
        $this->assertEquals($assignduedate + 2*24*60*60, $instance->duedate);

        // Create one more assignment and reset, make sure time shifted for previous assignment is not changed.
        $assign2 = $this->create_instance(array('assignsubmission_onlinetext_enabled' => 1,
                                               'duedate' => $now));
        $assignduedate = $instance->duedate;
        $data->timeshift = 3*24*60*60;
        $assign2->reset_userdata($data);
        $instance = $DB->get_record('assign', array('id' => $assign->get_instance()->id));
        $this->assertEquals($assignduedate, $instance->duedate);
        $instance2 = $DB->get_record('assign', array('id' => $assign2->get_instance()->id));
        $this->assertEquals($now + 3*24*60*60, $instance2->duedate);

        // Reset both assignments using assign_reset_userdata() and make sure both assignments have same date.
        $assignduedate = $instance->duedate;
        $assign2duedate = $instance2->duedate;
        $data->timeshift = 4*24*60*60;
        assign_reset_userdata($data);
        $instance = $DB->get_record('assign', array('id' => $assign->get_instance()->id));
        $this->assertEquals($assignduedate + 4*24*60*60, $instance->duedate);
        $instance2 = $DB->get_record('assign', array('id' => $assign2->get_instance()->id));
        $this->assertEquals($assign2duedate + 4*24*60*60, $instance2->duedate);
    }

    public function test_plugin_settings() {
        global $DB;

        $now = time();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_file_enabled'=>1,
                                               'assignsubmission_file_maxfiles'=>12,
                                               'assignsubmission_file_maxsizebytes'=>10));

        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertEquals('12', $plugin->get_config('maxfilesubmissions'));
    }

    public function test_update_calendar() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $userctx = context_user::instance($this->editingteachers[0]->id)->id;

        // Hack to pretend that there was an editor involved. We need both $_POST and $_REQUEST, and a sesskey.
        $draftid = file_get_unused_draft_itemid();
        $_REQUEST['introeditor'] = $draftid;
        $_POST['introeditor'] = $draftid;
        $_POST['sesskey'] = sesskey();

        // Write links to a draft area.
        $fakearealink1 = file_rewrite_pluginfile_urls('<a href="@@PLUGINFILE@@/pic.gif">link</a>', 'draftfile.php', $userctx,
            'user', 'draft', $draftid);
        $fakearealink2 = file_rewrite_pluginfile_urls('<a href="@@PLUGINFILE@@/pic.gif">new</a>', 'draftfile.php', $userctx,
            'user', 'draft', $draftid);

        // Create a new assignment with links to a draft area.
        $now = time();
        $assign = $this->create_instance(array(
            'duedate' => $now,
            'intro' => $fakearealink1,
            'introformat' => FORMAT_HTML
        ));

        // See if there is an event in the calendar.
        $params = array('modulename'=>'assign', 'instance'=>$assign->get_instance()->id);
        $event = $DB->get_record('event', $params);
        $this->assertNotEmpty($event);
        $this->assertSame('link', $event->description);     // The pluginfile links are removed.

        // Make sure the same works when updating the assignment.
        $instance = $assign->get_instance();
        $instance->instance = $instance->id;
        $instance->intro = $fakearealink2;
        $instance->introformat = FORMAT_HTML;
        $assign->update_instance($instance);
        $params = array('modulename' => 'assign', 'instance' => $assign->get_instance()->id);
        $event = $DB->get_record('event', $params);
        $this->assertNotEmpty($event);
        $this->assertSame('new', $event->description);     // The pluginfile links are removed.
    }

    public function test_update_instance() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        $now = time();
        $instance = $assign->get_instance();
        $instance->duedate = $now;
        $instance->instance = $instance->id;
        $instance->assignsubmission_onlinetext_enabled = 1;

        $assign->update_instance($instance);

        $instance = $DB->get_record('assign', array('id'=>$assign->get_instance()->id));
        $this->assertEquals($now, $instance->duedate);
    }

    public function test_cannot_submit_empty() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('submissiondrafts'=>1));

        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // Test you cannot see the submit button for an offline assignment regardless.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotContains(get_string('submitassignment', 'assign'), $output, 'Can submit empty offline assignment');

        // Test you cannot see the submit button for an online text assignment with no submission.
        $this->setUser($this->editingteachers[0]);
        $instance = $assign->get_instance();
        $instance->instance = $instance->id;
        $instance->assignsubmission_onlinetext_enabled = 1;

        $assign->update_instance($instance);
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotContains(get_string('submitassignment', 'assign'), $output, 'Cannot submit empty onlinetext assignment');

        // Simulate a submission.
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);
        // Test you can see the submit button for an online text assignment with a submission.
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertContains(get_string('submitassignment', 'assign'), $output, 'Can submit non empty onlinetext assignment');
    }

    public function test_list_participants() {
        $this->create_extra_users();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('grade'=>100));

        $this->assertEquals(self::DEFAULT_STUDENT_COUNT + self::EXTRA_STUDENT_COUNT, count($assign->list_participants(null, true)));

        // Teacher with user preference set should see suspended users as well.
        set_user_preference('grade_report_showonlyactiveenrol', false);
        $assign = $this->create_instance(array('grade'=>100));
        $this->assertEquals(self::DEFAULT_STUDENT_COUNT + self::EXTRA_STUDENT_COUNT + self::EXTRA_SUSPENDED_COUNT,
                count($assign->list_participants(null, true)));

        // Non-editing teacher should not see suspended users, even if user preference is set.
        $this->setUser($this->teachers[0]);
        set_user_preference('grade_report_showonlyactiveenrol', false);
        $assign = $this->create_instance(array('grade'=>100));
        $this->assertEquals(self::DEFAULT_STUDENT_COUNT + self::EXTRA_STUDENT_COUNT, count($assign->list_participants(null, true)));
    }

    public function test_count_teams() {
        $this->create_extra_users();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('teamsubmission'=>1));

        $this->assertEquals(self::GROUP_COUNT + 1, $assign->count_teams());
    }

    public function test_count_submissions() {
        $this->create_extra_users();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1));

        // Simulate a submission.
        $this->setUser($this->extrastudents[0]);
        $submission = $assign->get_user_submission($this->extrastudents[0]->id, true);
        // Leave this one as DRAFT.
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->extrastudents[0]->id, 0);

        // Simulate a submission.
        $this->setUser($this->extrastudents[1]);
        $submission = $assign->get_user_submission($this->extrastudents[1]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[1]->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate a submission.
        $this->setUser($this->extrastudents[2]);
        $submission = $assign->get_user_submission($this->extrastudents[2]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[2]->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate a submission.
        $this->setUser($this->extrastudents[3]);
        $submission = $assign->get_user_submission($this->extrastudents[3]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[3]->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate a submission for suspended user, this will never be counted.
        $this->setUser($this->extrastudents[3]);
        $submission = $assign->get_user_submission($this->extrasuspendedstudents[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrasuspendedstudents[0]->id, true, false);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->extrastudents[3]->id, 0);
        $assign->testable_apply_grade_to_user($data, $this->extrasuspendedstudents[0]->id, 0);

        $this->assertEquals(2, $assign->count_grades());
        $this->assertEquals(4, $assign->count_submissions());
        $this->assertEquals(2, $assign->count_submissions_need_grading());
        $this->assertEquals(3, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT));
    }

    public function test_get_grading_userid_list() {
        $this->create_extra_users();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $users = $assign->testable_get_grading_userid_list();
        $this->assertEquals(self::DEFAULT_STUDENT_COUNT + self::EXTRA_STUDENT_COUNT, count($users));

        $this->setUser($this->editingteachers[0]);
        set_user_preference('grade_report_showonlyactiveenrol', false);
        $assign = $this->create_instance();

        $users = $assign->testable_get_grading_userid_list();
        $this->assertEquals(self::DEFAULT_STUDENT_COUNT + self::EXTRA_STUDENT_COUNT + self::EXTRA_SUSPENDED_COUNT, count($users));
    }

    public function test_cron() {
        // First run cron so there are no messages waiting to be sent (from other tests).
        cron_setup_user();
        assign::cron();

        // Now create an assignment and add some feedback.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('sendstudentnotifications'=>1));

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);
        $assign->testable_apply_grade_to_user($data, $this->students[1]->id, 0);

        $data->sendstudentnotifications = false;
        $assign->testable_apply_grade_to_user($data, $this->students[2]->id, 0);

        // Now run cron and see that one message was sent.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();
        cron_setup_user();
        $this->expectOutputRegex('/Done processing 2 assignment submissions/');
        assign::cron();

        $messages = $sink->get_messages();
        // The sent count should be 2, because the 3rd one was marked as do not send notifications.
        $this->assertEquals(2, count($messages));
        $this->assertEquals(1, $messages[0]->notification);
        $this->assertEquals($assign->get_instance()->name, $messages[0]->contexturlname);
    }

    public function test_is_graded() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        $this->assertEquals(true, $assign->testable_is_graded($this->students[0]->id));
        $this->assertEquals(false, $assign->testable_is_graded($this->students[1]->id));
    }

    public function test_can_grade() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->setUser($this->students[0]);
        $this->assertEquals(false, $assign->can_grade());
        $this->setUser($this->editingteachers[0]);
        $this->assertEquals(true, $assign->can_grade());
        $this->setUser($this->teachers[0]);
        $this->assertEquals(true, $assign->can_grade());

        // Test the viewgrades capability - without mod/assign:grade.
        $this->setUser($this->students[0]);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        assign_capability('mod/assign:viewgrades', CAP_ALLOW, $studentrole->id, $assign->get_context()->id);
        $this->assertEquals(false, $assign->can_grade());
    }

    public function test_can_view_submission() {
        global $DB;

        $this->create_extra_users();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->setUser($this->students[0]);
        $this->assertEquals(true, $assign->can_view_submission($this->students[0]->id));
        $this->assertEquals(false, $assign->can_view_submission($this->students[1]->id));
        $this->assertEquals(false, $assign->can_view_submission($this->teachers[0]->id));
        $this->setUser($this->teachers[0]);
        $this->assertEquals(true, $assign->can_view_submission($this->students[0]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->students[1]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->teachers[0]->id));
        $this->assertEquals(false, $assign->can_view_submission($this->extrasuspendedstudents[0]->id));
        $this->setUser($this->editingteachers[0]);
        $this->assertEquals(true, $assign->can_view_submission($this->students[0]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->students[1]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->teachers[0]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->extrasuspendedstudents[0]->id));

        // Test the viewgrades capability - without mod/assign:grade.
        $this->setUser($this->students[0]);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        assign_capability('mod/assign:viewgrades', CAP_ALLOW, $studentrole->id, $assign->get_context()->id);
        $this->assertEquals(true, $assign->can_view_submission($this->students[0]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->students[1]->id));
        $this->assertEquals(true, $assign->can_view_submission($this->teachers[0]->id));
        $this->assertEquals(false, $assign->can_view_submission($this->extrasuspendedstudents[0]->id));
    }


    public function test_update_submission() {
        $this->create_extra_users();
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->setUser($this->extrastudents[0]);
        $now = time();
        $submission = $assign->get_user_submission($this->extrastudents[0]->id, true);
        $assign->testable_update_submission($submission, $this->extrastudents[0]->id, true, false);

        $this->setUser($this->teachers[0]);
        // Verify the gradebook update.
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[0]->id);

        $this->assertEquals($this->extrastudents[0]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[0]->id]->usermodified);

        // Now verify group assignments.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('teamsubmission'=>1));

        $this->setUser($this->extrastudents[0]);
        $now = time();
        $submission = $assign->get_group_submission($this->extrastudents[0]->id, 0, true);
        $assign->testable_update_submission($submission, $this->extrastudents[0]->id, true, true);

        // Check that at least 2 active members and 1 suspended member of the submission group had their submission updated.

        $this->setUser($this->editingteachers[0]);
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[0]->id);

        $this->assertEquals($this->extrastudents[0]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[0]->id]->usermodified);

        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[self::GROUP_COUNT]->id);

        $this->assertEquals($this->extrastudents[self::GROUP_COUNT]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[self::GROUP_COUNT]->id]->usermodified);

        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrasuspendedstudents[0]->id);
        $this->assertEquals($this->extrasuspendedstudents[0]->id,
                            $gradinginfo->items[0]->grades[$this->extrasuspendedstudents[0]->id]->usermodified);

        // Check the same with non-editing teacher and make sure submission is not updated for suspended user.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('teamsubmission'=>1));

        $this->setUser($this->extrastudents[1]);
        $now = time();
        $submission = $assign->get_group_submission($this->extrastudents[1]->id, 0, true);
        $assign->testable_update_submission($submission, $this->extrastudents[1]->id, true, true);

        $this->setUser($this->teachers[0]);
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[1]->id);

        $this->assertEquals($this->extrastudents[1]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[1]->id]->usermodified);

        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[self::GROUP_COUNT+1]->id);

        $this->assertEquals($this->extrastudents[self::GROUP_COUNT+1]->id,
                            $gradinginfo->items[0]->grades[$this->extrastudents[self::GROUP_COUNT+1]->id]->usermodified);

        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrasuspendedstudents[1]->id);
        $this->assertEquals($this->extrasuspendedstudents[1]->id,
                            $gradinginfo->items[0]->grades[$this->extrasuspendedstudents[1]->id]->usermodified);

        // Now verify blind marking.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('blindmarking'=>1));

        $this->setUser($this->extrastudents[0]);
        $now = time();
        $submission = $assign->get_user_submission($this->extrastudents[0]->id, true);
        $assign->testable_update_submission($submission, $this->extrastudents[0]->id, true, false);

        $this->setUser($this->editingteachers[0]);
        $gradinginfo = grade_get_grades($this->course->id,
                                        'mod',
                                        'assign',
                                        $assign->get_instance()->id,
                                        $this->extrastudents[0]->id);

        $this->assertEquals(null, $gradinginfo->items[0]->grades[$this->extrastudents[0]->id]->datesubmitted);
    }

    public function test_group_submissions_submit_for_marking() {
        global $PAGE;

        $this->create_extra_users();
        // Now verify group assignments.
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('teamsubmission'=>1,
                                               'assignsubmission_onlinetext_enabled'=>1,
                                               'submissiondrafts'=>1,
                                               'requireallteammemberssubmit'=>1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        $this->setUser($this->extrastudents[0]);
        // Add a submission.
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);

        $notices = array();
        $assign->save_submission($data, $notices);

        // Check we can see the submit button.
        $output = $assign->view_student_summary($this->extrastudents[0], true);
        $this->assertContains(get_string('submitassignment', 'assign'), $output);

        $submission = $assign->get_group_submission($this->extrastudents[0]->id, 0, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[0]->id, true, true);

        // Check that the student does not see "Submit" button.
        $output = $assign->view_student_summary($this->extrastudents[0], true);
        $this->assertNotContains(get_string('submitassignment', 'assign'), $output);

        // Change to another user in the same group.
        $this->setUser($this->extrastudents[self::GROUP_COUNT]);
        $output = $assign->view_student_summary($this->extrastudents[self::GROUP_COUNT], true);
        $this->assertContains(get_string('submitassignment', 'assign'), $output);

        $submission = $assign->get_group_submission($this->extrastudents[self::GROUP_COUNT]->id, 0, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[self::GROUP_COUNT]->id, true, true);
        $output = $assign->view_student_summary($this->extrastudents[self::GROUP_COUNT], true);
        $this->assertNotContains(get_string('submitassignment', 'assign'), $output);
    }

    public function test_submissions_open() {
        $this->setUser($this->editingteachers[0]);

        $now = time();
        $tomorrow = $now + 24*60*60;
        $oneweek = $now + 7*24*60*60;
        $yesterday = $now - 24*60*60;

        $assign = $this->create_instance();
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$tomorrow));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$yesterday));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$yesterday, 'cutoffdate'=>$tomorrow));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('duedate'=>$yesterday, 'cutoffdate'=>$yesterday));
        $this->assertEquals(false, $assign->testable_submissions_open($this->students[0]->id));

        $assign->testable_save_user_extension($this->students[0]->id, $tomorrow);
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $assign = $this->create_instance(array('submissiondrafts'=>1));
        $this->assertEquals(true, $assign->testable_submissions_open($this->students[0]->id));

        $this->setUser($this->students[0]);
        $now = time();
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);
        $this->setUser($this->editingteachers[0]);
        $this->assertEquals(false, $assign->testable_submissions_open($this->students[0]->id));
    }

    public function test_get_graders() {
        $this->create_extra_users();
        $this->setUser($this->editingteachers[0]);

        // Create an assignment with no groups.
        $assign = $this->create_instance();
        $this->assertCount(self::DEFAULT_TEACHER_COUNT +
                           self::DEFAULT_EDITING_TEACHER_COUNT +
                           self::EXTRA_TEACHER_COUNT +
                           self::EXTRA_EDITING_TEACHER_COUNT,
                           $assign->testable_get_graders($this->students[0]->id));

        // Force create an assignment with SEPARATEGROUPS.
        $data = new stdClass();
        $data->courseid = $this->course->id;
        $data->name = 'Grouping';
        $groupingid = groups_create_grouping($data);
        groups_assign_grouping($groupingid, $this->groups[0]->id);
        $assign = $this->create_instance(array('groupingid' => $groupingid, 'groupmode' => SEPARATEGROUPS));

        $this->setUser($this->students[1]);
        $this->assertCount(4, $assign->testable_get_graders($this->students[0]->id));
        // Note the second student is in a group that is not in the grouping.
        // This means that we get all graders that are not in a group in the grouping.
        $this->assertCount(10, $assign->testable_get_graders($this->students[1]->id));
    }

    public function test_group_members_only() {
        global $CFG;

        $this->setAdminUser();
        $this->create_extra_users();
        $CFG->enablegroupmembersonly = true;
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $this->course->id));
        groups_assign_grouping($grouping->id, $this->groups[0]->id);

        // Force create an assignment with SEPARATEGROUPS.
        $instance = $this->getDataGenerator()->create_module('assign', array('course'=>$this->course->id),
            array('groupmembersonly' => SEPARATEGROUPS, 'groupingid' => $grouping->id));

        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $assign = new testable_assign($context, $cm, $this->course);

        $this->setUser($this->teachers[0]);
        get_fast_modinfo($this->course, 0, true);
        $this->assertCount(5, $assign->list_participants(0, true));

    }

    public function test_get_uniqueid_for_user() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        foreach ($this->students as $student) {
            $uniqueid = $assign->get_uniqueid_for_user($student->id);
            $this->assertEquals($student->id, $assign->get_user_id_for_uniqueid($uniqueid));
        }
    }

    public function test_show_student_summary() {
        global $CFG, $PAGE;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // No feedback should be available because this student has not been graded.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, 'Feedback'), 'Do not show feedback if there is no grade');
        // Simulate adding a grade.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Now we should see the feedback.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, 'Feedback'), 'Show feedback if there is a grade');

        // Now hide the grade in gradebook.
        $this->setUser($this->teachers[0]);
        require_once($CFG->libdir.'/gradelib.php');
        $gradeitem = new grade_item(array(
            'itemtype'      => 'mod',
            'itemmodule'    => 'assign',
            'iteminstance'  => $assign->get_instance()->id,
            'courseid'      => $this->course->id));

        $gradeitem->set_hidden(1, false);

        // No feedback should be available because the grade is hidden.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, 'Feedback'), 'Do not show feedback if the grade is hidden in the gradebook');

        // Do the same but add feedback.
        $assign = $this->create_instance(array('assignfeedback_comments_enabled' => 1));

        $this->setUser($this->teachers[0]);
        $grade = $assign->get_user_grade($this->students[0]->id, true);
        $data = new stdClass();
        $data->assignfeedbackcomments_editor = array('text'=>'Tomato sauce',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $plugin->save($grade, $data);

        // Should have feedback but no grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, 'Feedback'), 'Show feedback even if there is no grade');
        $this->assertEquals(false, strpos($output, 'Grade'), 'Do not show grade when there is no grade.');
        $this->assertEquals(false, strpos($output, 'Graded on'), 'Do not show graded date when there is no grade.');

        // Now hide the grade in gradebook.
        $this->setUser($this->teachers[0]);
        $gradeitem = new grade_item(array(
            'itemtype'      => 'mod',
            'itemmodule'    => 'assign',
            'iteminstance'  => $assign->get_instance()->id,
            'courseid'      => $this->course->id));

        $gradeitem->set_hidden(1, false);

        // No feedback should be available because the grade is hidden.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, 'Feedback'), 'Do not show feedback if the grade is hidden in the gradebook');
    }

    public function test_attempt_reopen_method_manual() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('attemptreopenmethod'=>ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
                                               'maxattempts'=>3,
                                               'submissiondrafts'=>1,
                                               'assignsubmission_onlinetext_enabled'=>1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // Student should be able to see an add submission button.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Add a submission.
        $now = time();
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // And now submit it for marking.
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);

        // Verify the student cannot make changes to the submission.
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Mark the submission.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the student can see the grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, '50.0'));

        // Allow the student another attempt.
        $this->teachers[0]->ignoresesskey = true;
        $this->setUser($this->teachers[0]);
        $result = $assign->testable_process_add_attempt($this->students[0]->id);
        $this->assertEquals(true, $result);

        // Check that the previous attempt is now in the submission history table.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        // Need a better check.
        $this->assertNotEquals(false, strpos($output, 'Submission text'), 'Contains: Submission text');

        // Check that the student now has a button for Add a new attempt".
        $this->assertNotEquals(false, strpos($output, get_string('addnewattempt', 'assign')));
        // Check that the student now does not have a button for Submit.
        $this->assertEquals(false, strpos($output, get_string('submitassignment', 'assign')));

        // Check that the student now has a submission history.
        $this->assertNotEquals(false, strpos($output, get_string('attempthistory', 'assign')));

        $this->setUser($this->teachers[0]);
        // Check that the grading table loads correctly and contains this user.
        // This is also testing that we do not get duplicate rows in the grading table.
        $gradingtable = new assign_grading_table($assign, 100, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, $this->students[0]->lastname));

        // Should be 1 not 2.
        $this->assertEquals(1, $assign->count_submissions());
        $this->assertEquals(1, $assign->count_submissions_with_status('reopened'));
        $this->assertEquals(0, $assign->count_submissions_need_grading());
        $this->assertEquals(1, $assign->count_grades());

        // Change max attempts to unlimited.
        $formdata = clone($assign->get_instance());
        $formdata->maxattempts = ASSIGN_UNLIMITED_ATTEMPTS;
        $formdata->instance = $formdata->id;
        $assign->update_instance($formdata);

        // Check we can repopen still.
        $result = $assign->testable_process_add_attempt($this->students[0]->id);
        $this->assertEquals(true, $result);

        $grades = $assign->get_user_grades_for_gradebook($this->students[0]->id);
        $this->assertEquals(50, (int)$grades[$this->students[0]->id]->rawgrade);

    }

    public function test_markingworkflow() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('markingworkflow'=>1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // Mark the submission and set to notmarked.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $data->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED;
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the student can't see the grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Mark the submission and set to inmarking.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $data->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_INMARKING;
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the student can't see the grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Mark the submission and set to readyforreview.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $data->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW;
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the student can't see the grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Mark the submission and set to inreview.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $data->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW;
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the student can't see the grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Mark the submission and set to readyforrelease.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $data->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE;
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the student can't see the grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Mark the submission and set to released.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->grade = '50.0';
        $data->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the student can see the grade.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, '50.0'));
    }

    public function test_markerallocation() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance(array('markingworkflow'=>1, 'markingallocation'=>1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // Allocate marker to submission.
        $data = new stdClass();
        $data->allocatedmarker = $this->teachers[0]->id;
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);

        // Check the allocated marker can view the submission.
        $this->setUser($this->teachers[0]);
        $gradingtable = new assign_grading_table($assign, 100, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, $this->students[0]->lastname));

        // Check that other teachers can't view this submission.
        $this->setUser($this->teachers[1]);
        $gradingtable = new assign_grading_table($assign, 100, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertNotEquals(true, strpos($output, $this->students[0]->lastname));
    }

    public function test_extension_granted_event() {
        $this->setUser($this->editingteachers[0]);

        $tomorrow = time() + 24*60*60;
        $yesterday = time() - 24*60*60;

        $assign = $this->create_instance(array('duedate' => $yesterday, 'cutoffdate' => $yesterday));
        $sink = $this->redirectEvents();

        $assign->testable_save_user_extension($this->students[0]->id, $tomorrow);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\extension_granted', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'grant extension',
            'view.php?id=' . $assign->get_course_module()->id,
            $this->students[0]->id,
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();
    }

    public function test_submission_locked_event() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $sink = $this->redirectEvents();

        $assign->lock_submission($this->students[0]->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_locked', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'lock submission',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('locksubmissionforstudent', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_identities_revealed_event() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance(array('blindmarking'=>1));
        $sink = $this->redirectEvents();

        $assign->reveal_identities();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\identities_revealed', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'reveal identities',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('revealidentities', 'assign'),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_submission_status_updated_event() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->students[0]->id, true, false);

        $sink = $this->redirectEvents();
        $assign->revert_to_draft($this->students[0]->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_status_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals(ASSIGN_SUBMISSION_STATUS_DRAFT, $event->other['newstatus']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'revert submission to draft',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('reverttodraftforstudent', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_teacher_submit_for_student() {
        global $PAGE;

        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1, 'submissiondrafts'=>1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        $this->setUser($this->students[0]);
        // Simulate a submission.
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Student submission text',
                                         'format'=>FORMAT_MOODLE);

        $notices = array();
        $assign->save_submission($data, $notices);

        // Check that the submission text was saved.
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertContains('Student submission text', $output, 'Contains student submission text');

        // Check that a teacher teacher with the extra capability can edit a students submission.
        $this->setUser($this->teachers[0]);
        $data = new stdClass();
        $data->userid = $this->students[0]->id;
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Teacher edited submission text',
                                         'format'=>FORMAT_MOODLE);

        // Add the required capability.
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('mod/assign:editothersubmission', CAP_ALLOW, $roleid, $assign->get_context()->id);
        role_assign($roleid, $this->teachers[0]->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Try to save the submission.
        $notices = array();
        $assign->save_submission($data, $notices);

        // Check that the teacher can submit the students work.
        $data = new stdClass();
        $data->userid = $this->students[0]->id;
        $notices = array();
        $assign->submit_for_grading($data, $notices);

        // Revert to draft so the student can edit it.
        $assign->revert_to_draft($this->students[0]->id);

        $this->setUser($this->students[0]);

        // Check that the submission text was saved.
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertContains('Teacher edited submission text', $output, 'Contains student submission text');

        // Check that the student can submit their work.
        $data = new stdClass();
        $assign->submit_for_grading($data, $notices);

        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotContains(get_string('addsubmission', 'assign'), $output);

        // Set to a default editing teacher who should not be able to edit this submission.
        $this->setUser($this->editingteachers[1]);

        // Revert to draft so the submission is editable.
        $assign->revert_to_draft($this->students[0]->id);

        $data = new stdClass();
        $data->userid = $this->students[0]->id;
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Teacher 2 edited submission text',
                                         'format'=>FORMAT_MOODLE);

        $notices = array();
        $this->setExpectedException('moodle_exception');
        $assign->save_submission($data, $notices);

        $sink->close();
    }

    public function test_marker_updated_event() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();

        $sink = $this->redirectEvents();
        $assign->testable_process_set_batch_marking_allocation($this->students[0]->id, $this->teachers[0]->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\marker_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals($this->editingteachers[0]->id, $event->userid);
        $this->assertEquals($this->teachers[0]->id, $event->other['markerid']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'set marking allocation',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('setmarkerallocationforlog', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]), 'marker' => fullname($this->teachers[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_workflow_state_updated_event() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();

        $sink = $this->redirectEvents();
        $assign->testable_process_set_batch_marking_workflow_state($this->students[0]->id, ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\workflow_state_updated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $this->assertEquals($this->editingteachers[0]->id, $event->userid);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW, $event->other['newstate']);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'set marking workflow state',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('setmarkingworkflowstateforlog', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]), 'state' => ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW)),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_submission_duplicated_event() {
        $this->setUser($this->students[0]);

        $assign = $this->create_instance();
        $submission1 = $assign->get_user_submission($this->students[0]->id, true, 0);
        $submission2 = $assign->get_user_submission($this->students[0]->id, true, 1);
        $submission2->status = ASSIGN_SUBMISSION_STATUS_REOPENED;
        $assign->testable_update_submission($submission2, $this->students[0]->id, time(), $assign->get_instance()->teamsubmission);

        $sink = $this->redirectEvents();
        $notices = null;
        $assign->copy_previous_attempt($notices);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_duplicated', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($submission2->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->userid);
        $submission2->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'submissioncopied',
            'view.php?id=' . $assign->get_course_module()->id,
            $assign->testable_format_submission_for_log($submission2),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();
    }

    public function test_submission_unlocked_event() {
        $this->editingteachers[0]->ignoresesskey = true;
        $this->setUser($this->editingteachers[0]);

        $assign = $this->create_instance();
        $sink = $this->redirectEvents();

        $assign->unlock_submission($this->students[0]->id);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_unlocked', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($assign->get_instance()->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'unlock submission',
            'view.php?id=' . $assign->get_course_module()->id,
            get_string('unlocksubmissionforstudent', 'assign', array('id' => $this->students[0]->id,
                'fullname' => fullname($this->students[0]))),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Revert to defaults.
        $this->editingteachers[0]->ignoresesskey = false;
    }

    public function test_submission_graded_event() {
        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        // Test apply_grade_to_user.
        $sink = $this->redirectEvents();

        $data = new stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->students[0]->id, 0);
        $grade = $assign->get_user_grade($this->students[0]->id, false, 0);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'grade submission',
            'view.php?id=' . $assign->get_course_module()->id,
            $assign->format_grade_for_log($grade),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();

        // Test process_save_quick_grades.
        $sink = $this->redirectEvents();

        $data = array(
            'grademodified_' . $this->students[0]->id => time(),
            'quickgrade_' . $this->students[0]->id => '60.0'
        );
        $assign->testable_process_save_quick_grades($data);
        $grade = $assign->get_user_grade($this->students[0]->id, false);
        $this->assertEquals('60.0', $grade->grade);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\mod_assign\event\submission_graded', $event);
        $this->assertEquals($assign->get_context(), $event->get_context());
        $this->assertEquals($grade->id, $event->objectid);
        $this->assertEquals($this->students[0]->id, $event->relateduserid);
        $expected = array(
            $assign->get_course()->id,
            'assign',
            'grade submission',
            'view.php?id=' . $assign->get_course_module()->id,
            $assign->format_grade_for_log($grade),
            $assign->get_course_module()->id
        );
        $this->assertEventLegacyLogData($expected, $event);
        $sink->close();
    }

    public function test_disable_submit_after_cutoff_date() {
        global $PAGE;

        $this->setUser($this->editingteachers[0]);
        $now = time();
        $tomorrow = $now + 24*60*60;
        $lastweek = $now - 7*24*60*60;
        $yesterday = $now - 24*60*60;

        $assign = $this->create_instance(array('duedate'=>$yesterday,
                                               'cutoffdate'=>$tomorrow,
                                               'assignsubmission_onlinetext_enabled'=>1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // Student should be able to see an add submission button.
        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Add a submission but don't submit now.
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>'Submission text',
                                         'format'=>FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Create another instance with cut-off and due-date already passed.
        $this->setUser($this->editingteachers[0]);
        $now = time();
        $assign = $this->create_instance(array('duedate'=>$lastweek,
                                               'cutoffdate'=>$yesterday,
                                               'assignsubmission_onlinetext_enabled'=>1));

        $this->setUser($this->students[0]);
        $output = $assign->view_student_summary($this->students[0], true);
        $this->assertNotContains($output, get_string('editsubmission', 'assign'),
                                 'Should not be able to edit after cutoff date.');
        $this->assertNotContains($output, get_string('submitassignment', 'assign'),
                                 'Should not be able to submit after cutoff date.');
    }
    /**
     * Testing for submission comment plugin settings
     */
    public function test_submission_comment_plugin_settings() {
        global $CFG;

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }

        $CFG->usecomments = true;
        $assign = $this->create_instance();
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEquals(1, $plugin->is_enabled('enabled'));

        $assign = $this->create_instance(array('assignsubmission_comments_enabled' => 0));
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEquals(1, $plugin->is_enabled('enabled'));

        $assign = $this->create_instance(array('assignsubmission_comments_enabled' => 1));
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEquals(1, $plugin->is_enabled('enabled'));

        $CFG->usecomments = false;
        $assign = $this->create_instance();
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEquals(0, $plugin->is_enabled('enabled'));

        $assign = $this->create_instance(array('assignsubmission_comments_enabled' => 0));
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEquals(0, $plugin->is_enabled('enabled'));

        $assign = $this->create_instance(array('assignsubmission_comments_enabled' => 1));
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEquals(0, $plugin->is_enabled('enabled'));

        $CFG->usecomments = $commentconfig;
    }

    /**
     * Testing for comment inline settings
     */
    public function test_feedback_comment_commentinline() {
        global $CFG;

        $sourcetext = "Hello!

I'm writing to you from the Moodle Majlis in Muscat, Oman, where we just had several days of Moodle community goodness.

URL outside a tag: https://moodle.org/logo/logo-240x60.gif
Plugin url outside a tag: @@PLUGINFILE@@/logo-240x60.gif

External link 1:<img src='https://moodle.org/logo/logo-240x60.gif' alt='Moodle'/>
External link 2:<img alt=\"Moodle\" src=\"https://moodle.org/logo/logo-240x60.gif\"/>
Internal link 1:<img src='@@PLUGINFILE@@/logo-240x60.gif' alt='Moodle'/>
Internal link 2:<img alt=\"Moodle\" src=\"@@PLUGINFILE@@logo-240x60.gif\"/>
Anchor link 1:<a href=\"@@PLUGINFILE@@logo-240x60.gif\" alt=\"bananas\">Link text</a>
Anchor link 2:<a title=\"bananas\" href=\"../logo-240x60.gif\">Link text</a>
";

        // Note the internal images have been stripped and the html is purified (quotes fixed in this case).
        $filteredtext = "Hello!

I'm writing to you from the Moodle Majlis in Muscat, Oman, where we just had several days of Moodle community goodness.

URL outside a tag: https://moodle.org/logo/logo-240x60.gif
Plugin url outside a tag: @@PLUGINFILE@@/logo-240x60.gif

External link 1:<img src=\"https://moodle.org/logo/logo-240x60.gif\" alt=\"Moodle\" />
External link 2:<img alt=\"Moodle\" src=\"https://moodle.org/logo/logo-240x60.gif\" />
Internal link 1:
Internal link 2:
Anchor link 1:Link text
Anchor link 2:<a title=\"bananas\" href=\"../logo-240x60.gif\">Link text</a>
";

        $this->setUser($this->editingteachers[0]);
        $params = array('assignsubmission_onlinetext_enabled' => 1,
                        'assignfeedback_comments_enabled' => 1,
                        'assignfeedback_comments_commentinline' => 1);
        $assign = $this->create_instance($params);

        $this->setUser($this->students[0]);
        // Add a submission but don't submit now.
        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $data = new stdClass();

        // Test the internal link is stripped, but the external one is not.
        $data->onlinetext_editor = array('itemid'=>file_get_unused_draft_itemid(),
                                         'text'=>$sourcetext,
                                         'format'=>FORMAT_MOODLE);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        $this->setUser($this->editingteachers[0]);

        $data = new stdClass();
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');
        $pagination = array('userid'=>$this->students[0]->id,
                            'rownum'=>0,
                            'last'=>true,
                            'useridlistid'=>time(),
                            'attemptnumber'=>0);
        $formparams = array($assign, $data, $pagination);
        $mform = new mod_assign_grade_form(null, $formparams);

        $this->assertEquals($filteredtext, $data->assignfeedbackcomments_editor['text']);
    }

    /**
     * Testing for feedback comment plugin settings
     */
    public function test_feedback_plugin_settings() {

        $assign = $this->create_instance();
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertEquals(0, $plugin->is_enabled('enabled'));

        $assign = $this->create_instance(array('assignfeedback_comments_enabled' => 0));
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertEquals(0, $plugin->is_enabled('enabled'));

        $assign = $this->create_instance(array('assignfeedback_comments_enabled' => 1));
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertEquals(1, $plugin->is_enabled('enabled'));
    }

    /**
     * Testing if gradebook feedback plugin is enabled.
     */
    public function test_is_gradebook_feedback_enabled() {
        $adminconfig = get_config('assign');
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        // Create assignment with gradebook feedback enabled and grade = 0.
        $assign = $this->create_instance(array($gradebookplugin . '_enabled' => 1, 'grades' => 0));

        // Get gradebook feedback plugin.
        $gradebookplugintype = str_replace('assignfeedback_', '', $gradebookplugin);
        $plugin = $assign->get_feedback_plugin_by_type($gradebookplugintype);
        $this->assertEquals(1, $plugin->is_enabled('enabled'));
        $this->assertEquals(1, $assign->is_gradebook_feedback_enabled());

        // Create assignment with gradebook feedback disabled and grade = 0.
        $assign = $this->create_instance(array($gradebookplugin . '_enabled' => 0, 'grades' => 0));
        $plugin = $assign->get_feedback_plugin_by_type($gradebookplugintype);
        $this->assertEquals(0, $plugin->is_enabled('enabled'));
    }

    /**
     * Testing can_edit_submission
     */
    public function test_can_edit_submission() {
        global $PAGE, $DB;
        $this->create_extra_users();

        $this->setAdminUser();
        // Create assignment (onlinetext).
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled'=>1, 'submissiondrafts'=>1));
        $PAGE->set_url(new moodle_url('/mod/assign/view.php', array('id' => $assign->get_course_module()->id)));

        // Check student can edit their own submission.
        $this->assertTrue($assign->can_edit_submission($this->students[0]->id, $this->students[0]->id));
        // Check student cannot edit others submission.
        $this->assertFalse($assign->can_edit_submission($this->students[0]->id, $this->students[1]->id));

        // Check teacher cannot (by default) edit a students submission.
        $this->assertFalse($assign->can_edit_submission($this->students[0]->id, $this->teachers[0]->id));

        // Add the required capability to edit a student submission.
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('mod/assign:editothersubmission', CAP_ALLOW, $roleid, $assign->get_context()->id);
        role_assign($roleid, $this->teachers[0]->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();
        // Retest - should now have access.
        $this->assertTrue($assign->can_edit_submission($this->students[0]->id, $this->teachers[0]->id));

        // Force create an assignment with SEPARATEGROUPS.
        $data = new stdClass();
        $data->courseid = $this->course->id;
        $data->name = 'Grouping';
        $groupingid = groups_create_grouping($data);
        groups_assign_grouping($groupingid, $this->groups[0]->id);
        groups_assign_grouping($groupingid, $this->groups[1]->id);
        $assign = $this->create_instance(array('groupingid' => $groupingid, 'groupmode' => SEPARATEGROUPS));

        // Add the capability to the new assignment for extra students 0 and 1.
        assign_capability('mod/assign:editothersubmission', CAP_ALLOW, $roleid, $assign->get_context()->id);
        role_assign($roleid, $this->extrastudents[0]->id, $assign->get_context()->id);
        role_assign($roleid, $this->extrastudents[1]->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Verify the extra student does not have the capability to edit a submission not in their group.
        $this->assertFalse($assign->can_edit_submission($this->students[0]->id, $this->extrastudents[1]->id));
        // Verify the extra student does have the capability to edit a submission in their group.
        $this->assertTrue($assign->can_edit_submission($this->students[0]->id, $this->extrastudents[0]->id));

    }
}

