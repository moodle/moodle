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
 * File contains the unit tests for the email certificate task.
 *
 * @package    mod_customcert
 * @category   test
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

use completion_info;
use stdClass;
use context_course;
use advanced_testcase;
use mod_customcert\task\email_certificate_task;
use mod_customcert\task\issue_certificates_task;

/**
 * Unit tests for the email certificate task.
 *
 * @package    mod_customcert
 * @category   test
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_customcert\task\email_certificate_task
 */
final class email_certificate_task_test extends advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp(): void {
        $this->resetAfterTest();

        set_config('certificateexecutionperiod', 0, 'customcert');

        parent::setUp();
    }

    /**
     * Tests the email certificate task when there are no elements.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_no_elements(): void {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a user.
        $user1 = $this->getDataGenerator()->create_user();

        // Create a custom certificate with no elements.
        $this->getDataGenerator()->create_module('customcert', ['course' => $course->id, 'emailstudents' => 1]);

        // Enrol the user as a student.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm that we did not send any emails because the certificate has no elements.
        $this->assertCount(0, $emails);
    }

    /**
     * Tests the email certificate task for users without a capability to receive a certificate.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_no_cap(): void {
        global $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Enrol two of them in the course as students but revoke their right to receive a certificate issue.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        unassign_capability('mod/customcert:receiveissue', $roleids['student']);

        // Create a custom certificate.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id, 'emailstudents' => 1]);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm that we did not send any emails.
        $this->assertCount(0, $emails);
    }

    /**
     * Tests the email certificate task for students.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_students(): void {
        global $CFG, $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user(['firstname' => 'Teacher', 'lastname' => 'One']);

        // Enrol two of them in the course as students.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        // Enrol one of the users as a teacher.
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $roleids['editingteacher']);

        // Create a custom certificate.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id,
            'emailstudents' => 1]);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Ok, now issue this to one user.
        \mod_customcert\certificate::issue_certificate($customcert->id, $user1->id);

        // Confirm there is only one entry in this table.
        $this->assertEquals(1, $DB->count_records('customcert_issues'));

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Get the issues from the issues table now.
        $issues = $DB->get_records('customcert_issues');
        $this->assertCount(2, $issues);

        // Confirm that it was marked as emailed and was not issued to the teacher.
        foreach ($issues as $issue) {
            $this->assertEquals(1, $issue->emailed);
            $this->assertNotEquals($user3->id, $issue->userid);
        }

        // Confirm that we sent out emails to the two users.
        $this->assertCount(2, $emails);

        $this->assertEquals($CFG->noreplyaddress, $emails[0]->from);
        $this->assertEquals($user1->email, $emails[0]->to);

        $this->assertEquals($CFG->noreplyaddress, $emails[1]->from);
        $this->assertEquals($user2->email, $emails[1]->to);

        // Now, run the task again and ensure we did not issue any more certificates.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        $issues = $DB->get_records('customcert_issues');

        $this->assertCount(2, $issues);
        $this->assertCount(0, $emails);
    }

    /**
     * Tests the email certificate task for teachers.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_teachers(): void {
        global $CFG, $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user(['firstname' => 'Teacher', 'lastname' => 'One']);

        // Enrol two of them in the course as students.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        // Enrol one of the users as a teacher.
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $roleids['editingteacher']);

        // Create a custom certificate.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id,
            'emailteachers' => 1]);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm that we only sent out 2 emails, both emails to the teacher for the two students.
        $this->assertCount(2, $emails);

        $this->assertEquals($CFG->noreplyaddress, $emails[0]->from);
        $this->assertEquals($user3->email, $emails[0]->to);

        $this->assertEquals($CFG->noreplyaddress, $emails[1]->from);
        $this->assertEquals($user3->email, $emails[1]->to);
    }

    /**
     * Tests the email certificate task for others.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_others(): void {
        global $CFG, $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Enrol two of them in the course as students.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        // Create a custom certificate.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id,
            'emailothers' => 'testcustomcert@example.com, doo@dah']);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm that we only sent out 2 emails, both emails to the other address that was valid for the two students.
        $this->assertCount(2, $emails);

        $this->assertEquals($CFG->noreplyaddress, $emails[0]->from);
        $this->assertEquals('testcustomcert@example.com', $emails[0]->to);

        $this->assertEquals($CFG->noreplyaddress, $emails[1]->from);
        $this->assertEquals('testcustomcert@example.com', $emails[1]->to);
    }

    /**
     * Tests the email certificate task when the certificate is not visible.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_students_not_visible(): void {
        global $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a user.
        $user1 = $this->getDataGenerator()->create_user();

        // Enrol them in the course.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        // Create a custom certificate.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id, 'emailstudents' => 1]);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Remove the permission for the user to view the certificate.
        assign_capability('mod/customcert:view', CAP_PROHIBIT, $roleids['student'], \context_course::instance($course->id));

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm there are no issues as the user did not have permissions to view it.
        $issues = $DB->get_records('customcert_issues');
        $this->assertCount(0, $issues);

        // Confirm no emails were sent.
        $this->assertCount(0, $emails);
    }

    /**
     * Tests the email certificate task when the student has not met the required time for the course.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_students_havent_met_required_time(): void {
        global $DB;

        // Set the standard log to on.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a user.
        $user1 = $this->getDataGenerator()->create_user();

        // Enrol them in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        // Create a custom certificate.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id, 'emailstudents' => 1,
            'requiredtime' => '60']);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm there are no issues as the user did not meet the required time.
        $issues = $DB->get_records('customcert_issues');
        $this->assertCount(0, $issues);

        // Confirm no emails were sent.
        $this->assertCount(0, $emails);
    }

    /**
     * Tests the email certificate task when the student has not met the completion criteria.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_students_havent_met_required_criteria(): void {
        global $CFG, $DB;

        $CFG->enablecompletion = true;

        // Create a course.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);

        // Create a user.
        $user1 = $this->getDataGenerator()->create_user();

        // Enrol them in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $quizmodule = $DB->get_record('course_modules', ['id' => $quiz->cmid]);

        // Set completion criteria for the quiz.
        $quizmodule->completion = COMPLETION_TRACKING_AUTOMATIC;
        $quizmodule->completionview = 1; // Require view to complete.
        $quizmodule->completionexpected = 0;
        $DB->update_record('course_modules', $quizmodule);

        // Set restrict access to the customcert activity based on the completion of the quiz.
        $customcert = $this->getDataGenerator()->create_module('customcert', [
            'course' => $course->id,
            'emailstudents' => 1,
            'availability' => json_encode(
                [
                    'op' => '&',
                    'c' => [
                        [
                            'type' => 'completion',
                            'cm' => $quiz->cmid,
                            'e' => COMPLETION_COMPLETE, // Ensure the quiz is marked as complete.
                        ],
                    ],
                    'showc' => [
                        false,
                    ],
                ],
            ),
        ]);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm there are no issues as the user can not view the certificate.
        $issues = $DB->get_records('customcert_issues');
        $this->assertCount(0, $issues);

        // Confirm no emails were sent.
        $this->assertCount(0, $emails);
    }

    /**
     * Tests the email certificate task when the student has met the completion criteria.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     * @covers \mod_customcert\task\email_certificate_task
     */
    public function test_email_certificates_students_have_met_required_criteria(): void {
        global $CFG, $DB;

        $CFG->enablecompletion = true;

        // Create a course.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);

        // Create a user.
        $user1 = $this->getDataGenerator()->create_user();

        // Enrol them in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);

        // Create a quiz.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $quizmodule = $DB->get_record('course_modules', ['id' => $quiz->cmid]);

        // Set completion criteria for the quiz.
        $quizmodule->completion = COMPLETION_TRACKING_AUTOMATIC;
        $quizmodule->completionview = 1; // Require view to complete.
        $quizmodule->completionexpected = 0;
        $DB->update_record('course_modules', $quizmodule);

        // Mark the quiz as complete for the user.
        $completion = new completion_info($course);
        $completion->update_state($quizmodule, COMPLETION_COMPLETE, $user1->id);

        // Set restrict access to the customcert activity based on the completion of the quiz.
        $customcert = $this->getDataGenerator()->create_module('customcert', [
            'course' => $course->id,
            'emailstudents' => 1,
            'availability' => json_encode(
                [
                    'op' => '&',
                    'c' => [
                        [
                            'type' => 'completion',
                            'cm' => $quiz->cmid,
                            'e' => COMPLETION_COMPLETE, // Ensure the quiz is marked as complete.
                        ],
                    ],
                    'showc' => [
                        false,
                    ],
                ],
            ),
        ]);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Confirm there is an issue as the user can view the certificate.
        $issues = $DB->get_records('customcert_issues');
        $this->assertCount(1, $issues);

        // Confirm an email was sent.
        $this->assertCount(1, $emails);
    }

    /**
     * Tests the email certificate task running adhoc.
     *
     * @covers \mod_customcert\task\email_certificate_task
     * @covers \mod_customcert\task\issue_certificates_task
     */
    public function test_email_certificates_adhoc(): void {
        global $CFG, $DB;

        set_config('useadhoc', 1, 'customcert');

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user(['firstname' => 'Teacher', 'lastname' => 'One']);

        // Enrol two of them in the course as students.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        // Enrol one of the users as a teacher.
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $roleids['editingteacher']);

        // Create a custom certificate.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id,
            'emailstudents' => 1]);

        // Create template object.
        $template = new stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'A template';
        $template->contextid = context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page to this template.
        $pageid = $template->add_page();

        // Add an element to the page.
        $element = new stdClass();
        $element->pageid = $pageid;
        $element->name = 'Image';
        $DB->insert_record('customcert_elements', $element);

        // Ok, now issue this to one user.
        \mod_customcert\certificate::issue_certificate($customcert->id, $user1->id);

        // Confirm there is only one entry in this table.
        $this->assertEquals(1, $DB->count_records('customcert_issues'));

        // Run the task.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        // Get the issues from the issues table now.
        $issues = $DB->get_records('customcert_issues');
        $this->assertCount(2, $issues);

        // Confirm that it wasn't marked as emailed and was not issued to the teacher.
        foreach ($issues as $issue) {
            $this->assertEquals(0, $issue->emailed);
            $this->assertNotEquals($user3->id, $issue->userid);
        }

        // Now we send emails to the two users using the adhoc method.
        $this->assertCount(0, $emails);
        $issues = array_values($issues);
        $task = new email_certificate_task();
        $task->set_custom_data((object)['issueid' => $issues[0]->id, 'customcertid' => $customcert->id]);
        $task->execute();
        $task->set_custom_data((object)['issueid' => $issues[1]->id, 'customcertid' => $customcert->id]);
        $task->execute();
        $emails = $sink->get_messages();

        // Get the issues from the issues table now.
        $issues = $DB->get_records('customcert_issues');
        // Confirm that it wasn't marked as emailed and was not issued to the teacher.
        foreach ($issues as $issue) {
            $this->assertEquals(1, $issue->emailed);
            $this->assertNotEquals($user3->id, $issue->userid);
        }

        // Confirm that we sent out emails to the two users.
        $this->assertCount(2, $emails);

        $this->assertEquals($CFG->noreplyaddress, $emails[0]->from);
        $this->assertEquals($user1->email, $emails[0]->to);

        $this->assertEquals($CFG->noreplyaddress, $emails[1]->from);
        $this->assertEquals($user2->email, $emails[1]->to);

        // Now, run the task again and ensure we did not issue any more certificates.
        $sink = $this->redirectEmails();
        $task = new issue_certificates_task();
        $task->execute();
        $emails = $sink->get_messages();

        $issues = $DB->get_records('customcert_issues');

        $this->assertCount(2, $issues);
        $this->assertCount(0, $emails);
    }

    /**
     * Tests that we still issue a certificate if there are none when 'certificateexecutionperiod' is set.
     *
     * @covers \mod_customcert\task\issue_certificates_task
     */
    public function test_issue_certificates_task_creates_issue_when_none_exist(): void {
        global $CFG, $DB;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student user.
        $student = $this->getDataGenerator()->create_user();

        // Enrol the student in the course.
        $this->getDataGenerator()->enrol_user($student->id, $course->id);

        // Create a custom certificate module with emailing enabled for students.
        $customcert = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id,
            'emailstudents' => 1]);

        // Set up a basic certificate template.
        $template = new \stdClass();
        $template->id = $customcert->templateid;
        $template->name = 'Test Template';
        $template->contextid = \context_course::instance($course->id)->id;
        $template = new template($template);

        // Add a page and an element to put the certificate in a valid state.
        $pageid = $template->add_page();
        $element = new \stdClass();
        $element->pageid = $pageid;
        $element->name = 'Test Element';
        $DB->insert_record('customcert_elements', $element);

        // Verify that no certificate issues exist before task execution.
        $this->assertEmpty($DB->get_records('customcert_issues'),
            'No certificate issues should exist before executing the task.');

        // Redirect emails to a sink so we can capture any outgoing messages.
        $sink = $this->redirectEmails();

        set_config('certificateexecutionperiod', 1, 'customcert');

        // Execute the issue certificates task.
        $task = new \mod_customcert\task\issue_certificates_task();
        $task->execute();

        // After executing the task, verify that a certificate issue record was created.
        $issues = $DB->get_records('customcert_issues');
        $this->assertCount(1, $issues,
            'A certificate issue record should have been created by the task.');
        $issue = reset($issues);
        $this->assertEquals(1, $issue->emailed,
            'The certificate issue should be marked as emailed.');

        // Verify that an email was sent to the student.
        $emails = $sink->get_messages();
        $this->assertCount(1, $emails, 'An email should have been sent to the student.');
        $this->assertEquals($CFG->noreplyaddress, $emails[0]->from, 'Email sender is incorrect.');
        $this->assertEquals($student->email, $emails[0]->to, 'Email recipient is incorrect.');
    }

}
