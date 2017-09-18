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
 * Workshop module external functions tests
 *
 * @package    mod_workshop
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.4
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/workshop/lib.php');

use mod_workshop\external\workshop_summary_exporter;

/**
 * Workshop module external functions tests
 *
 * @package    mod_workshop
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.4
 */
class mod_workshop_external_testcase extends externallib_advanced_testcase {

    /** @var stdClass course object */
    private $course;
    /** @var stdClass workshop object */
    private $workshop;
    /** @var stdClass context object */
    private $context;
    /** @var stdClass cm object */
    private $cm;
    /** @var stdClass student object */
    private $student;
    /** @var stdClass teacher object */
    private $teacher;
    /** @var stdClass student role object */
    private $studentrole;
    /** @var stdClass teacher role object */
    private $teacherrole;

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $this->course->id));
        $this->context = context_module::instance($this->workshop->cmid);
        $this->cm = get_coursemodule_from_instance('workshop', $this->workshop->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /**
     * Test test_mod_workshop_get_workshops_by_courses
     */
    public function test_mod_workshop_get_workshops_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second workshop.
        $record = new stdClass();
        $record->course = $course2->id;
        $workshop2 = self::getDataGenerator()->create_module('workshop', $record);

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $this->student->id, $this->studentrole->id);

        self::setUser($this->student);

        $returndescription = mod_workshop_external::get_workshops_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        $properties = workshop_summary_exporter::read_properties_definition();
        $expectedfields = array_keys($properties);

        // Add expected coursemodule and data.
        $workshop1 = $this->workshop;
        $workshop1->coursemodule = $workshop1->cmid;
        $workshop1->introformat = 1;
        $workshop1->introfiles = [];
        $workshop1->instructauthorsfiles = [];
        $workshop1->instructauthorsformat = 1;
        $workshop1->instructreviewersfiles = [];
        $workshop1->instructreviewersformat = 1;
        $workshop1->conclusionfiles = [];
        $workshop1->conclusionformat = 1;

        $workshop2->coursemodule = $workshop2->cmid;
        $workshop2->introformat = 1;
        $workshop2->introfiles = [];
        $workshop2->instructauthorsfiles = [];
        $workshop2->instructauthorsformat = 1;
        $workshop2->instructreviewersfiles = [];
        $workshop2->instructreviewersformat = 1;
        $workshop2->conclusionfiles = [];
        $workshop2->conclusionformat = 1;

        foreach ($expectedfields as $field) {
            if (!empty($properties[$field]) && $properties[$field]['type'] == PARAM_BOOL) {
                $workshop1->{$field} = (bool) $workshop1->{$field};
                $workshop2->{$field} = (bool) $workshop2->{$field};
            }
            $expected1[$field] = $workshop1->{$field};
            $expected2[$field] = $workshop2->{$field};
        }

        $expectedworkshops = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_workshop_external::get_workshops_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedworkshops, $result['workshops']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_workshop_external::get_workshops_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedworkshops, $result['workshops']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected workshops.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedworkshops);

        // Call the external function without passing course id.
        $result = mod_workshop_external::get_workshops_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedworkshops, $result['workshops']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_workshop_external::get_workshops_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);
    }

    /**
     * Test mod_workshop_get_workshop_access_information for students.
     */
    public function test_mod_workshop_get_workshop_access_information_student() {

        self::setUser($this->student);
        $result = mod_workshop_external::get_workshop_access_information($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_workshop_access_information_returns(), $result);
        // Check default values for capabilities.
        $enabledcaps = array('canpeerassess', 'cansubmit', 'canview', 'canviewauthornames', 'canviewauthorpublished',
            'canviewpublishedsubmissions', 'canexportsubmissions');

        foreach ($result as $capname => $capvalue) {
            if (strpos($capname, 'can') !== 0) {
                continue;
            }
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }
        // Now, unassign some capabilities.
        unassign_capability('mod/workshop:peerassess', $this->studentrole->id);
        unassign_capability('mod/workshop:submit', $this->studentrole->id);
        unset($enabledcaps[0]);
        unset($enabledcaps[1]);
        accesslib_clear_all_caches_for_unit_testing();

        $result = mod_workshop_external::get_workshop_access_information($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_workshop_access_information_returns(), $result);
        foreach ($result as $capname => $capvalue) {
            if (strpos($capname, 'can') !== 0) {
                continue;
            }
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }

        // Now, specific functionalities.
        $this->assertFalse($result['creatingsubmissionallowed']);
        $this->assertFalse($result['modifyingsubmissionallowed']);
        $this->assertFalse($result['assessingallowed']);
        $this->assertFalse($result['assessingexamplesallowed']);
        $this->assertTrue($result['examplesassessed']);

        // Switch phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_SUBMISSION);
        $result = mod_workshop_external::get_workshop_access_information($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_workshop_access_information_returns(), $result);

        $this->assertTrue($result['creatingsubmissionallowed']);
        $this->assertTrue($result['modifyingsubmissionallowed']);
        $this->assertFalse($result['assessingallowed']);
        $this->assertFalse($result['assessingexamplesallowed']);
        $this->assertTrue($result['examplesassessed']);

        // Switch to next (to assessment).
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_ASSESSMENT);
        $result = mod_workshop_external::get_workshop_access_information($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_workshop_access_information_returns(), $result);

        $this->assertFalse($result['creatingsubmissionallowed']);
        $this->assertFalse($result['modifyingsubmissionallowed']);
        $this->assertTrue($result['assessingallowed']);
        $this->assertFalse($result['assessingexamplesallowed']);
        $this->assertTrue($result['examplesassessed']);
    }

    /**
     * Test mod_workshop_get_workshop_access_information for teachers.
     */
    public function test_mod_workshop_get_workshop_access_information_teacher() {

        self::setUser($this->teacher);
        $result = mod_workshop_external::get_workshop_access_information($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_workshop_access_information_returns(), $result);
        // Check default values.
        $disabledcaps = array('canpeerassess', 'cansubmit');

        foreach ($result as $capname => $capvalue) {
            if (strpos($capname, 'can') !== 0) {
                continue;
            }
            if (in_array($capname, $disabledcaps)) {
                $this->assertFalse($capvalue);
            } else {
                $this->assertTrue($capvalue);
            }
        }

        // Now, specific functionalities.
        $this->assertFalse($result['creatingsubmissionallowed']);
        $this->assertFalse($result['modifyingsubmissionallowed']);
        $this->assertFalse($result['assessingallowed']);
        $this->assertFalse($result['assessingexamplesallowed']);
    }

    /**
     * Test mod_workshop_get_user_plan for students.
     */
    public function test_mod_workshop_get_user_plan_student() {

        self::setUser($this->student);
        $result = mod_workshop_external::get_user_plan($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_user_plan_returns(), $result);

        $this->assertCount(0, $result['userplan']['examples']);  // No examples given.
        $this->assertCount(5, $result['userplan']['phases']);  // Always 5 phases.
        $this->assertEquals(workshop::PHASE_SETUP, $result['userplan']['phases'][0]['code']);  // First phase always setup.
        $this->assertTrue($result['userplan']['phases'][0]['active']); // First phase "Setup" active in new workshops.

        // Switch phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_SUBMISSION);

        $result = mod_workshop_external::get_user_plan($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_user_plan_returns(), $result);

        $this->assertEquals(workshop::PHASE_SUBMISSION, $result['userplan']['phases'][1]['code']);
        $this->assertTrue($result['userplan']['phases'][1]['active']); // We are now in submission phase.
    }

    /**
     * Test mod_workshop_get_user_plan for teachers.
     */
    public function test_mod_workshop_get_user_plan_teacher() {
        global $DB;

        self::setUser($this->teacher);
        $result = mod_workshop_external::get_user_plan($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_user_plan_returns(), $result);

        $this->assertCount(0, $result['userplan']['examples']);  // No examples given.
        $this->assertCount(5, $result['userplan']['phases']);  // Always 5 phases.
        $this->assertEquals(workshop::PHASE_SETUP, $result['userplan']['phases'][0]['code']);  // First phase always setup.
        $this->assertTrue($result['userplan']['phases'][0]['active']); // First phase "Setup" active in new workshops.
        $this->assertCount(4, $result['userplan']['phases'][0]['tasks']);  // For new empty workshops, always 4 tasks.

        foreach ($result['userplan']['phases'][0]['tasks'] as $task) {
            if ($task['code'] == 'intro' || $task['code'] == 'instructauthors') {
                $this->assertEquals(1, $task['completed']);
            } else {
                $this->assertEmpty($task['completed']);
            }
        }

        // Do some of the tasks asked - switch phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_SUBMISSION);

        $result = mod_workshop_external::get_user_plan($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_user_plan_returns(), $result);
        foreach ($result['userplan']['phases'][0]['tasks'] as $task) {
            if ($task['code'] == 'intro' || $task['code'] == 'instructauthors' || $task['code'] == 'switchtonextphase') {
                $this->assertEquals(1, $task['completed']);
            } else {
                $this->assertEmpty($task['completed']);
            }
        }

        $result = mod_workshop_external::get_user_plan($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::get_user_plan_returns(), $result);

        $this->assertEquals(workshop::PHASE_SUBMISSION, $result['userplan']['phases'][1]['code']);
        $this->assertTrue($result['userplan']['phases'][1]['active']); // We are now in submission phase.
    }

    /**
     * Test test_view_workshop invalid id.
     */
    public function test_view_workshop_invalid_id() {
        $this->expectException('moodle_exception');
        mod_workshop_external::view_workshop(0);
    }

    /**
     * Test test_view_workshop user not enrolled.
     */
    public function test_view_workshop_user_not_enrolled() {
        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        $this->expectException('moodle_exception');
        mod_workshop_external::view_workshop($this->workshop->id);
    }

    /**
     * Test test_view_workshop user student.
     */
    public function test_view_workshop_user_student() {
        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_workshop_external::view_workshop($this->workshop->id);
        $result = external_api::clean_returnvalue(mod_workshop_external::view_workshop_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_workshop\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodleworkshop = new \moodle_url('/mod/workshop/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodleworkshop, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test test_view_workshop user missing capabilities.
     */
    public function test_view_workshop_user_missing_capabilities() {
        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/workshop:view', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        // Empty all the caches that may be affected  by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        $this->setUser($this->student);
        $this->expectException('moodle_exception');
        mod_workshop_external::view_workshop($this->workshop->id);
    }

    /**
     * Test test_add_submission.
     */
    public function test_add_submission() {
        $fs = get_file_storage();

        // Test user with full capabilities.
        $this->setUser($this->student);

        $title = 'Submission title';
        $content = 'Submission contents';

        // Create a file in a draft area for inline attachments.
        $draftidinlineattach = file_get_unused_draft_itemid();
        $usercontext = context_user::instance($this->student->id);
        $filenameimg = 'shouldbeanimage.txt';
        $filerecordinline = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidinlineattach,
            'filepath'  => '/',
            'filename'  => $filenameimg,
        );
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        // Create a file in a draft area for regular attachments.
        $draftidattach = file_get_unused_draft_itemid();
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        // Switch to submission phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_SUBMISSION);

        $result = mod_workshop_external::add_submission($this->workshop->id, $title, $content, FORMAT_MOODLE, $draftidinlineattach,
            $draftidattach);
        $result = external_api::clean_returnvalue(mod_workshop_external::add_submission_returns(), $result);
        $this->assertEmpty($result['warnings']);

        // Check submission created.
        $submission = $workshop->get_submission_by_author($this->student->id);
        $this->assertTrue($result['status']);
        $this->assertEquals($result['submissionid'], $submission->id);
        $this->assertEquals($title, $submission->title);
        $this->assertEquals($content, $submission->content);

        // Check files.
        $contentfiles = $fs->get_area_files($this->context->id, 'mod_workshop', 'submission_content', $submission->id);
        $this->assertCount(2, $contentfiles);
        foreach ($contentfiles as $file) {
            if ($file->is_directory()) {
                continue;
            } else {
                $this->assertEquals($filenameimg, $file->get_filename());
            }
        }
        $contentfiles = $fs->get_area_files($this->context->id, 'mod_workshop', 'submission_attachment', $submission->id);
        $this->assertCount(2, $contentfiles);
        foreach ($contentfiles as $file) {
            if ($file->is_directory()) {
                continue;
            } else {
                $this->assertEquals($attachfilename, $file->get_filename());
            }
        }
    }

    /**
     * Test test_add_submission invalid phase.
     */
    public function test_add_submission_invalid_phase() {
        $this->setUser($this->student);

        $this->expectException('moodle_exception');
        mod_workshop_external::add_submission($this->workshop->id, 'Test');
    }

    /**
     * Test test_add_submission empty title.
     */
    public function test_add_submission_empty_title() {
        $this->setUser($this->student);

        // Switch to submission phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_SUBMISSION);

        $this->expectException('moodle_exception');
        mod_workshop_external::add_submission($this->workshop->id, '');
    }

    /**
     * Test test_add_submission already added.
     */
    public function test_add_submission_already_added() {
        $this->setUser($this->student);

        // Switch to submission phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_SUBMISSION);

        // Create the submission.
        $result = mod_workshop_external::add_submission($this->workshop->id, 'My submission');
        $result = external_api::clean_returnvalue(mod_workshop_external::add_submission_returns(), $result);

        // Try to create it again.
        $result = mod_workshop_external::add_submission($this->workshop->id, 'My submission');
        $result = external_api::clean_returnvalue(mod_workshop_external::add_submission_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertArrayNotHasKey('submissionid', $result);
        $this->assertCount(2, $result['warnings']);
        $this->assertEquals('fielderror', $result['warnings'][0]['warningcode']);
        $this->assertEquals('content_editor', $result['warnings'][0]['item']);
        $this->assertEquals('fielderror', $result['warnings'][1]['warningcode']);
        $this->assertEquals('attachment_filemanager', $result['warnings'][1]['item']);
    }

    /**
     * Helper method to create a submission for testing for the given user.
     *
     * @param int $user the submission will be created by this student.
     * @return int the submission id
     */
    protected function create_test_submission($user) {
        // Test user with full capabilities.
        $this->setUser($user);

        $title = 'Submission title';
        $content = 'Submission contents';

        // Create a file in a draft area for inline attachments.
        $fs = get_file_storage();
        $draftidinlineattach = file_get_unused_draft_itemid();
        $usercontext = context_user::instance($this->student->id);
        $filenameimg = 'shouldbeanimage.txt';
        $filerecordinline = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidinlineattach,
            'filepath'  => '/',
            'filename'  => $filenameimg,
        );
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        // Create a file in a draft area for regular attachments.
        $draftidattach = file_get_unused_draft_itemid();
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        // Switch to submission phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_SUBMISSION);

        $result = mod_workshop_external::add_submission($this->workshop->id, $title, $content, FORMAT_MOODLE, $draftidinlineattach,
            $draftidattach);
        return $result['submissionid'];
    }

    /**
     * Test test_update_submission.
     */
    public function test_update_submission() {

        // Create the submission that will be updated.
        $submissionid = $this->create_test_submission($this->student);

        // Test user with full capabilities.
        $this->setUser($this->student);

        $title = 'Submission new title';
        $content = 'Submission new contents';

        // Create a different file in a draft area for inline attachments.
        $fs = get_file_storage();
        $draftidinlineattach = file_get_unused_draft_itemid();
        $usercontext = context_user::instance($this->student->id);
        $filenameimg = 'shouldbeanimage_new.txt';
        $filerecordinline = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidinlineattach,
            'filepath'  => '/',
            'filename'  => $filenameimg,
        );
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        // Create a different file in a draft area for regular attachments.
        $draftidattach = file_get_unused_draft_itemid();
        $filerecordattach = $filerecordinline;
        $attachfilename = 'attachment_new.txt';
        $filerecordattach['filename'] = $attachfilename;
        $filerecordattach['itemid'] = $draftidattach;
        $fs->create_file_from_string($filerecordattach, 'simple text attachment');

        $result = mod_workshop_external::update_submission($submissionid, $title, $content, FORMAT_MOODLE, $draftidinlineattach,
            $draftidattach);
        $result = external_api::clean_returnvalue(mod_workshop_external::update_submission_returns(), $result);
        $this->assertEmpty($result['warnings']);

        // Check submission updated.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $submission = $workshop->get_submission_by_id($submissionid);
        $this->assertTrue($result['status']);
        $this->assertEquals($title, $submission->title);
        $this->assertEquals($content, $submission->content);

        // Check files.
        $contentfiles = $fs->get_area_files($this->context->id, 'mod_workshop', 'submission_content', $submission->id);
        $this->assertCount(2, $contentfiles);
        foreach ($contentfiles as $file) {
            if ($file->is_directory()) {
                continue;
            } else {
                $this->assertEquals($filenameimg, $file->get_filename());
            }
        }
        $contentfiles = $fs->get_area_files($this->context->id, 'mod_workshop', 'submission_attachment', $submission->id);
        $this->assertCount(2, $contentfiles);
        foreach ($contentfiles as $file) {
            if ($file->is_directory()) {
                continue;
            } else {
                $this->assertEquals($attachfilename, $file->get_filename());
            }
        }
    }

    /**
     * Test test_update_submission belonging to other user.
     */
    public function test_update_submission_of_other_user() {
        // Create the submission that will be updated.
        $submissionid = $this->create_test_submission($this->student);

        $this->setUser($this->teacher);

        $this->expectException('moodle_exception');
        mod_workshop_external::update_submission($submissionid, 'Test');
    }

    /**
     * Test test_update_submission invalid phase.
     */
    public function test_update_submission_invalid_phase() {
        // Create the submission that will be updated.
        $submissionid = $this->create_test_submission($this->student);

        $this->setUser($this->student);

        // Switch to assessment phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_ASSESSMENT);

        $this->expectException('moodle_exception');
        mod_workshop_external::update_submission($submissionid, 'Test');
    }

    /**
     * Test test_update_submission empty title.
     */
    public function test_update_submission_empty_title() {
        // Create the submission that will be updated.
        $submissionid = $this->create_test_submission($this->student);

        $this->setUser($this->student);

        $this->expectException('moodle_exception');
        mod_workshop_external::update_submission($submissionid, '');
    }

    /**
     * Test test_delete_submission.
     */
    public function test_delete_submission() {

        // Create the submission that will be deleted.
        $submissionid = $this->create_test_submission($this->student);

        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_workshop_external::delete_submission($submissionid);
        $result = external_api::clean_returnvalue(mod_workshop_external::delete_submission_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertTrue($result['status']);
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $submission = $workshop->get_submission_by_author($this->student->id);
        $this->assertFalse($submission);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking event.
        $this->assertInstanceOf('\mod_workshop\event\submission_deleted', $event);
        $this->assertEquals($this->context, $event->get_context());
    }

    /**
     * Test test_delete_submission_with_assessments.
     */
    public function test_delete_submission_with_assessments() {

        // Create the submission that will be deleted.
        $submissionid = $this->create_test_submission($this->student);

        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $workshopgenerator->create_assessment($submissionid, $this->teacher->id, array(
            'weight' => 3,
            'grade' => 95.00000,
        ));

        $this->setUser($this->student);
        $this->expectException('moodle_exception');
        mod_workshop_external::delete_submission($submissionid);
    }

    /**
     * Test test_delete_submission_invalid_phase.
     */
    public function test_delete_submission_invalid_phase() {

        // Create the submission that will be deleted.
        $submissionid = $this->create_test_submission($this->student);

        // Switch to assessment phase.
        $workshop = new workshop($this->workshop, $this->cm, $this->course);
        $workshop->switch_phase(workshop::PHASE_ASSESSMENT);

        $this->setUser($this->student);
        $this->expectException('moodle_exception');
        mod_workshop_external::delete_submission($submissionid);
    }

    /**
     * Test test_delete_submission_as_teacher.
     */
    public function test_delete_submission_as_teacher() {

        // Create the submission that will be deleted.
        $submissionid = $this->create_test_submission($this->student);

        $this->setUser($this->teacher);
        $result = mod_workshop_external::delete_submission($submissionid);
        $result = external_api::clean_returnvalue(mod_workshop_external::delete_submission_returns(), $result);
        $this->assertEmpty($result['warnings']);
        $this->assertTrue($result['status']);
    }

    /**
     * Test test_delete_submission_other_user.
     */
    public function test_delete_submission_other_user() {

        $anotheruser = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($anotheruser->id, $this->course->id, $this->studentrole->id, 'manual');
        // Create the submission that will be deleted.
        $submissionid = $this->create_test_submission($this->student);

        $this->setUser($anotheruser);
        $this->expectException('moodle_exception');
        mod_workshop_external::delete_submission($submissionid);
    }
}
