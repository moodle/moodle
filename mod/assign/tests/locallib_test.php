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
namespace mod_assign;

use mod_assign_grade_form;
use mod_assign_test_generator;
use mod_assign_testable_assign;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Unit tests for (some of) mod/assign/locallib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_assign_test_generator;

    /** @var array */
    public $extrastudents;

    /** @var array */
    public $extrasuspendedstudents;

    public function test_return_links() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $assign = $this->create_instance($course);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        $assign->register_return_link('RETURNACTION', ['param' => 1]);
        $this->assertEquals('RETURNACTION', $assign->get_return_action());
        $this->assertEquals(['param' => 1], $assign->get_return_params());
    }

    public function test_get_feedback_plugins() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $this->setUser($teacher);
        $assign = $this->create_instance($course);
        $installedplugins = array_keys(\core_component::get_plugin_list('assignfeedback'));

        foreach ($assign->get_feedback_plugins() as $plugin) {
            $this->assertContains($plugin->get_type(), $installedplugins, 'Feedback plugin not in list of installed plugins');
        }
    }

    public function test_get_submission_plugins() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $this->setUser($teacher);
        $assign = $this->create_instance($course);
        $installedplugins = array_keys(\core_component::get_plugin_list('assignsubmission'));

        foreach ($assign->get_submission_plugins() as $plugin) {
            $this->assertContains($plugin->get_type(), $installedplugins, 'Submission plugin not in list of installed plugins');
        }
    }

    public function test_is_blind_marking() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course, ['blindmarking' => 1]);
        $this->assertEquals(true, $assign->is_blind_marking());

        // Test cannot see student names.
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, get_string('hiddenuser', 'assign')));

        // Test students cannot reveal identities.
        $nopermission = false;
        $student->ignoresesskey = true;
        $this->setUser($student);
        $this->expectException('required_capability_exception');
        $assign->reveal_identities();
        $student->ignoresesskey = false;

        // Test teachers cannot reveal identities.
        $nopermission = false;
        $teacher->ignoresesskey = true;
        $this->setUser($teacher);
        $this->expectException('required_capability_exception');
        $assign->reveal_identities();
        $teacher->ignoresesskey = false;

        // Test sesskey is required.
        $this->setUser($teacher);
        $this->expectException('moodle_exception');
        $assign->reveal_identities();

        // Test editingteacher can reveal identities if sesskey is ignored.
        $teacher->ignoresesskey = true;
        $this->setUser($teacher);
        $assign->reveal_identities();
        $this->assertEquals(false, $assign->is_blind_marking());
        $teacher->ignoresesskey = false;

        // Test student names are visible.
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(false, strpos($output, get_string('hiddenuser', 'assign')));

        // Set this back to default.
        $teacher->ignoresesskey = false;
    }

    /**
     * Data provider for test_get_assign_perpage
     *
     * @return array Provider data
     */
    public function get_assign_perpage_provider() {
        return array(
            array(
                'maxperpage' => -1,
                'userprefs' => array(
                    -1 => -1,
                    10 => 10,
                    20 => 20,
                    50 => 50,
                ),
            ),
            array(
                'maxperpage' => 15,
                'userprefs' => array(
                    -1 => 15,
                    10 => 10,
                    20 => 15,
                    50 => 15,
                ),
            ),
        );
    }

    /**
     * Test maxperpage
     *
     * @dataProvider get_assign_perpage_provider
     * @param integer $maxperpage site config value
     * @param array $userprefs Array of user preferences and expected page sizes
     */
    public function test_get_assign_perpage($maxperpage, $userprefs) {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course);

        set_config('maxperpage', $maxperpage, 'assign');
        set_user_preference('assign_perpage', null);
        $this->assertEquals(10, $assign->get_assign_perpage());
        foreach ($userprefs as $pref => $perpage) {
            set_user_preference('assign_perpage', $pref);
            $this->assertEquals($perpage, $assign->get_assign_perpage());
        }
    }

    /**
     * Test filter by requires grading.
     *
     * This is specifically checking an assignment with no grade to make sure we do not
     * get an exception thrown when rendering the grading table for this type of assignment.
     */
    public function test_gradingtable_filter_by_requiresgrading_no_grade() {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
                'assignfeedback_comments_enabled' => 0,
                'grade' => GRADE_TYPE_NONE
            ]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', array(
            'id' => $assign->get_course_module()->id,
            'action' => 'grading',
        )));

        // Render the table with the requires grading filter.
        $gradingtable = new \assign_grading_table($assign, 1, ASSIGN_FILTER_REQUIRE_GRADING, 0, true);
        $output = $assign->get_renderer()->render($gradingtable);

        // Test that the filter function does not throw errors for assignments with no grade.
        $this->assertStringContainsString(get_string('nothingtodisplay'), $output);
    }


    /**
     * Test submissions with extension date.
     */
    public function test_gradingtable_extension_due_date() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Setup the assignment.
        $this->setUser($teacher);
        $time = time();
        $assign = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
                'duedate' => time() - (4 * DAYSECS),
            ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', array(
            'id' => $assign->get_course_module()->id,
            'action' => 'grading',
        )));

        // Check that the assignment is late.
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_', 'assign'), $output);
        $this->assertStringContainsString(get_string('overdue', 'assign', format_time((4 * DAYSECS))), $output);

        // Grant an extension.
        $extendedtime = $time + (2 * DAYSECS);
        $assign->testable_save_user_extension($student->id, $extendedtime);
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_', 'assign'), $output);
        $this->assertStringContainsString(get_string('userextensiondate', 'assign', userdate($extendedtime)), $output);

        // Simulate a submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Verify output.
        $this->setUser($teacher);
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_submitted', 'assign'), $output);
        $this->assertStringContainsString(get_string('userextensiondate', 'assign', userdate($extendedtime)), $output);
    }

    /**
     * Test that late submissions with extension date calculate correctly.
     */
    public function test_gradingtable_extension_date_calculation_for_lateness() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Setup the assignment.
        $this->setUser($teacher);
        $time = time();
        $assign = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
                'duedate' => time() - (4 * DAYSECS),
            ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', array(
            'id' => $assign->get_course_module()->id,
            'action' => 'grading',
        )));

        // Check that the assignment is late.
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_', 'assign'), $output);
        $difftime = time() - $time;
        $this->assertStringContainsString(get_string('overdue', 'assign', format_time((4 * DAYSECS) + $difftime)), $output);

        // Grant an extension that is in the past.
        $assign->testable_save_user_extension($student->id, $time - (2 * DAYSECS));
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_', 'assign'), $output);
        $this->assertStringContainsString(get_string('userextensiondate', 'assign', userdate($time - (2 * DAYSECS))), $output);
        $difftime = time() - $time;
        $this->assertStringContainsString(get_string('overdue', 'assign', format_time((2 * DAYSECS) + $difftime)), $output);

        // Simulate a submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);
        $submittedtime = time();

        // Verify output.
        $this->setUser($teacher);
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_submitted', 'assign'), $output);
        $this->assertStringContainsString(get_string('userextensiondate', 'assign', userdate($time - (2 * DAYSECS))), $output);

        $difftime = $submittedtime - $time;
        $this->assertStringContainsString(get_string('submittedlateshort', 'assign', format_time((2 * DAYSECS) + $difftime)),
            $output);
    }

    public function test_gradingtable_status_rendering() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Setup the assignment.
        $this->setUser($teacher);
        $time = time();
        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'duedate' => $time - (4 * DAYSECS),
         ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', array(
            'id' => $assign->get_course_module()->id,
            'action' => 'grading',
        )));

        // Check that the assignment is late.
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_', 'assign'), $output);
        $difftime = time() - $time;
        $this->assertStringContainsString(get_string('overdue', 'assign', format_time((4 * DAYSECS) + $difftime)), $output);

        // Simulate a student viewing the assignment without submitting.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_NEW;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $submittedtime = time();

        // Verify output.
        $this->setUser($teacher);
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $difftime = $submittedtime - $time;
        $this->assertStringContainsString(get_string('overdue', 'assign', format_time((4 * DAYSECS) + $difftime)), $output);

        $document = new \DOMDocument();
        @$document->loadHTML($output);
        $xpath = new \DOMXPath($document);
        $this->assertEmpty($xpath->evaluate('string(//td[@id="mod_assign_grading-' . $assign->get_context()->id. '_r0_c8"])'));
    }

    /**
     * Check that group submission information is rendered correctly in the
     * grading table.
     */
    public function test_gradingtable_group_submissions_rendering() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        groups_add_member($group, $teacher);

        $students = [];

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $students[] = $student;
        groups_add_member($group, $student);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $students[] = $student;
        groups_add_member($group, $student);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $students[] = $student;
        groups_add_member($group, $student);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $students[] = $student;
        groups_add_member($group, $student);

        // Verify group assignments.
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
            'requireallteammemberssubmit' => 0,
        ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', array(
            'id' => $assign->get_course_module()->id,
            'action' => 'grading',
        )));

        // Add a submission.
        $this->setUser($student);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $notices = array();
        $assign->save_submission($data, $notices);

        $submission = $assign->get_group_submission($student->id, 0, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, true);

        // Check output.
        $this->setUser($teacher);
        $gradingtable = new \assign_grading_table($assign, 4, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $document = new \DOMDocument();
        @$document->loadHTML($output);
        $xpath = new \DOMXPath($document);

        // The XPath expression is based on the unique ID of the table.
        $xpathuniqueidroot = 'mod_assign_grading-' . $assign->get_context()->id;

        // Check status.
        $this->assertSame(get_string('submissionstatus_submitted', 'assign'),
            $xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r0_c4"]/div[@class="submissionstatussubmitted"])'));
        $this->assertSame(get_string('submissionstatus_submitted', 'assign'),
            $xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r3_c4"]/div[@class="submissionstatussubmitted"])'));

        // Check submission last modified date.
        $this->assertGreaterThan(0, strtotime($xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r0_c8"])')));
        $this->assertGreaterThan(0, strtotime($xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r3_c8"])')));

        // Check group.
        $this->assertSame($group->name, $xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r0_c5"])'));
        $this->assertSame($group->name, $xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r3_c5"])'));

        // Check submission text.
        $this->assertSame('Submission text', $xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r0_c9"]/div/div)'));
        $this->assertSame('Submission text', $xpath->evaluate('string(//td[@id="' . $xpathuniqueidroot . '_r3_c9"]/div/div)'));

        // Check comments can be made.
        $this->assertEquals(1, $xpath->evaluate('count(//td[@id="' . $xpathuniqueidroot . '_r0_c10"]//textarea)'));
        $this->assertEquals(1, $xpath->evaluate('count(//td[@id="' . $xpathuniqueidroot . '_r3_c10"]//textarea)'));
    }

    public function test_show_intro() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        // Test whether we are showing the intro at the correct times.
        $this->setUser($teacher);
        $assign = $this->create_instance($course, ['alwaysshowdescription' => 1]);

        $this->assertEquals(true, $assign->testable_show_intro());

        $tomorrow = time() + DAYSECS;

        $assign = $this->create_instance($course, [
                'alwaysshowdescription' => 0,
                'allowsubmissionsfromdate' => $tomorrow,
            ]);
        $this->assertEquals(false, $assign->testable_show_intro());
        $yesterday = time() - DAYSECS;
        $assign = $this->create_instance($course, [
                'alwaysshowdescription' => 0,
                'allowsubmissionsfromdate' => $yesterday,
            ]);
        $this->assertEquals(true, $assign->testable_show_intro());
    }

    public function test_has_submissions_or_grades() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);
        $instance = $assign->get_instance();

        // Should start empty.
        $this->assertEquals(false, $assign->has_submissions_or_grades());

        // Simulate a submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);

        // The submission is still new.
        $this->assertEquals(false, $assign->has_submissions_or_grades());

        // Submit the submission.
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Now test again.
        $this->assertEquals(true, $assign->has_submissions_or_grades());
    }

    public function test_delete_grades() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course);

        // Simulate adding a grade.
        $this->setUser($teacher);
        $data = new \stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $student->id, 0);

        // Now see if the data is in the gradebook.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id);

        $this->assertNotEquals(0, count($gradinginfo->items));

        $assign->testable_delete_grades();
        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id);

        $this->assertEquals(0, count($gradinginfo->items));
    }

    public function test_delete_instance() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        // Simulate adding a grade.
        $this->setUser($teacher);
        $data = new \stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $student->id, 0);

        // Simulate a submission.
        $this->add_submission($student, $assign);

        // Now try and delete.
        $this->setUser($teacher);
        $this->assertEquals(true, $assign->delete_instance());
    }

    public function test_reset_userdata() {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $now = time();
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
                'duedate' => $now,
            ]);

        // Simulate adding a grade.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0);

        // Simulate a submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $data = new \stdClass();
        $data->onlinetext_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        $this->assertEquals(true, $assign->has_submissions_or_grades());
        // Now try and reset.
        $data = new \stdClass();
        $data->reset_assign_submissions = 1;
        $data->reset_gradebook_grades = 1;
        $data->reset_assign_user_overrides = 1;
        $data->reset_assign_group_overrides = 1;
        $data->courseid = $course->id;
        $data->timeshift = DAYSECS;
        $this->setUser($teacher);
        $assign->reset_userdata($data);
        $this->assertEquals(false, $assign->has_submissions_or_grades());

        // Reload the instance data.
        $instance = $DB->get_record('assign', array('id' => $assign->get_instance()->id));
        $this->assertEquals($now + DAYSECS, $instance->duedate);

        // Test reset using assign_reset_userdata().
        $assignduedate = $instance->duedate; // Keep old updated value for comparison.
        $data->timeshift = (2 * DAYSECS);
        assign_reset_userdata($data);
        $instance = $DB->get_record('assign', array('id' => $assign->get_instance()->id));
        $this->assertEquals($assignduedate + (2 * DAYSECS), $instance->duedate);

        // Create one more assignment and reset, make sure time shifted for previous assignment is not changed.
        $assign2 = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
                'duedate' => $now,
            ]);
        $assignduedate = $instance->duedate;
        $data->timeshift = 3 * DAYSECS;
        $assign2->reset_userdata($data);
        $instance = $DB->get_record('assign', array('id' => $assign->get_instance()->id));
        $this->assertEquals($assignduedate, $instance->duedate);
        $instance2 = $DB->get_record('assign', array('id' => $assign2->get_instance()->id));
        $this->assertEquals($now + 3 * DAYSECS, $instance2->duedate);

        // Reset both assignments using assign_reset_userdata() and make sure both assignments have same date.
        $assignduedate = $instance->duedate;
        $assign2duedate = $instance2->duedate;
        $data->timeshift = (4 * DAYSECS);
        assign_reset_userdata($data);
        $instance = $DB->get_record('assign', array('id' => $assign->get_instance()->id));
        $this->assertEquals($assignduedate + (4 * DAYSECS), $instance->duedate);
        $instance2 = $DB->get_record('assign', array('id' => $assign2->get_instance()->id));
        $this->assertEquals($assign2duedate + (4 * DAYSECS), $instance2->duedate);
    }

    public function test_plugin_settings() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $now = time();
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
                'assignsubmission_file_enabled' => 1,
                'assignsubmission_file_maxfiles' => 12,
                'assignsubmission_file_maxsizebytes' => 10,
            ]);

        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertEquals('12', $plugin->get_config('maxfilesubmissions'));
    }

    public function test_update_calendar() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $this->setUser($teacher);
        $userctx = \context_user::instance($teacher->id)->id;

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

        // Create a new \assignment with links to a draft area.
        $now = time();
        $assign = $this->create_instance($course, [
                'duedate' => $now,
                'intro' => $fakearealink1,
                'introformat' => FORMAT_HTML
            ]);

        // See if there is an event in the calendar.
        $params = array('modulename' => 'assign', 'instance' => $assign->get_instance()->id);
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

        // Create an assignment with a description that should be hidden.
        $assign = $this->create_instance($course, [
                'duedate' => $now + 160,
                'alwaysshowdescription' => false,
                'allowsubmissionsfromdate' => $now + 60,
                'intro' => 'Some text',
            ]);

        // Get the event from the calendar.
        $params = array('modulename' => 'assign', 'instance' => $assign->get_instance()->id);
        $event = $DB->get_record('event', [
            'modulename' => 'assign',
            'instance' => $assign->get_instance()->id,
        ]);

        $this->assertEmpty($event->description);

        // Change the allowsubmissionfromdate to the past - do this directly in the DB
        // because if we call the assignment update method - it will update the calendar
        // and we want to test that this works from cron.
        $DB->set_field('assign', 'allowsubmissionsfromdate', $now - 60, array('id' => $assign->get_instance()->id));
        // Run cron to update the event in the calendar.
        \assign::cron();
        $event = $DB->get_record('event', $params);

        $this->assertStringContainsString('Some text', $event->description);

    }

    public function test_update_instance() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $this->setUser($teacher);
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);

        $now = time();
        $instance = $assign->get_instance();
        $instance->duedate = $now;
        $instance->instance = $instance->id;
        $instance->assignsubmission_onlinetext_enabled = 1;

        $assign->update_instance($instance);

        $instance = $DB->get_record('assign', ['id' => $assign->get_instance()->id]);
        $this->assertEquals($now, $instance->duedate);
    }

    public function test_cannot_submit_empty() {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, ['submissiondrafts' => 1]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Test you cannot see the submit button for an offline assignment regardless.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertStringNotContainsString(get_string('submitassignment', 'assign'),
            $output, 'Can submit empty offline assignment');
    }

    public function test_cannot_submit_empty_no_submission() {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'submissiondrafts' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Test you cannot see the submit button for an online text assignment with no submission.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertStringNotContainsString(get_string('submitassignment', 'assign'),
            $output, 'Cannot submit empty onlinetext assignment');
    }

    public function test_can_submit_with_submission() {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'submissiondrafts' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Add a draft.
        $this->add_submission($student, $assign);

        // Test you can see the submit button for an online text assignment with a submission.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertStringContainsString(get_string('submitassignment', 'assign'),
            $output, 'Can submit non empty onlinetext assignment');
    }

    /**
     * Test new_submission_empty
     *
     * We only test combinations of plugins here. Individual plugins are tested
     * in their respective test files.
     *
     * @dataProvider new_submission_empty_testcases
     * @param string $data The file submission data
     * @param bool $expected The expected return value
     */
    public function test_new_submission_empty($data, $expected) {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
                'assignsubmission_file_enabled' => 1,
                'assignsubmission_file_maxfiles' => 12,
                'assignsubmission_file_maxsizebytes' => 10,
                'assignsubmission_onlinetext_enabled' => 1,
            ]);
        $this->setUser($student);
        $submission = new \stdClass();

        if ($data['file'] && isset($data['file']['filename'])) {
            $itemid = file_get_unused_draft_itemid();
            $submission->files_filemanager = $itemid;
            $data['file'] += ['contextid' => \context_user::instance($student->id)->id, 'itemid' => $itemid];
            $fs = get_file_storage();
            $fs->create_file_from_string((object)$data['file'], 'Content of ' . $data['file']['filename']);
        }

        if ($data['onlinetext']) {
            $submission->onlinetext_editor = ['text' => $data['onlinetext']];
        }

        $result = $assign->new_submission_empty($submission);
        $this->assertTrue($result === $expected);
    }

    /**
     * Dataprovider for the test_new_submission_empty testcase
     *
     * @return array of testcases
     */
    public function new_submission_empty_testcases() {
        return [
            'With file and onlinetext' => [
                [
                    'file' => [
                        'component' => 'user',
                        'filearea' => 'draft',
                        'filepath' => '/',
                        'filename' => 'not_a_virus.exe'
                    ],
                    'onlinetext' => 'Balin Fundinul Uzbadkhazaddumu'
                ],
                false
            ]
        ];
    }

    public function test_list_participants() {
        global $CFG;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        // Create 10 students.
        for ($i = 0; $i < 10; $i++) {
            $this->getDataGenerator()->create_and_enrol($course, 'student');
        }

        $this->setUser($teacher);
        $assign = $this->create_instance($course, ['grade' => 100]);

        $this->assertCount(10, $assign->list_participants(null, true));
    }

    public function test_list_participants_activeenrol() {
        global $CFG, $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        // Create 10 students.
        for ($i = 0; $i < 10; $i++) {
            $this->getDataGenerator()->create_and_enrol($course, 'student');
        }

        // Create 10 suspended students.
        for ($i = 0; $i < 10; $i++) {
            $this->getDataGenerator()->create_and_enrol($course, 'student', null, 'manual', 0, 0, ENROL_USER_SUSPENDED);
        }

        $this->setUser($teacher);
        set_user_preference('grade_report_showonlyactiveenrol', false);
        $assign = $this->create_instance($course, ['grade' => 100]);

        $this->assertCount(10, $assign->list_participants(null, true));
    }

    public function test_list_participants_with_group_restriction() {
        global $CFG;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $unrelatedstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Turn on availability and a group restriction, and check that it doesn't show users who aren't in the group.
        $CFG->enableavailability = true;

        $specialgroup = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $assign = $this->create_instance($course, [
            'grade' => 100,
            'availability' => json_encode(
                \core_availability\tree::get_root_json([\availability_group\condition::get_json($specialgroup->id)])
            ),
        ]);

        groups_add_member($specialgroup, $student);
        groups_add_member($specialgroup, $otherstudent);
        $this->assertEquals(2, count($assign->list_participants(null, true)));
    }

    public function test_get_participant_user_not_exist() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $assign = $this->create_instance($course);
        $this->assertNull($assign->get_participant('-1'));
    }

    public function test_get_participant_not_enrolled() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);

        $user = $this->getDataGenerator()->create_user();
        $this->assertNull($assign->get_participant($user->id));
    }

    public function test_get_participant_no_submission() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $participant = $assign->get_participant($student->id);

        $this->assertEquals($student->id, $participant->id);
        $this->assertFalse($participant->submitted);
        $this->assertFalse($participant->requiregrading);
        $this->assertFalse($participant->grantedextension);
    }

    public function test_get_participant_granted_extension() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign->save_user_extension($student->id, time());
        $participant = $assign->get_participant($student->id);

        $this->assertEquals($student->id, $participant->id);
        $this->assertFalse($participant->submitted);
        $this->assertFalse($participant->requiregrading);
        $this->assertTrue($participant->grantedextension);
    }

    public function test_get_participant_with_ungraded_submission() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Simulate a submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        $participant = $assign->get_participant($student->id);

        $this->assertEquals($student->id, $participant->id);
        $this->assertTrue($participant->submitted);
        $this->assertTrue($participant->requiregrading);
        $this->assertFalse($participant->grantedextension);
    }

    /**
     * Tests that if a student with no submission who can no longer submit is not a participant.
     */
    public function test_get_participant_with_no_submission_no_capability() {
        global $DB;
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $assign = $this->create_instance($course);
        $teacher = self::getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = self::getDataGenerator()->create_and_enrol($course, 'student');

        // Remove the students capability to submit.
        $role = $DB->get_field('role', 'id', ['shortname' => 'student']);
        assign_capability('mod/assign:submit', CAP_PROHIBIT, $role, $coursecontext);

        $participant = $assign->get_participant($student->id);

        self::assertNull($participant);
    }

    /**
     * Tests that if a student that has submitted but can no longer submit is a participant.
     */
    public function test_get_participant_with_submission_no_capability() {
        global $DB;
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $assign = $this->create_instance($course);
        $teacher = self::getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = self::getDataGenerator()->create_and_enrol($course, 'student');

        // Simulate a submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Remove the students capability to submit.
        $role = $DB->get_field('role', 'id', ['shortname' => 'student']);
        assign_capability('mod/assign:submit', CAP_PROHIBIT, $role, $coursecontext);

        $participant = $assign->get_participant($student->id);

        self::assertNotNull($participant);
        self::assertEquals($student->id, $participant->id);
        self::assertTrue($participant->submitted);
        self::assertTrue($participant->requiregrading);
        self::assertFalse($participant->grantedextension);
    }

    public function test_get_participant_with_graded_submission() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Simulate a submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        $this->mark_submission($teacher, $assign, $student, 50.0);

        $data = new \stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $student->id, 0);

        $participant = $assign->get_participant($student->id);

        $this->assertEquals($student->id, $participant->id);
        $this->assertTrue($participant->submitted);
        $this->assertFalse($participant->requiregrading);
        $this->assertFalse($participant->grantedextension);
    }

    /**
     * No active group and non-group submissions disallowed => 2 groups.
     */
    public function test_count_teams_no_active_non_group_allowed() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group1, $student1);
        groups_add_member($group2, $student2);

        $this->setUser($teacher);
        $assign = $this->create_instance($course, ['teamsubmission' => 1]);

        $this->assertEquals(2, $assign->count_teams());
    }

    /**
     * No active group and non group submissions allowed => 2 groups + the default one.
     */
    public function test_count_teams_non_group_allowed() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group1->id, 'groupingid' => $grouping->id));
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group2->id, 'groupingid' => $grouping->id));

        groups_add_member($group1, $student1);
        groups_add_member($group2, $student2);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'teamsubmissiongroupingid' => $grouping->id,
            'preventsubmissionnotingroup' => false,
        ]);

        $this->setUser($teacher);
        $this->assertEquals(3, $assign->count_teams());

        // Active group only.
        $this->assertEquals(1, $assign->count_teams($group1->id));
        $this->assertEquals(1, $assign->count_teams($group2->id));
    }

    /**
     * Active group => just selected one.
     */
    public function test_count_teams_no_active_group() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group1->id, 'groupingid' => $grouping->id));
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group2->id, 'groupingid' => $grouping->id));

        groups_add_member($group1, $student1);
        groups_add_member($group2, $student2);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'preventsubmissionnotingroup' => true,
        ]);

        $this->setUser($teacher);
        $this->assertEquals(2, $assign->count_teams());

        // Active group only.
        $this->assertEquals(1, $assign->count_teams($group1->id));
        $this->assertEquals(1, $assign->count_teams($group2->id));
    }

    /**
     * Active group => just selected one.
     */
    public function test_count_teams_groups_only() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'teamsubmissiongroupingid' => $grouping->id,
            'preventsubmissionnotingroup' => false,
        ]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group1, $student1);

        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group2, $student2);

        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group1->id, 'groupingid' => $grouping->id));
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group2->id, 'groupingid' => $grouping->id));

        $this->setUser($teacher);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'preventsubmissionnotingroup' => true,
        ]);
        $this->assertEquals(2, $assign->count_teams());
    }

    public function test_submit_to_default_group() {
        global $DB, $SESSION;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(['courseid' => $course->id]);
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 0,
            'groupmode' => VISIBLEGROUPS,
        ]);

        $usergroup = $assign->get_submission_group($student->id);
        $this->assertFalse($usergroup, 'New student is in default group');

        // Add a submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Set active groups to all groups.
        $this->setUser($teacher);
        $SESSION->activegroup[$course->id]['aag'][0] = 0;
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));

        // Set an active group.
        $SESSION->activegroup[$course->id]['aag'][0] = (int) $group->id;
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
    }

    public function test_count_submissions_no_draft() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $assign->get_user_submission($student->id, true);

        // Note: Drafts count as a submission.
        $this->assertEquals(0, $assign->count_grades());
        $this->assertEquals(0, $assign->count_submissions());
        $this->assertEquals(1, $assign->count_submissions(true));
        $this->assertEquals(0, $assign->count_submissions_need_grading());
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_NEW));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_REOPENED));
    }

    public function test_count_submissions_draft() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $this->add_submission($student, $assign);

        // Note: Drafts count as a submission.
        $this->assertEquals(0, $assign->count_grades());
        $this->assertEquals(1, $assign->count_submissions());
        $this->assertEquals(1, $assign->count_submissions(true));
        $this->assertEquals(0, $assign->count_submissions_need_grading());
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_NEW));
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_REOPENED));
    }

    public function test_count_submissions_submitted() {
        global $SESSION;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        $this->assertEquals(0, $assign->count_grades());
        $this->assertEquals(1, $assign->count_submissions());
        $this->assertEquals(1, $assign->count_submissions(true));
        $this->assertEquals(1, $assign->count_submissions_need_grading());
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_NEW));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT));
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_REOPENED));
    }

    public function test_count_submissions_graded() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0);

        // Although it has been graded, it is still marked as submitted.
        $this->assertEquals(1, $assign->count_grades());
        $this->assertEquals(1, $assign->count_submissions());
        $this->assertEquals(1, $assign->count_submissions(true));
        $this->assertEquals(0, $assign->count_submissions_need_grading());
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_NEW));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT));
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_REOPENED));
    }

    public function test_count_submissions_graded_group() {
        global $SESSION;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $othergroup = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group, $student);

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'groupmode' => VISIBLEGROUPS,
        ]);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // The user should still be listed when fetching all groups.
        $this->setUser($teacher);
        $SESSION->activegroup[$course->id]['aag'][0] = 0;
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));

        // The user should still be listed when fetching just their group.
        $SESSION->activegroup[$course->id]['aag'][0] = $group->id;
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));

        // The user should still be listed when fetching just their group.
        $SESSION->activegroup[$course->id]['aag'][0] = $othergroup->id;
        $this->assertEquals(0, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
    }

    // TODO
    public function x_test_count_submissions_for_team() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $othergroup = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group, $student);

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'teamsubmission' => 1,
        ]);

        // Add a graded submission.
        $this->add_submission($student, $assign);

        // Simulate adding a grade.
        $this->setUser($teacher);
        $data = new \stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->extrastudents[0]->id, 0);

        // Simulate a submission.
        $this->setUser($this->extrastudents[1]);
        $submission = $assign->get_group_submission($this->extrastudents[1]->id, $groupid, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[1]->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate a submission.
        $this->setUser($this->extrastudents[2]);
        $submission = $assign->get_group_submission($this->extrastudents[2]->id, $groupid, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[2]->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate a submission.
        $this->setUser($this->extrastudents[3]);
        $submission = $assign->get_group_submission($this->extrastudents[3]->id, $groupid, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $this->extrastudents[3]->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE);
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Simulate adding a grade.
        $this->setUser($teacher);
        $data = new \stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $this->extrastudents[3]->id, 0);
        $assign->testable_apply_grade_to_user($data, $this->extrasuspendedstudents[0]->id, 0);

        // Create a new submission with status NEW.
        $this->setUser($this->extrastudents[4]);
        $submission = $assign->get_group_submission($this->extrastudents[4]->id, $groupid, true);

        $this->assertEquals(2, $assign->count_grades());
        $this->assertEquals(4, $assign->count_submissions());
        $this->assertEquals(5, $assign->count_submissions(true));
        $this->assertEquals(3, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED));
        $this->assertEquals(1, $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT));
    }

    public function test_get_grading_userid_list_only_active() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $suspendedstudent = $this->getDataGenerator()->create_and_enrol(
            $course, 'student', null, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        $this->setUser($teacher);

        $assign = $this->create_instance($course);
        $this->assertCount(1, $assign->testable_get_grading_userid_list());
    }

    public function test_get_grading_userid_list_all() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $suspendedstudent = $this->getDataGenerator()->create_and_enrol(
            $course, 'student', null, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        $this->setUser($teacher);
        set_user_preference('grade_report_showonlyactiveenrol', false);

        $assign = $this->create_instance($course);
        $this->assertCount(2, $assign->testable_get_grading_userid_list());
    }

    public function test_cron() {
        global $PAGE;
        $this->resetAfterTest();

        // First run cron so there are no messages waiting to be sent (from other tests).
        \core\cron::setup_user();
        \assign::cron();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now create an assignment and add some feedback.
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'sendstudentnotifications' => 1,
        ]);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0);

        $this->expectOutputRegex('/Done processing 1 assignment submissions/');
        \core\cron::setup_user();
        $sink = $this->redirectMessages();
        \assign::cron();
        $messages = $sink->get_messages();

        $this->assertEquals(1, count($messages));
        $this->assertEquals(1, $messages[0]->notification);
        $this->assertEquals($assign->get_instance()->name, $messages[0]->contexturlname);
        // Test customdata.
        $customdata = json_decode($messages[0]->customdata);
        $this->assertEquals($assign->get_course_module()->id, $customdata->cmid);
        $this->assertEquals($assign->get_instance()->id, $customdata->instance);
        $this->assertEquals('feedbackavailable', $customdata->messagetype);
        $userpicture = new \user_picture($teacher);
        $userpicture->size = 1; // Use f1 size.
        $this->assertEquals($userpicture->get_url($PAGE)->out(false), $customdata->notificationiconurl);
        $this->assertEquals(0, $customdata->uniqueidforuser);   // Not used in this case.
        $this->assertFalse($customdata->blindmarking);
    }

    public function test_cron_without_notifications() {
        $this->resetAfterTest();

        // First run cron so there are no messages waiting to be sent (from other tests).
        \core\cron::setup_user();
        \assign::cron();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now create an assignment and add some feedback.
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'sendstudentnotifications' => 1,
        ]);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0, [
            'sendstudentnotifications' => 0,
        ]);

        \core\cron::setup_user();
        $sink = $this->redirectMessages();
        \assign::cron();
        $messages = $sink->get_messages();

        $this->assertEquals(0, count($messages));
    }

    public function test_cron_regraded() {
        $this->resetAfterTest();

        // First run cron so there are no messages waiting to be sent (from other tests).
        \core\cron::setup_user();
        \assign::cron();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now create an assignment and add some feedback.
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'sendstudentnotifications' => 1,
        ]);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0);

        $this->expectOutputRegex('/Done processing 1 assignment submissions/');
        \core\cron::setup_user();
        \assign::cron();

        // Regrade.
        $this->mark_submission($teacher, $assign, $student, 50.0);

        $this->expectOutputRegex('/Done processing 1 assignment submissions/');
        \core\cron::setup_user();
        $sink = $this->redirectMessages();
        \assign::cron();
        $messages = $sink->get_messages();

        $this->assertEquals(1, count($messages));
        $this->assertEquals(1, $messages[0]->notification);
        $this->assertEquals($assign->get_instance()->name, $messages[0]->contexturlname);
    }

    /**
     * Test delivery of grade notifications as controlled by marking workflow.
     */
    public function test_markingworkflow_cron() {
        $this->resetAfterTest();

        // First run cron so there are no messages waiting to be sent (from other tests).
        \core\cron::setup_user();
        \assign::cron();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now create an assignment and add some feedback.
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'sendstudentnotifications' => 1,
            'markingworkflow' => 1,
        ]);

        // Mark a submission but set the workflowstate to an unreleased state.
        // This should not trigger a notification.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0, [
            'sendstudentnotifications' => 1,
            'workflowstate' => ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE,
        ]);

        \core\cron::setup_user();
        $sink = $this->redirectMessages();
        \assign::cron();
        $messages = $sink->get_messages();

        $this->assertEquals(0, count($messages));

        // Transition to the released state.
        $this->setUser($teacher);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
        $assign->testable_apply_grade_to_user($submission, $student->id, 0);

        // Now run cron and see that one message was sent.
        \core\cron::setup_user();
        $sink = $this->redirectMessages();
        $this->expectOutputRegex('/Done processing 1 assignment submissions/');
        \assign::cron();
        $messages = $sink->get_messages();

        $this->assertEquals(1, count($messages));
        $this->assertEquals(1, $messages[0]->notification);
        $this->assertEquals($assign->get_instance()->name, $messages[0]->contexturlname);
    }

    public function test_cron_message_includes_courseid() {
        $this->resetAfterTest();

        // First run cron so there are no messages waiting to be sent (from other tests).
        \core\cron::setup_user();
        \assign::cron();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now create an assignment and add some feedback.
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'sendstudentnotifications' => 1,
        ]);

        // Mark a submission but set the workflowstate to an unreleased state.
        // This should not trigger a notification.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student);
        \phpunit_util::stop_message_redirection();

        // Now run cron and see that one message was sent.
        \core\cron::setup_user();
        $this->preventResetByRollback();
        $sink = $this->redirectEvents();
        $this->expectOutputRegex('/Done processing 1 assignment submissions/');
        \assign::cron();

        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\notification_sent', $event);
        $this->assertEquals($assign->get_course()->id, $event->other['courseid']);
        $sink->close();
    }

    public function test_is_graded() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course);

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, 50.0);

        $this->setUser($teacher);
        $this->assertEquals(true, $assign->testable_is_graded($student->id));
        $this->assertEquals(false, $assign->testable_is_graded($otherstudent->id));
    }

    public function test_can_grade() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course);

        $this->setUser($student);
        $this->assertEquals(false, $assign->can_grade());

        $this->setUser($teacher);
        $this->assertEquals(true, $assign->can_grade());

        // Test the viewgrades capability for other users.
        $this->setUser();
        $this->assertTrue($assign->can_grade($teacher->id));
        $this->assertFalse($assign->can_grade($student->id));

        // Test the viewgrades capability - without mod/assign:grade.
        $this->setUser($student);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        assign_capability('mod/assign:viewgrades', CAP_ALLOW, $studentrole->id, $assign->get_context()->id);
        $this->assertEquals(false, $assign->can_grade());
    }

    public function test_can_view_submission() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $suspendedstudent = $this->getDataGenerator()->create_and_enrol(
            $course, 'student', null, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        $assign = $this->create_instance($course);

        $this->setUser($student);
        $this->assertEquals(true, $assign->can_view_submission($student->id));
        $this->assertEquals(false, $assign->can_view_submission($otherstudent->id));
        $this->assertEquals(false, $assign->can_view_submission($teacher->id));

        $this->setUser($teacher);
        $this->assertEquals(true, $assign->can_view_submission($student->id));
        $this->assertEquals(true, $assign->can_view_submission($otherstudent->id));
        $this->assertEquals(true, $assign->can_view_submission($teacher->id));
        $this->assertEquals(false, $assign->can_view_submission($suspendedstudent->id));

        $this->setUser($editingteacher);
        $this->assertEquals(true, $assign->can_view_submission($student->id));
        $this->assertEquals(true, $assign->can_view_submission($otherstudent->id));
        $this->assertEquals(true, $assign->can_view_submission($teacher->id));
        $this->assertEquals(true, $assign->can_view_submission($suspendedstudent->id));

        // Test the viewgrades capability - without mod/assign:grade.
        $this->setUser($student);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        assign_capability('mod/assign:viewgrades', CAP_ALLOW, $studentrole->id, $assign->get_context()->id);
        $this->assertEquals(true, $assign->can_view_submission($student->id));
        $this->assertEquals(true, $assign->can_view_submission($otherstudent->id));
        $this->assertEquals(true, $assign->can_view_submission($teacher->id));
        $this->assertEquals(false, $assign->can_view_submission($suspendedstudent->id));
    }

    public function test_update_submission() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course);

        $this->add_submission($student, $assign);
        $submission = $assign->get_user_submission($student->id, 0);
        $assign->testable_update_submission($submission, $student->id, true, true);

        $this->setUser($teacher);

        // Verify the gradebook update.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $student->id);

        $this->assertTrue(isset($gradinginfo->items[0]->grades[$student->id]));
        $this->assertEquals($student->id, $gradinginfo->items[0]->grades[$student->id]->usermodified);
    }

    public function test_update_submission_team() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $otherstudent);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
        ]);

        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $student->id);
        $this->assertTrue(isset($gradinginfo->items[0]->grades[$student->id]));
        $this->assertNull($gradinginfo->items[0]->grades[$student->id]->usermodified);

        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $otherstudent->id);
        $this->asserttrue(isset($gradinginfo->items[0]->grades[$otherstudent->id]));
        $this->assertNull($gradinginfo->items[0]->grades[$otherstudent->id]->usermodified);

        $this->add_submission($student, $assign);
        $submission = $assign->get_group_submission($student->id, 0, true);
        $assign->testable_update_submission($submission, $student->id, true, true);

        // Verify the gradebook update for the student.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $student->id);

        $this->assertTrue(isset($gradinginfo->items[0]->grades[$student->id]));
        $this->assertEquals($student->id, $gradinginfo->items[0]->grades[$student->id]->usermodified);

        // Verify the gradebook update for the other student.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $otherstudent->id);

        $this->assertTrue(isset($gradinginfo->items[0]->grades[$otherstudent->id]));
        $this->assertEquals($otherstudent->id, $gradinginfo->items[0]->grades[$otherstudent->id]->usermodified);
    }

    public function test_update_submission_suspended() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student', null, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        $assign = $this->create_instance($course);

        $this->add_submission($student, $assign);
        $submission = $assign->get_user_submission($student->id, 0);
        $assign->testable_update_submission($submission, $student->id, true, false);

        $this->setUser($teacher);

        // Verify the gradebook update.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $student->id);

        $this->assertTrue(isset($gradinginfo->items[0]->grades[$student->id]));
        $this->assertEquals($student->id, $gradinginfo->items[0]->grades[$student->id]->usermodified);
    }

    public function test_update_submission_blind() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'blindmarking' => 1,
        ]);

        $this->add_submission($student, $assign);
        $submission = $assign->get_user_submission($student->id, 0);
        $assign->testable_update_submission($submission, $student->id, true, false);

        // Verify the gradebook update.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $student->id);

        // The usermodified is not set because this is blind marked.
        $this->assertTrue(isset($gradinginfo->items[0]->grades[$student->id]));
        $this->assertNull($gradinginfo->items[0]->grades[$student->id]->usermodified);
    }

    public function test_group_submissions_submit_for_marking_requireallteammemberssubmit() {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $otherstudent);

        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
            'requireallteammemberssubmit' => 1,
        ]);

        // Now verify group assignments.
        $this->setUser($teacher);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Add a submission.
        $this->add_submission($student, $assign);

        // Check we can see the submit button.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertStringContainsString(get_string('submitassignment', 'assign'), $output);

        $submission = $assign->get_group_submission($student->id, 0, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, true);

        // Check that the student does not see "Submit" button.
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertStringNotContainsString(get_string('submitassignment', 'assign'), $output);

        // Change to another user in the same group.
        $this->setUser($otherstudent);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $otherstudent);
        $this->assertStringContainsString(get_string('submitassignment', 'assign'), $output);

        $submission = $assign->get_group_submission($otherstudent->id, 0, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $otherstudent->id, true, true);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $otherstudent);
        $this->assertStringNotContainsString(get_string('submitassignment', 'assign'), $output);
    }

    public function test_group_submissions_submit_for_marking() {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group, $otherstudent);

        // Now verify group assignments.
        $this->setUser($teacher);
        $time = time();
        $assign = $this->create_instance($course, [
            'teamsubmission' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
            'requireallteammemberssubmit' => 0,
            'duedate' => $time - (2 * DAYSECS),
        ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Add a submission.
        $this->add_submission($student, $assign);

        // Check we can see the submit button.
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertStringContainsString(get_string('submitassignment', 'assign'), $output);
        $output = $assign->view_student_summary($student, true);
        $this->assertStringContainsString(get_string('timeremaining', 'assign'), $output);
        $difftime = time() - $time;
        $this->assertStringContainsString(get_string('overdue', 'assign', format_time((2 * DAYSECS) + $difftime)), $output);

        $submission = $assign->get_group_submission($student->id, 0, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, true);

        // Check that the student does not see "Submit" button.
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertStringNotContainsString(get_string('submitassignment', 'assign'), $output);

        // Change to another user in the same group.
        $this->setUser($otherstudent);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $otherstudent);
        $this->assertStringNotContainsString(get_string('submitassignment', 'assign'), $output);

        // Check that time remaining is not overdue.
        $output = $assign->view_student_summary($otherstudent, true);
        $this->assertStringContainsString(get_string('timeremaining', 'assign'), $output);
        $difftime = time() - $time;
        $this->assertStringContainsString(get_string('submittedlate', 'assign', format_time((2 * DAYSECS) + $difftime)), $output);

        $submission = $assign->get_group_submission($otherstudent->id, 0, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $otherstudent->id, true, true);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $otherstudent);
        $this->assertStringNotContainsString(get_string('submitassignment', 'assign'), $output);
    }

    public function test_submissions_open() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $suspendedstudent = $this->getDataGenerator()->create_and_enrol(
            $course, 'student', null, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        $this->setAdminUser();

        $now = time();
        $tomorrow = $now + DAYSECS;
        $oneweek = $now + WEEKSECS;
        $yesterday = $now - DAYSECS;

        $assign = $this->create_instance($course);
        $this->assertEquals(true, $assign->testable_submissions_open($student->id));

        $assign = $this->create_instance($course, ['duedate' => $tomorrow]);
        $this->assertEquals(true, $assign->testable_submissions_open($student->id));

        $assign = $this->create_instance($course, ['duedate' => $yesterday]);
        $this->assertEquals(true, $assign->testable_submissions_open($student->id));

        $assign = $this->create_instance($course, ['duedate' => $yesterday, 'cutoffdate' => $tomorrow]);
        $this->assertEquals(true, $assign->testable_submissions_open($student->id));

        $assign = $this->create_instance($course, ['duedate' => $yesterday, 'cutoffdate' => $yesterday]);
        $this->assertEquals(false, $assign->testable_submissions_open($student->id));

        $assign->testable_save_user_extension($student->id, $tomorrow);
        $this->assertEquals(true, $assign->testable_submissions_open($student->id));

        $assign = $this->create_instance($course, ['submissiondrafts' => 1]);
        $this->assertEquals(true, $assign->testable_submissions_open($student->id));

        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);

        $this->setUser($teacher);
        $this->assertEquals(false, $assign->testable_submissions_open($student->id));
    }

    public function test_get_graders() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        // Create an assignment with no groups.
        $assign = $this->create_instance($course);
        $this->assertCount(2, $assign->testable_get_graders($student->id));
    }

    public function test_get_graders_separate_groups() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group1, $student);

        $this->setAdminUser();

        // Force create an assignment with SEPARATEGROUPS.
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        $assign = $this->create_instance($course, [
            'groupingid' => $grouping->id,
            'groupmode' => SEPARATEGROUPS,
        ]);

        $this->assertCount(4, $assign->testable_get_graders($student->id));

        // Note the second student is in a group that is not in the grouping.
        // This means that we get all graders that are not in a group in the grouping.
        $this->assertCount(4, $assign->testable_get_graders($otherstudent->id));
    }

    public function test_get_notified_users() {
        global $CFG, $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group1->id, 'groupingid' => $grouping->id));

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        groups_add_member($group1, $teacher);

        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        groups_add_member($group1, $editingteacher);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group1, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $capability = 'mod/assign:receivegradernotifications';
        $coursecontext = \context_course::instance($course->id);
        $role = $DB->get_record('role', array('shortname' => 'teacher'));

        $this->setUser($teacher);

        // Create an assignment with no groups.
        $assign = $this->create_instance($course);

        $this->assertCount(3, $assign->testable_get_notifiable_users($student->id));

        // Change nonediting teachers role to not receive grader notifications.
        assign_capability($capability, CAP_PROHIBIT, $role->id, $coursecontext);

        // Only the editing teachers will be returned.
        $this->assertCount(1, $assign->testable_get_notifiable_users($student->id));

        // Note the second student is in a group that is not in the grouping.
        // This means that we get all graders that are not in a group in the grouping.
        $this->assertCount(1, $assign->testable_get_notifiable_users($otherstudent->id));
    }

    public function test_get_notified_users_in_grouping() {
        global $CFG, $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group1->id, 'groupingid' => $grouping->id));

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        groups_add_member($group1, $teacher);

        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        groups_add_member($group1, $editingteacher);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group1, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        // Force create an assignment with SEPARATEGROUPS.
        $assign = $this->create_instance($course, [
            'groupingid' => $grouping->id,
            'groupmode' => SEPARATEGROUPS,
        ]);

        // Student is in a group - only the tacher and editing teacher in the group shoudl be present.
        $this->setUser($student);
        $this->assertCount(2, $assign->testable_get_notifiable_users($student->id));

        // Note the second student is in a group that is not in the grouping.
        // This means that we get all graders that are not in a group in the grouping.
        $this->assertCount(1, $assign->testable_get_notifiable_users($otherstudent->id));

        // Change nonediting teachers role to not receive grader notifications.
        $capability = 'mod/assign:receivegradernotifications';
        $coursecontext = \context_course::instance($course->id);
        $role = $DB->get_record('role', ['shortname' => 'teacher']);
        assign_capability($capability, CAP_PROHIBIT, $role->id, $coursecontext);

        // Only the editing teachers will be returned.
        $this->assertCount(1, $assign->testable_get_notifiable_users($student->id));

        // Note the second student is in a group that is not in the grouping.
        // This means that we get all graders that are not in a group in the grouping.
        // Unfortunately there are no editing teachers who are not in a group.
        $this->assertCount(0, $assign->testable_get_notifiable_users($otherstudent->id));
    }

    public function test_group_members_only() {
        global $CFG;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_grouping_group([
            'groupid' => $group1->id,
            'groupingid' => $grouping->id,
        ]);

        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $this->getDataGenerator()->create_grouping_group([
            'groupid' => $group2->id,
            'groupingid' => $grouping->id,
        ]);

        $group3 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        // Add users in the following groups
        // - Teacher - Group 1.
        // - Student - Group 1.
        // - Student - Group 2.
        // - Student - Unrelated Group
        // - Student - No group.
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        groups_add_member($group1, $teacher);

        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group1, $student);

        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group2, $otherstudent);

        $yetotherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group2, $otherstudent);

        $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setAdminUser();

        $CFG->enableavailability = true;
        $assign = $this->create_instance($course, [], [
            'availability' => json_encode(
                \core_availability\tree::get_root_json([\availability_grouping\condition::get_json()])
            ),
            'groupingid' => $grouping->id,
        ]);

        // The two students in groups should be returned, but not the teacher in the group, or the student not in the
        // group, or the student in an unrelated group.
        $this->setUser($teacher);
        $participants = $assign->list_participants(0, true);
        $this->assertCount(2, $participants);
        $this->assertTrue(isset($participants[$student->id]));
        $this->assertTrue(isset($participants[$otherstudent->id]));
    }

    public function test_get_uniqueid_for_user() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $students = [];
        for ($i = 0; $i < 10; $i++) {
            $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
            $students[$student->id] = $student;
        }

        $this->setUser($teacher);
        $assign = $this->create_instance($course);

        foreach ($students as $student) {
            $uniqueid = $assign->get_uniqueid_for_user($student->id);
            $this->assertEquals($student->id, $assign->get_user_id_for_uniqueid($uniqueid));
        }
    }

    public function test_show_student_summary() {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);
        $assign = $this->create_instance($course);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // No feedback should be available because this student has not been graded.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertDoesNotMatchRegularExpression('/Feedback/', $output, 'Do not show feedback if there is no grade');

        // Simulate adding a grade.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student);

        // Now we should see the feedback.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertMatchesRegularExpression('/Feedback/', $output, 'Show feedback if there is a grade');

        // Now hide the grade in gradebook.
        $this->setUser($teacher);
        require_once($CFG->libdir.'/gradelib.php');
        $gradeitem = new \grade_item(array(
            'itemtype'      => 'mod',
            'itemmodule'    => 'assign',
            'iteminstance'  => $assign->get_instance()->id,
            'courseid'      => $course->id));

        $gradeitem->set_hidden(1, false);

        // No feedback should be available because the grade is hidden.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertDoesNotMatchRegularExpression('/Feedback/', $output,
            'Do not show feedback if the grade is hidden in the gradebook');

        // Freeze the context.
        $this->setAdminUser();
        $context = $assign->get_context();
        $CFG->contextlocking = true;
        $context->set_locked(true);

        // No feedback should be available because the grade is hidden.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertDoesNotMatchRegularExpression('/Feedback/', $output, 'Do not show feedback if the grade is hidden in the gradebook');

        // Show the feedback again - it should still be visible even in a frozen context.
        $this->setUser($teacher);
        $gradeitem->set_hidden(0, false);

        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertMatchesRegularExpression('/Feedback/', $output, 'Show feedback if there is a grade');
    }

    public function test_show_student_summary_with_feedback() {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'assignfeedback_comments_enabled' => 1
        ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // No feedback should be available because this student has not been graded.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertDoesNotMatchRegularExpression('/Feedback/', $output);

        // Simulate adding a grade.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);
        $this->mark_submission($teacher, $assign, $student, null, [
            'assignfeedbackcomments_editor' => [
                'text' => 'Tomato sauce',
                'format' => FORMAT_MOODLE,
            ],
        ]);

        // Should have feedback but no grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertMatchesRegularExpression('/Feedback/', $output);
        $this->assertMatchesRegularExpression('/Tomato sauce/', $output);
        $this->assertDoesNotMatchRegularExpression('/Grade/', $output, 'Do not show grade when there is no grade.');
        $this->assertDoesNotMatchRegularExpression('/Graded on/', $output, 'Do not show graded date when there is no grade.');

        // Add a grade now.
        $this->mark_submission($teacher, $assign, $student, 50.0, [
            'assignfeedbackcomments_editor' => [
                'text' => 'Bechamel sauce',
                'format' => FORMAT_MOODLE,
            ],
        ]);

        // Should have feedback but no grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertDoesNotMatchRegularExpression('/Tomato sauce/', $output);
        $this->assertMatchesRegularExpression('/Bechamel sauce/', $output);
        $this->assertMatchesRegularExpression('/Grade/', $output);
        $this->assertMatchesRegularExpression('/Graded on/', $output);

        // Now hide the grade in gradebook.
        $this->setUser($teacher);
        $gradeitem = new \grade_item(array(
            'itemtype'      => 'mod',
            'itemmodule'    => 'assign',
            'iteminstance'  => $assign->get_instance()->id,
            'courseid'      => $course->id));

        $gradeitem->set_hidden(1, false);

        // No feedback should be available because the grade is hidden.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertDoesNotMatchRegularExpression('/Feedback/', $output,
            'Do not show feedback if the grade is hidden in the gradebook');
    }

    /**
     * Test reopen behavior when in "Manual" mode.
     */
    public function test_attempt_reopen_method_manual() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $assign = $this->create_instance($course, [
            'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
            'maxattempts' => 3,
            'submissiondrafts' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Student should be able to see an add submission button.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertNotEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Add a submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Verify the student cannot make changes to the submission.
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Mark the submission.
        $this->mark_submission($teacher, $assign, $student);

        // Check the student can see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertNotEquals(false, strpos($output, '50.0'));

        // Allow the student another attempt.
        $teacher->ignoresesskey = true;
        $this->setUser($teacher);
        $result = $assign->testable_process_add_attempt($student->id);
        $this->assertEquals(true, $result);

        // Check that the previous attempt is now in the submission history table.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        // Need a better check.
        $this->assertNotEquals(false, strpos($output, 'Submission text'), 'Contains: Submission text');

        // Check that the student now has a submission history.
        $this->assertNotEquals(false, strpos($output, get_string('attempthistory', 'assign')));

        // Check that the student now does not have a button for Submit.
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertEquals(false, strpos($output, get_string('submitassignment', 'assign')));

        // Check that the student now has a button for Add a new attempt".
        $this->assertNotEquals(false, strpos($output, get_string('addnewattempt', 'assign')));

        $this->setUser($teacher);
        // Check that the grading table loads correctly and contains this user.
        // This is also testing that we do not get duplicate rows in the grading table.
        $gradingtable = new \assign_grading_table($assign, 100, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, $student->lastname));

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

        // Mark the submission again.
        $this->mark_submission($teacher, $assign, $student, 60.0, [], 1);

        // Check the grade exists.
        $this->setUser($teacher);
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEquals(60, (int) $grades[$student->id]->rawgrade);

        // Check we can reopen still.
        $result = $assign->testable_process_add_attempt($student->id);
        $this->assertEquals(true, $result);

        // Should no longer have a grade because there is no grade for the latest attempt.
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEmpty($grades);
    }

    /**
     * Test reopen behavior when in "Reopen until pass" mode.
     */
    public function test_attempt_reopen_method_untilpass() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $assign = $this->create_instance($course, [
            'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS,
            'maxattempts' => 3,
            'submissiondrafts' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Set grade to pass to 80.
        $gradeitem = $assign->get_grade_item();
        $gradeitem->gradepass = '80.0';
        $gradeitem->update();

        // Student should be able to see an add submission button.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertNotEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Add a submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Verify the student cannot make a new attempt.
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, get_string('addnewattempt', 'assign')));

        // Mark the submission as non-passing.
        $this->mark_submission($teacher, $assign, $student, 50.0);

        // Check the student can see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertNotEquals(false, strpos($output, '50.0'));

        // Check that the student now has a submission history.
        $this->assertNotEquals(false, strpos($output, get_string('attempthistory', 'assign')));

        // Check that the student now does not have a button for Submit.
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertEquals(false, strpos($output, get_string('submitassignment', 'assign')));

        // Check that the student now has a button for Add a new attempt.
        $this->assertNotEquals(false, strpos($output, get_string('addnewattempt', 'assign')));

        // Add a second submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Mark the submission as passing.
        $this->mark_submission($teacher, $assign, $student, 80.0);

        // Check that the student does not have a button for Add a new attempt.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, get_string('addnewattempt', 'assign')));

        // Re-mark the submission as not passing.
        $this->mark_submission($teacher, $assign, $student, 40.0, [], 1);

        // Check that the student now has a button for Add a new attempt.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertMatchesRegularExpression('/' . get_string('addnewattempt', 'assign') . '/', $output);
        $this->assertNotEquals(false, strpos($output, get_string('addnewattempt', 'assign')));
    }

    public function test_attempt_reopen_method_untilpass_passing() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $assign = $this->create_instance($course, [
            'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS,
            'maxattempts' => 3,
            'submissiondrafts' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Set grade to pass to 80.
        $gradeitem = $assign->get_grade_item();
        $gradeitem->gradepass = '80.0';
        $gradeitem->update();

        // Student should be able to see an add submission button.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertNotEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Add a submission as a student.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Mark the submission as passing.
        $this->mark_submission($teacher, $assign, $student, 100.0);

        // Check the student can see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertNotEquals(false, strpos($output, '100.0'));

        // Check that the student does not have a button for Add a new attempt.
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, get_string('addnewattempt', 'assign')));
    }

    public function test_attempt_reopen_method_untilpass_no_passing_requirement() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $assign = $this->create_instance($course, [
            'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS,
            'maxattempts' => 3,
            'submissiondrafts' => 1,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Set grade to pass to 0, so that no attempts should reopen.
        $gradeitem = $assign->get_grade_item();
        $gradeitem->gradepass = '0';
        $gradeitem->update();

        // Student should be able to see an add submission button.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertNotEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Add a submission.
        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign);

        // Mark the submission with any grade.
        $this->mark_submission($teacher, $assign, $student, 0.0);

        // Check the student can see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertNotEquals(false, strpos($output, '0.0'));

        // Check that the student does not have a button for Add a new attempt.
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, get_string('addnewattempt', 'assign')));
    }

    /**
     * Test student visibility for each stage of the marking workflow.
     */
    public function test_markingworkflow() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $assign = $this->create_instance($course, [
            'markingworkflow' => 1,
        ]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Mark the submission and set to notmarked.
        $this->mark_submission($teacher, $assign, $student, 50.0,  [
            'workflowstate' => ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED,
        ]);

        // Check the student can't see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Make sure the grade isn't pushed to the gradebook.
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEmpty($grades);

        // Mark the submission and set to inmarking.
        $this->mark_submission($teacher, $assign, $student, 50.0,  [
            'workflowstate' => ASSIGN_MARKING_WORKFLOW_STATE_INMARKING,
        ]);

        // Check the student can't see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Make sure the grade isn't pushed to the gradebook.
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEmpty($grades);

        // Mark the submission and set to readyforreview.
        $this->mark_submission($teacher, $assign, $student, 50.0,  [
            'workflowstate' => ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW,
        ]);

        // Check the student can't see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Make sure the grade isn't pushed to the gradebook.
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEmpty($grades);

        // Mark the submission and set to inreview.
        $this->mark_submission($teacher, $assign, $student, 50.0,  [
            'workflowstate' => ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW,
        ]);

        // Check the student can't see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Make sure the grade isn't pushed to the gradebook.
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEmpty($grades);

        // Mark the submission and set to readyforrelease.
        $this->mark_submission($teacher, $assign, $student, 50.0,  [
            'workflowstate' => ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE,
        ]);

        // Check the student can't see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertEquals(false, strpos($output, '50.0'));

        // Make sure the grade isn't pushed to the gradebook.
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEmpty($grades);

        // Mark the submission and set to released.
        $this->mark_submission($teacher, $assign, $student, 50.0,  [
            'workflowstate' => ASSIGN_MARKING_WORKFLOW_STATE_RELEASED,
        ]);

        // Check the student can see the grade.
        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertNotEquals(false, strpos($output, '50.0'));

        // Make sure the grade is pushed to the gradebook.
        $grades = $assign->get_user_grades_for_gradebook($student->id);
        $this->assertEquals(50, (int)$grades[$student->id]->rawgrade);
    }

    /**
     * Test that a student allocated a specific marker is only shown to that marker.
     */
    public function test_markerallocation() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $otherteacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $assign = $this->create_instance($course, [
            'markingworkflow' => 1,
            'markingallocation' => 1
        ]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Allocate marker to submission.
        $this->mark_submission($teacher, $assign, $student, null, [
            'allocatedmarker' => $teacher->id,
        ]);

        // Check the allocated marker can view the submission.
        $this->setUser($teacher);
        $users = $assign->list_participants(0, true);
        $this->assertEquals(1, count($users));
        $this->assertTrue(isset($users[$student->id]));

        $cm = get_coursemodule_from_instance('assign', $assign->get_instance()->id);
        $context = \context_module::instance($cm->id);
        $assign = new mod_assign_testable_assign($context, $cm, $course);

        // Check that other teachers can't view this submission.
        $this->setUser($otherteacher);
        $users = $assign->list_participants(0, true);
        $this->assertEquals(0, count($users));
    }

    /**
     * Ensure that a teacher cannot submit for students as standard.
     */
    public function test_teacher_submit_for_student() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
        ]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Add a submission but do not submit.
        $this->add_submission($student, $assign, 'Student submission text');

        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertStringContainsString('Student submission text', $output, 'Contains student submission text');

        // Check that a teacher can not edit the submission as they do not have the capability.
        $this->setUser($teacher);
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('error/nopermission');
        $this->add_submission($student, $assign, 'Teacher edited submission text', false);
    }

    /**
     * Ensure that a teacher with the editothersubmission capability can submit on behalf of a student.
     */
    public function test_teacher_submit_for_student_with_capability() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $otherteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
        ]);

        // Add the required capability.
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('mod/assign:editothersubmission', CAP_ALLOW, $roleid, $assign->get_context()->id);
        role_assign($roleid, $teacher->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Add a submission but do not submit.
        $this->add_submission($student, $assign, 'Student submission text');

        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertStringContainsString('Student submission text', $output, 'Contains student submission text');

        // Check that a teacher can edit the submission.
        $this->setUser($teacher);
        $this->add_submission($student, $assign, 'Teacher edited submission text', false);

        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertStringNotContainsString('Student submission text', $output, 'Contains student submission text');
        $this->assertStringContainsString('Teacher edited submission text', $output, 'Contains teacher edited submission text');

        // Check that the teacher can submit the students work.
        $this->setUser($teacher);
        $this->submit_for_grading($student, $assign, [], false);

        // Revert to draft so the student can edit it.
        $assign->revert_to_draft($student->id);

        $this->setUser($student);

        // Check that the submission text was saved.
        $output = $assign->view_student_summary($student, true);
        $this->assertStringContainsString('Teacher edited submission text', $output, 'Contains student submission text');

        // Check that the student can submit their work.
        $this->submit_for_grading($student, $assign, []);

        $output = $assign->view_student_summary($student, true);
        $this->assertStringNotContainsString(get_string('addsubmission', 'assign'), $output);

        // An editing teacher without the extra role should still be able to revert to draft.
        $this->setUser($otherteacher);

        // Revert to draft so the submission is editable.
        $assign->revert_to_draft($student->id);
    }

    /**
     * Ensure that disabling submit after the cutoff date works as expected.
     */
    public function test_disable_submit_after_cutoff_date() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $now = time();
        $tomorrow = $now + DAYSECS;
        $lastweek = $now - (7 * DAYSECS);
        $yesterday = $now - DAYSECS;

        $this->setAdminUser();
        $assign = $this->create_instance($course, [
            'duedate' => $yesterday,
            'cutoffdate' => $tomorrow,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Student should be able to see an add submission button.
        $this->setUser($student);
        $output = $assign->view_submission_action_bar($assign->get_instance(), $student);
        $this->assertNotEquals(false, strpos($output, get_string('addsubmission', 'assign')));

        // Add a submission but don't submit now.
        $this->add_submission($student, $assign);

        // Create another instance with cut-off and due-date already passed.
        $this->setAdminUser();
        $assign = $this->create_instance($course, [
            'duedate' => $lastweek,
            'cutoffdate' => $yesterday,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $this->setUser($student);
        $output = $assign->view_student_summary($student, true);
        $this->assertStringNotContainsString($output, get_string('editsubmission', 'assign'),
            'Should not be able to edit after cutoff date.');
        $this->assertStringNotContainsString($output, get_string('submitassignment', 'assign'),
            'Should not be able to submit after cutoff date.');
    }

    /**
     * Testing for submission comment plugin settings.
     *
     * @dataProvider submission_plugin_settings_provider
     * @param   bool    $globalenabled
     * @param   array   $instanceconfig
     * @param   bool    $isenabled
     */
    public function test_submission_comment_plugin_settings($globalenabled, $instanceconfig, $isenabled) {
        global $CFG;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $CFG->usecomments = $globalenabled;
        $assign = $this->create_instance($course, $instanceconfig);
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEquals($isenabled, (bool) $plugin->is_enabled('enabled'));
    }

    public function submission_plugin_settings_provider() {
        return [
            'CFG->usecomments true, empty config => Enabled by default' => [
                true,
                [],
                true,
            ],
            'CFG->usecomments true, config enabled => Comments enabled' => [
                true,
                [
                    'assignsubmission_comments_enabled' => 1,
                ],
                true,
            ],
            'CFG->usecomments true, config idisabled => Comments enabled' => [
                true,
                [
                    'assignsubmission_comments_enabled' => 0,
                ],
                true,
            ],
            'CFG->usecomments false, empty config => Disabled by default' => [
                false,
                [],
                false,
            ],
            'CFG->usecomments false, config enabled => Comments disabled' => [
                false,
                [
                    'assignsubmission_comments_enabled' => 1,
                ],
                false,
            ],
            'CFG->usecomments false, config disabled => Comments disabled' => [
                false,
                [
                    'assignsubmission_comments_enabled' => 0,
                ],
                false,
            ],
        ];
    }

    /**
     * Testing for comment inline settings
     */
    public function test_feedback_comment_commentinline() {
        global $CFG, $USER;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

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

        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'assignfeedback_comments_enabled' => 1,
            'assignfeedback_comments_commentinline' => 1,
        ]);

        $this->setUser($student);

        // Add a submission but don't submit now.
        $this->add_submission($student, $assign, $sourcetext);

        $this->setUser($teacher);

        $data = new \stdClass();
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');
        $pagination = [
            'userid' => $student->id,
            'rownum' => 0,
            'last' => true,
            'useridlistid' => $assign->get_useridlist_key_id(),
            'attemptnumber' => 0,
        ];
        $formparams = array($assign, $data, $pagination);
        $mform = new mod_assign_grade_form(null, [$assign, $data, $pagination]);

        // We need to get the URL these will be transformed to.
        $context = \context_user::instance($USER->id);
        $itemid = $data->assignfeedbackcomments_editor['itemid'];
        $url = $CFG->wwwroot . '/draftfile.php/' . $context->id . '/user/draft/' . $itemid;

        // Note the internal images have been stripped and the html is purified (quotes fixed in this case).
        $filteredtext = "Hello!

I'm writing to you from the Moodle Majlis in Muscat, Oman, where we just had several days of Moodle community goodness.

URL outside a tag: https://moodle.org/logo/logo-240x60.gif
Plugin url outside a tag: $url/logo-240x60.gif

External link 1:<img src=\"https://moodle.org/logo/logo-240x60.gif\" alt=\"Moodle\" />
External link 2:<img alt=\"Moodle\" src=\"https://moodle.org/logo/logo-240x60.gif\" />
Internal link 1:<img src=\"$url/logo-240x60.gif\" alt=\"Moodle\" />
Internal link 2:<img alt=\"Moodle\" src=\"@@PLUGINFILE@@logo-240x60.gif\" />
Anchor link 1:<a href=\"@@PLUGINFILE@@logo-240x60.gif\">Link text</a>
Anchor link 2:<a title=\"bananas\" href=\"../logo-240x60.gif\">Link text</a>
";

        $this->assertEquals($filteredtext, $data->assignfeedbackcomments_editor['text']);
    }

    /**
     * Testing for feedback comment plugin settings.
     *
     * @dataProvider feedback_plugin_settings_provider
     * @param   array   $instanceconfig
     * @param   bool    $isenabled
     */
    public function test_feedback_plugin_settings($instanceconfig, $isenabled) {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        $assign = $this->create_instance($course, $instanceconfig);
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertEquals($isenabled, (bool) $plugin->is_enabled('enabled'));
    }

    public function feedback_plugin_settings_provider() {
        return [
            'No configuration => disabled' => [
                [],
                false,
            ],
            'Actively disabled' => [
                [
                    'assignfeedback_comments_enabled' => 0,
                ],
                false,
            ],
            'Actively enabled' => [
                [
                    'assignfeedback_comments_enabled' => 1,
                ],
                true,
            ],
        ];
    }

    /**
     * Testing if gradebook feedback plugin is enabled.
     */
    public function test_is_gradebook_feedback_enabled() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $adminconfig = get_config('assign');
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        // Create assignment with gradebook feedback enabled and grade = 0.
        $assign = $this->create_instance($course, [
            "{$gradebookplugin}_enabled" => 1,
            'grades' => 0,
        ]);

        // Get gradebook feedback plugin.
        $gradebookplugintype = str_replace('assignfeedback_', '', $gradebookplugin);
        $plugin = $assign->get_feedback_plugin_by_type($gradebookplugintype);
        $this->assertEquals(1, $plugin->is_enabled('enabled'));
        $this->assertEquals(1, $assign->is_gradebook_feedback_enabled());
    }

    /**
     * Testing if gradebook feedback plugin is disabled.
     */
    public function test_is_gradebook_feedback_disabled() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $adminconfig = get_config('assign');
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        // Create assignment with gradebook feedback disabled and grade = 0.
        $assign = $this->create_instance($course, [
            "{$gradebookplugin}_enabled" => 0,
            'grades' => 0,
        ]);

        $gradebookplugintype = str_replace('assignfeedback_', '', $gradebookplugin);
        $plugin = $assign->get_feedback_plugin_by_type($gradebookplugintype);
        $this->assertEquals(0, $plugin->is_enabled('enabled'));
    }

    /**
     * Testing can_edit_submission.
     */
    public function test_can_edit_submission() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
        ]);

        // Check student can edit their own submission.
        $this->assertTrue($assign->can_edit_submission($student->id, $student->id));

        // Check student cannot edit others submission.
        $this->assertFalse($assign->can_edit_submission($otherstudent->id, $student->id));

        // Check teacher cannot (by default) edit a students submission.
        $this->assertFalse($assign->can_edit_submission($student->id, $teacher->id));
    }

    /**
     * Testing can_edit_submission with the editothersubmission capability.
     */
    public function test_can_edit_submission_with_editothersubmission() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
        ]);

        // Add the required capability to edit a student submission.
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('mod/assign:editothersubmission', CAP_ALLOW, $roleid, $assign->get_context()->id);
        role_assign($roleid, $teacher->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Check student can edit their own submission.
        $this->assertTrue($assign->can_edit_submission($student->id, $student->id));

        // Check student cannot edit others submission.
        $this->assertFalse($assign->can_edit_submission($otherstudent->id, $student->id));

        // Retest - should now have access.
        $this->assertTrue($assign->can_edit_submission($student->id, $teacher->id));
    }

    /**
     * Testing can_edit_submission
     */
    public function test_can_edit_submission_separategroups() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student4 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group1->id);
        groups_add_member($group1, $student1);
        groups_add_member($group1, $student2);

        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group2->id);
        groups_add_member($group2, $student3);
        groups_add_member($group2, $student4);

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
            'groupingid' => $grouping->id,
            'groupmode' => SEPARATEGROUPS,
        ]);

        // Verify a student does not have the ability to edit submissions for other users.
        $this->assertTrue($assign->can_edit_submission($student1->id, $student1->id));
        $this->assertFalse($assign->can_edit_submission($student2->id, $student1->id));
        $this->assertFalse($assign->can_edit_submission($student3->id, $student1->id));
        $this->assertFalse($assign->can_edit_submission($student4->id, $student1->id));
    }

    /**
     * Testing can_edit_submission
     */
    public function test_can_edit_submission_separategroups_with_editothersubmission() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student4 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group1->id);
        groups_add_member($group1, $student1);
        groups_add_member($group1, $student2);

        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group2->id);
        groups_add_member($group2, $student3);
        groups_add_member($group2, $student4);

        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'submissiondrafts' => 1,
            'groupingid' => $grouping->id,
            'groupmode' => SEPARATEGROUPS,
        ]);

        // Add the capability to the new \assignment for student 1.
        $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        assign_capability('mod/assign:editothersubmission', CAP_ALLOW, $roleid, $assign->get_context()->id);
        role_assign($roleid, $student1->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Verify student1 has the ability to edit submissions for other users in their group, but not other groups.
        $this->assertTrue($assign->can_edit_submission($student1->id, $student1->id));
        $this->assertTrue($assign->can_edit_submission($student2->id, $student1->id));
        $this->assertFalse($assign->can_edit_submission($student3->id, $student1->id));
        $this->assertFalse($assign->can_edit_submission($student4->id, $student1->id));

        // Verify other students do not have the ability to edit submissions for other users.
        $this->assertTrue($assign->can_edit_submission($student2->id, $student2->id));
        $this->assertFalse($assign->can_edit_submission($student1->id, $student2->id));
        $this->assertFalse($assign->can_edit_submission($student3->id, $student2->id));
        $this->assertFalse($assign->can_edit_submission($student4->id, $student2->id));
    }

    /**
     * Test if the view blind details capability works
     */
    public function test_can_view_blind_details() {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $manager = $this->getDataGenerator()->create_and_enrol($course, 'manager');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
            'blindmarking' => 1,
        ]);

        $this->assertTrue($assign->is_blind_marking());

        // Test student names are hidden to teacher.
        $this->setUser($teacher);
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, get_string('hiddenuser', 'assign')));    // "Participant" is somewhere on the page.
        $this->assertEquals(false, strpos($output, fullname($student)));    // Students full name doesn't appear.

        // Test student names are visible to manager.
        $this->setUser($manager);
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertEquals(true, strpos($output, get_string('hiddenuser', 'assign')));
        $this->assertEquals(true, strpos($output, fullname($student)));
    }

    /**
     * Testing get_shared_group_members
     */
    public function test_get_shared_group_members() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student4 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group1->id);
        groups_add_member($group1, $student1);
        groups_add_member($group1, $student2);

        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group2->id);
        groups_add_member($group2, $student3);
        groups_add_member($group2, $student4);

        $assign = $this->create_instance($course, [
            'groupingid' => $grouping->id,
            'groupmode' => SEPARATEGROUPS,
        ]);

        $cm = $assign->get_course_module();

        // Get shared group members for students 0 and 1.
        $groupmembers = $assign->get_shared_group_members($cm, $student1->id);
        $this->assertCount(2, $groupmembers);
        $this->assertContainsEquals($student1->id, $groupmembers);
        $this->assertContainsEquals($student2->id, $groupmembers);

        $groupmembers = $assign->get_shared_group_members($cm, $student2->id);
        $this->assertCount(2, $groupmembers);
        $this->assertContainsEquals($student1->id, $groupmembers);
        $this->assertContainsEquals($student2->id, $groupmembers);

        $groupmembers = $assign->get_shared_group_members($cm, $student3->id);
        $this->assertCount(2, $groupmembers);
        $this->assertContainsEquals($student3->id, $groupmembers);
        $this->assertContainsEquals($student4->id, $groupmembers);

        $groupmembers = $assign->get_shared_group_members($cm, $student4->id);
        $this->assertCount(2, $groupmembers);
        $this->assertContainsEquals($student3->id, $groupmembers);
        $this->assertContainsEquals($student4->id, $groupmembers);
    }

    /**
     * Testing get_shared_group_members
     */
    public function test_get_shared_group_members_override() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student4 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group1->id);
        groups_add_member($group1, $student1);
        groups_add_member($group1, $student2);

        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_assign_grouping($grouping->id, $group2->id);
        groups_add_member($group2, $student3);
        groups_add_member($group2, $student4);

        $assign = $this->create_instance($course, [
            'groupingid' => $grouping->id,
            'groupmode' => SEPARATEGROUPS,
        ]);

        $cm = $assign->get_course_module();

        // Add the capability to access allgroups for one of the students.
        $roleid = create_role('Access all groups role', 'accessallgroupsrole', '');
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $roleid, $assign->get_context()->id);
        role_assign($roleid, $student1->id, $assign->get_context()->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Get shared group members for students 0 and 1.
        $groupmembers = $assign->get_shared_group_members($cm, $student1->id);
        $this->assertCount(4, $groupmembers);
        $this->assertContainsEquals($student1->id, $groupmembers);
        $this->assertContainsEquals($student2->id, $groupmembers);
        $this->assertContainsEquals($student3->id, $groupmembers);
        $this->assertContainsEquals($student4->id, $groupmembers);

        $groupmembers = $assign->get_shared_group_members($cm, $student2->id);
        $this->assertCount(2, $groupmembers);
        $this->assertContainsEquals($student1->id, $groupmembers);
        $this->assertContainsEquals($student2->id, $groupmembers);

        $groupmembers = $assign->get_shared_group_members($cm, $student3->id);
        $this->assertCount(2, $groupmembers);
        $this->assertContainsEquals($student3->id, $groupmembers);
        $this->assertContainsEquals($student4->id, $groupmembers);

        $groupmembers = $assign->get_shared_group_members($cm, $student4->id);
        $this->assertCount(2, $groupmembers);
        $this->assertContainsEquals($student3->id, $groupmembers);
        $this->assertContainsEquals($student4->id, $groupmembers);
    }

    /**
     * Test get plugins file areas
     */
    public function test_get_plugins_file_areas() {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $assign = $this->create_instance($course);

        // Test that all the submission and feedback plugins are returning the expected file aras.
        $usingfilearea = 0;
        $coreplugins = \core_plugin_manager::standard_plugins_list('assignsubmission');
        foreach ($assign->get_submission_plugins() as $plugin) {
            $type = $plugin->get_type();
            if (!in_array($type, $coreplugins)) {
                continue;
            }
            $fileareas = $plugin->get_file_areas();

            if ($type == 'onlinetext') {
                $this->assertEquals(array('submissions_onlinetext' => 'Online text'), $fileareas);
                $usingfilearea++;
            } else if ($type == 'file') {
                $this->assertEquals(array('submission_files' => 'File submissions'), $fileareas);
                $usingfilearea++;
            } else {
                $this->assertEmpty($fileareas);
            }
        }
        $this->assertEquals(2, $usingfilearea);

        $usingfilearea = 0;
        $coreplugins = \core_plugin_manager::standard_plugins_list('assignfeedback');
        foreach ($assign->get_feedback_plugins() as $plugin) {
            $type = $plugin->get_type();
            if (!in_array($type, $coreplugins)) {
                continue;
            }
            $fileareas = $plugin->get_file_areas();

            if ($type == 'editpdf') {
                $checkareas = [
                    'download' => 'Annotate PDF',
                    'combined' => 'Annotate PDF',
                    'partial' => 'Annotate PDF',
                    'importhtml' => 'Annotate PDF',
                    'pages' => 'Annotate PDF',
                    'readonlypages' => 'Annotate PDF',
                    'stamps' => 'Annotate PDF',
                    'tmp_jpg_to_pdf' => 'Annotate PDF',
                    'tmp_rotated_jpg' => 'Annotate PDF'
                ];
                $this->assertEquals($checkareas, $fileareas);
                $usingfilearea++;
            } else if ($type == 'file') {
                $this->assertEquals(array('feedback_files' => 'Feedback files'), $fileareas);
                $usingfilearea++;
            } else if ($type == 'comments') {
                $this->assertEquals(array('feedback' => 'Feedback comments'), $fileareas);
                $usingfilearea++;
            } else {
                $this->assertEmpty($fileareas);
            }
        }
        $this->assertEquals(3, $usingfilearea);
    }

    /**
     * Test override exists
     *
     * This function needs to obey the group override logic as per the assign grading table and
     * the overview block.
     */
    public function test_override_exists() {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        // Data:
        // - student1 => group A only
        // - student2 => group B only
        // - student3 => Group A + Group B (No user override)
        // - student4 => Group A + Group B (With user override)
        // - student4 => No groups.
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group1, $student1);

        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group2, $student2);

        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group1, $student3);
        groups_add_member($group2, $student3);

        $student4 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        groups_add_member($group1, $student4);
        groups_add_member($group2, $student4);

        $student5 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course);
        $instance = $assign->get_instance();

        // Overrides for each of the groups, and a user override.
        $overrides = [
            (object) [
                // Override for group 1, highest priority (numerically lowest sortorder).
                'assignid' => $instance->id,
                'groupid' => $group1->id,
                'userid' => null,
                'sortorder' => 1,
                'allowsubmissionsfromdate' => 1,
                'duedate' => 2,
                'cutoffdate' => 3,
                'timelimit' => null
            ],
            (object) [
                // Override for group 2, lower priority (numerically higher sortorder).
                'assignid' => $instance->id,
                'groupid' => $group2->id,
                'userid' => null,
                'sortorder' => 2,
                'allowsubmissionsfromdate' => 5,
                'duedate' => 6,
                'cutoffdate' => 6,
                'timelimit' => null
            ],
            (object) [
                // User override.
                'assignid' => $instance->id,
                'groupid' => null,
                'userid' => $student3->id,
                'sortorder' => null,
                'allowsubmissionsfromdate' => 7,
                'duedate' => 8,
                'cutoffdate' => 9,
                'timelimit' => null
            ],
        ];

        foreach ($overrides as &$override) {
            $override->id = $DB->insert_record('assign_overrides', $override);
        }

        // User only in group 1 should see the group 1 override.
        $this->assertEquals($overrides[0], $assign->override_exists($student1->id));

        // User only in group 2 should see the group 2 override.
        $this->assertEquals($overrides[1], $assign->override_exists($student2->id));

        // User only in both groups with an override should see the user override as it has higher priority.
        $this->assertEquals($overrides[2], $assign->override_exists($student3->id));

        // User only in both groups with no override should see the group 1 override as it has higher priority.
        $this->assertEquals($overrides[0], $assign->override_exists($student4->id));

        // User with no overrides shoudl get nothing.
        $override = $assign->override_exists($student5->id);
        $this->assertNull($override->duedate);
        $this->assertNull($override->cutoffdate);
        $this->assertNull($override->allowsubmissionsfromdate);
    }

    /**
     * Test the quicksave grades processor
     */
    public function test_process_save_quick_grades() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $teacher->ignoresesskey = true;
        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
                'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
            ]);

        // Initially grade the user.
        $grade = (object) [
            'attemptnumber' => '',
            'timemodified' => '',
        ];
        $data = [
            "grademodified_{$student->id}" => $grade->timemodified,
            "gradeattempt_{$student->id}" => $grade->attemptnumber,
            "quickgrade_{$student->id}" => '60.0',
        ];

        $result = $assign->testable_process_save_quick_grades($data);
        $this->assertStringContainsString(get_string('quickgradingchangessaved', 'assign'), $result);
        $grade = $assign->get_user_grade($student->id, false);
        $this->assertEquals(60.0, $grade->grade);

        // Attempt to grade with a past attempts grade info.
        $assign->testable_process_add_attempt($student->id);
        $data = array(
            'grademodified_' . $student->id => $grade->timemodified,
            'gradeattempt_' . $student->id => $grade->attemptnumber,
            'quickgrade_' . $student->id => '50.0'
        );
        $result = $assign->testable_process_save_quick_grades($data);
        $this->assertStringContainsString(get_string('errorrecordmodified', 'assign'), $result);
        $grade = $assign->get_user_grade($student->id, false);
        $this->assertFalse($grade);

        // Attempt to grade a the attempt.
        $submission = $assign->get_user_submission($student->id, false);
        $data = array(
            'grademodified_' . $student->id => '',
            'gradeattempt_' . $student->id => $submission->attemptnumber,
            'quickgrade_' . $student->id => '40.0'
        );
        $result = $assign->testable_process_save_quick_grades($data);
        $this->assertStringContainsString(get_string('quickgradingchangessaved', 'assign'), $result);
        $grade = $assign->get_user_grade($student->id, false);
        $this->assertEquals(40.0, $grade->grade);

        // Catch grade update conflicts.
        // Save old data for later.
        $pastdata = $data;
        // Update the grade the 'good' way.
        $data = array(
            'grademodified_' . $student->id => $grade->timemodified,
            'gradeattempt_' . $student->id => $grade->attemptnumber,
            'quickgrade_' . $student->id => '30.0'
        );
        $result = $assign->testable_process_save_quick_grades($data);
        $this->assertStringContainsString(get_string('quickgradingchangessaved', 'assign'), $result);
        $grade = $assign->get_user_grade($student->id, false);
        $this->assertEquals(30.0, $grade->grade);

        // Now update using 'old' data. Should fail.
        $result = $assign->testable_process_save_quick_grades($pastdata);
        $this->assertStringContainsString(get_string('errorrecordmodified', 'assign'), $result);
        $grade = $assign->get_user_grade($student->id, false);
        $this->assertEquals(30.0, $grade->grade);
    }

    /**
     * Test updating activity completion when submitting an assessment.
     */
    public function test_update_activity_completion_records_solitary_submission() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'grade' => 100,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'requireallteammemberssubmit' => 0,
        ]);
        $cm = $assign->get_course_module();

        // Submit the assignment as the student.
        $this->add_submission($student, $assign);

        // Check that completion is not met yet.
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm, false, $student->id);
        $this->assertEquals(0, $completiondata->completionstate);

        // Update to mark as complete.
        $submission = $assign->get_user_submission($student->id, true);
        $assign->testable_update_activity_completion_records(0, 0, $submission,
            $student->id, COMPLETION_COMPLETE, $completion);

        // Completion should now be met.
        $completiondata = $completion->get_data($cm, false, $student->id);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    /**
     * Test updating activity completion when submitting an assessment.
     */
    public function test_update_activity_completion_records_team_submission() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        groups_add_member($group1, $student);
        groups_add_member($group1, $otherstudent);

        $assign = $this->create_instance($course, [
            'grade' => 100,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'teamsubmission' => 1,
        ]);

        $cm = $assign->get_course_module();

        $this->add_submission($student, $assign);
        $this->submit_for_grading($student, $assign, ['groupid' => $group1->id]);

        $completion = new \completion_info($course);

        // Check that completion is not met yet.
        $completiondata = $completion->get_data($cm, false, $student->id);
        $this->assertEquals(0, $completiondata->completionstate);

        $completiondata = $completion->get_data($cm, false, $otherstudent->id);
        $this->assertEquals(0, $completiondata->completionstate);

        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $submission->groupid = $group1->id;

        $assign->testable_update_activity_completion_records(1, 0, $submission, $student->id, COMPLETION_COMPLETE, $completion);

        // Completion should now be met.
        $completiondata = $completion->get_data($cm, false, $student->id);
        $this->assertEquals(1, $completiondata->completionstate);

        $completiondata = $completion->get_data($cm, false, $otherstudent->id);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    /**
     * Test updating activity completion when submitting an assessment for MDL-67126.
     */
    public function test_update_activity_completion_records_team_submission_new() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        groups_add_member($group1, $student);
        groups_add_member($group1, $otherstudent);

        $assign = $this->create_instance($course, [
            'submissiondrafts' => 0,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionsubmit' => 1,
            'teamsubmission' => 1,
            'assignsubmission_onlinetext_enabled' => 1
        ]);

        $cm = $assign->get_course_module();

        $this->add_submission($student, $assign);

        $completion = new \completion_info($course);

        // Completion should now be met.
        $completiondata = $completion->get_data($cm, false, $student->id);
        $this->assertEquals(1, $completiondata->completionstate);

        $completiondata = $completion->get_data($cm, false, $otherstudent->id);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    /**
     * Data provider for test_fix_null_grades
     * @return array[] Test data for test_fix_null_grades. Each element should contain grade, expectedcount and gradebookvalue
     */
    public function fix_null_grades_provider() {
        return [
            'Negative less than one is errant' => [
                'grade' => -0.64,
                'gradebookvalue' => null,
            ],
            'Negative more than one is errant' => [
                'grade' => -30.18,
                'gradebookvalue' => null,
            ],
            'Negative one exactly is not errant, but shouldn\'t be pushed to gradebook' => [
                'grade' => ASSIGN_GRADE_NOT_SET,
                'gradebookvalue' => null,
            ],
            'Positive grade is not errant' => [
                'grade' => 1,
                'gradebookvalue' => 1,
            ],
            'Large grade is not errant' => [
                'grade' => 100,
                'gradebookvalue' => 100,
            ],
            'Zero grade is not errant' => [
                'grade' => 0,
                'gradebookvalue' => 0,
            ],
        ];
    }

    /**
     * Test fix_null_grades
     * @param number $grade The grade we should set in the assign grading table.
     * @param number $expectedcount The finalgrade we expect in the gradebook after fixing the grades.
     * @dataProvider fix_null_grades_provider
     */
    public function test_fix_null_grades($grade, $gradebookvalue) {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course);

        // Try getting a student's grade. This will give a grade of -1.
        // Then we can override it with a bad negative grade.
        $assign->get_user_grade($student->id, true);

        // Set the grade to something errant.
        // We don't set the grader here, so we expect it to be -1 as a result.
        $DB->set_field(
            'assign_grades',
            'grade',
            $grade,
            [
                'userid' => $student->id,
                'assignment' => $assign->get_instance()->id,
            ]
        );
        $assign->grade = $grade;
        $assigntemp = clone $assign->get_instance();
        $assigntemp->cmidnumber = $assign->get_course_module()->idnumber;
        assign_update_grades($assigntemp);

        // Check that the gradebook was updated with the assign grade. So we can guarentee test results later on.
        $expectedgrade = $grade == -1 ? null : $grade; // Assign sends null to the gradebook for -1 grades.
        $gradegrade = \grade_grade::fetch(array('userid' => $student->id, 'itemid' => $assign->get_grade_item()->id));
        $this->assertEquals(-1, $gradegrade->usermodified);
        $this->assertEquals($expectedgrade, $gradegrade->rawgrade);

        // Call fix_null_grades().
        $method = new \ReflectionMethod(\assign::class, 'fix_null_grades');
        $result = $method->invoke($assign);

        $this->assertSame(true, $result);

        $gradegrade = \grade_grade::fetch(array('userid' => $student->id, 'itemid' => $assign->get_grade_item()->id));

        $this->assertEquals(-1, $gradegrade->usermodified);
        $this->assertEquals($gradebookvalue, $gradegrade->finalgrade);

        // Check that the grade was updated in the gradebook by fix_null_grades.
        $this->assertEquals($gradebookvalue, $gradegrade->finalgrade);
    }

    /**
     * Test grade override displays 'Graded' for students
     */
    public function test_grade_submission_override() {
        global $DB, $PAGE, $OUTPUT;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // Simulate adding a grade.
        $this->setUser($teacher);
        $data = new \stdClass();
        $data->grade = '50.0';
        $assign->testable_apply_grade_to_user($data, $student->id, 0);

        // Set grade override.
        $gradegrade = \grade_grade::fetch([
            'userid' => $student->id,
            'itemid' => $assign->get_grade_item()->id,
        ]);

        // Check that grade submission is not overridden yet.
        $this->assertEquals(false, $gradegrade->is_overridden());

        // Simulate a submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);

        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', ['id' => $assign->get_course_module()->id]));

        // Set override grade grade, and check that grade submission has been overridden.
        $gradegrade->set_overridden(true);
        $this->assertEquals(true, $gradegrade->is_overridden());

        // Check that submissionslocked message 'This assignment is not accepting submissions' does not appear for student.
        $gradingtable = new \assign_grading_table($assign, 1, '', 0, true);
        $output = $assign->get_renderer()->render($gradingtable);
        $this->assertStringContainsString(get_string('submissionstatus_', 'assign'), $output);

        $assignsubmissionstatus = $assign->get_assign_submission_status_renderable($student, true);
        $output2 = $assign->get_renderer()->render($assignsubmissionstatus);

        // Check that submissionslocked 'This assignment is not accepting submissions' message does not appear for student.
        $this->assertStringNotContainsString(get_string('submissionslocked', 'assign'), $output2);
        // Check that submissionstatus_marked 'Graded' message does appear for student.
        $this->assertStringContainsString(get_string('submissionstatus_marked', 'assign'), $output2);
    }

    /**
     * Test the result of get_filters is consistent.
     */
    public function test_get_filters() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $valid = $assign->get_filters();

        $this->assertEquals(count($valid), 6);
    }

    /**
     * Test assign->get_instance() for a number of cases, as defined in the data provider.
     *
     * @dataProvider assign_get_instance_provider
     * @param array $courseconfig the config to use when creating the course.
     * @param array $assignconfig the config to use when creating the assignment.
     * @param array $enrolconfig the config to use when enrolling the user (this will be the active user).
     * @param array $expectedproperties an map containing the expected names and values for the assign instance data.
     */
    public function test_assign_get_instance(array $courseconfig, array $assignconfig, array $enrolconfig,
            array $expectedproperties) {
        $this->resetAfterTest();

        set_config('enablecourserelativedates', true); // Enable relative dates at site level.

        $course = $this->getDataGenerator()->create_course($courseconfig);
        $assign = $this->create_instance($course, $assignconfig);
        $user = $this->getDataGenerator()->create_and_enrol($course, ...array_values($enrolconfig));

        $instance = $assign->get_instance($user->id);

        foreach ($expectedproperties as $propertyname => $propertyval) {
            $this->assertEquals($propertyval, $instance->$propertyname);
        }
    }

    /**
     * The test_assign_get_instance data provider.
     */
    public function assign_get_instance_provider() {
        $timenow = time();

        // The get_default_instance() method shouldn't calculate any properties per-user. It should just return the record data.
        // We'll confirm this works for a few different user types anyway, just like we do for get_instance().
        return [
            'Teacher whose enrolment starts after the course start date, relative dates mode enabled' => [
                'courseconfig' => ['relativedatesmode' => true, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'teacher', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 8 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 6 * DAYSECS]
            ],
            'Teacher whose enrolment starts before the course start date, relative dates mode enabled' => [
                'courseconfig' => ['relativedatesmode' => true, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'teacher', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 12 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
            'Teacher whose enrolment starts after the course start date, relative dates mode disabled' => [
                'courseconfig' => ['relativedatesmode' => false, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'teacher', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 8 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
            'Student whose enrolment starts after the course start date, relative dates mode enabled' => [
                'courseconfig' => ['relativedatesmode' => true, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'student', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 8 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 6 * DAYSECS]
            ],
            'Student whose enrolment starts before the course start date, relative dates mode enabled' => [
                'courseconfig' => ['relativedatesmode' => true, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'student', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 12 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
            'Student whose enrolment starts after the course start date, relative dates mode disabled' => [
                'courseconfig' => ['relativedatesmode' => false, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'student', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 8 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
        ];
    }

    /**
     * Test assign->get_default_instance() for a number of cases, as defined in the date provider.
     *
     * @dataProvider assign_get_default_instance_provider
     * @param array $courseconfig the config to use when creating the course.
     * @param array $assignconfig the config to use when creating the assignment.
     * @param array $enrolconfig the config to use when enrolling the user (this will be the active user).
     * @param array $expectedproperties an map containing the expected names and values for the assign instance data.
     */
    public function test_assign_get_default_instance(array $courseconfig, array $assignconfig, array $enrolconfig,
            array $expectedproperties) {
        $this->resetAfterTest();

        set_config('enablecourserelativedates', true); // Enable relative dates at site level.

        $course = $this->getDataGenerator()->create_course($courseconfig);
        $assign = $this->create_instance($course, $assignconfig);
        $user = $this->getDataGenerator()->create_and_enrol($course, ...array_values($enrolconfig));

        $this->setUser($user);
        $defaultinstance = $assign->get_default_instance();

        foreach ($expectedproperties as $propertyname => $propertyval) {
            $this->assertEquals($propertyval, $defaultinstance->$propertyname);
        }
    }

    /**
     * The test_assign_get_default_instance data provider.
     */
    public function assign_get_default_instance_provider() {
        $timenow = time();

        // The get_default_instance() method shouldn't calculate any properties per-user. It should just return the record data.
        // We'll confirm this works for a few different user types anyway, just like we do for get_instance().
        return [
            'Teacher whose enrolment starts after the course start date, relative dates mode enabled' => [
                'courseconfig' => ['relativedatesmode' => true, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'teacher', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 8 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
            'Teacher whose enrolment starts before the course start date, relative dates mode enabled' => [
                'courseconfig' => ['relativedatesmode' => true, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'teacher', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 12 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
            'Teacher whose enrolment starts after the course start date, relative dates mode disabled' => [
                'courseconfig' => ['relativedatesmode' => false, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'teacher', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 8 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
            'Student whose enrolment starts after the course start date, relative dates mode enabled' => [
                'courseconfig' => ['relativedatesmode' => true, 'startdate' => $timenow - 10 * DAYSECS],
                'assignconfig' => ['duedate' => $timenow + 4 * DAYSECS],
                'enrolconfig' => ['shortname' => 'student', 'userparams' => null, 'method' => 'manual',
                    'startdate' => $timenow - 8 * DAYSECS],
                'expectedproperties' => ['duedate' => $timenow + 4 * DAYSECS]
            ],
        ];
    }

    /**
     * Test that cron task uses task API to get its last run time.
     */
    public function test_cron_use_task_api_to_get_lastruntime() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        // Create an assignment which allows submissions from 3 days ago.
        $assign1 = $this->create_instance($course, [
            'duedate' => time() + DAYSECS,
            'alwaysshowdescription' => 0,
            'allowsubmissionsfromdate' => time() - 3 * DAYSECS,
            'intro' => 'This one should not be re-created',
        ]);

        // Create an assignment which allows submissions from 1 day ago.
        $assign2 = $this->create_instance($course, [
            'duedate' => time() + DAYSECS,
            'alwaysshowdescription' => 0,
            'allowsubmissionsfromdate' => time() - DAYSECS,
            'intro' => 'This one should be re-created',
        ]);

        // Set last run time 2 days ago.
        $DB->set_field('task_scheduled', 'lastruntime', time() - 2 * DAYSECS, ['classname' => '\mod_assign\task\cron_task']);

        // Remove events to make sure cron will update calendar and re-create one of them.
        $params = array('modulename' => 'assign', 'instance' => $assign1->get_instance()->id);
        $DB->delete_records('event', $params);
        $params = array('modulename' => 'assign', 'instance' => $assign2->get_instance()->id);
        $DB->delete_records('event', $params);

        // Run cron.
        \assign::cron();

        // Assert that calendar hasn't been updated for the first assignment as it's supposed to be
        // updated as part of previous cron runs (allowsubmissionsfromdate is less than lastruntime).
        $params = array('modulename' => 'assign', 'instance' => $assign1->get_instance()->id);
        $event1 = $DB->get_record('event', $params);
        $this->assertEmpty($event1);

        // Assert that calendar has been updated for the second assignment
        // because its allowsubmissionsfromdate is greater than lastruntime.
        $params = array('modulename' => 'assign', 'instance' => $assign2->get_instance()->id);
        $event2 = $DB->get_record('event', $params);
        $this->assertNotEmpty($event2);
        $this->assertSame('This one should be re-created', $event2->description);
    }

    /**
     * Test submissions that need grading output after one ungraded submission
     */
    public function test_submissions_need_grading() {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Setup the assignment.
        $this->setUser($teacher);
        $time = time();
        $assign = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
            ]);
        $PAGE->set_url(new \moodle_url('/mod/assign/view.php', [
            'id' => $assign->get_course_module()->id,
            'action' => 'grading',
        ]));

        // Check for 0 submissions.
        $summary = $assign->view('viewcourseindex');

        $this->assertStringContainsString('/mod/assign/view.php?id=' .
            $assign->get_course_module()->id . '&amp;action=grading">' .
            get_string('numberofsubmissionsneedgradinglabel', 'assign', 0) . '</a>', $summary);

        // Simulate an assignment submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Check for 1 ungraded submission.
        $this->setUser($teacher);

        $summary = $assign->view('viewcourseindex');

        $this->assertStringContainsString('/mod/assign/view.php?id=' .
            $assign->get_course_module()->id .  '&amp;action=grading">' .
            get_string('numberofsubmissionsneedgradinglabel', 'assign', 1) . '</a>', $summary);
    }

    /**
     * Test that attachments should not be provided if \assign->show_intro returns false.
     *
     * @covers \assign::should_provide_intro_attachments
     */
    public function test_should_provide_intro_attachments_with_show_intro_disabled() {
        $this->resetAfterTest();
        $futuredate = time() + 300;
        list($assign, $instance, $student) = $this->create_submission([
            'alwaysshowdescription' => '0',
            'allowsubmissionsfromdate' => $futuredate,
        ]);
        $this->assertFalse($assign->should_provide_intro_attachments($student->id));
    }

    /**
     * Test that attachments should be provided if user has capability to manage activity.
     *
     * @covers \assign::should_provide_intro_attachments
     */
    public function test_should_provide_intro_attachments_with_bypass_capability() {
        $this->resetAfterTest();
        list($assign, $instance, $student) = $this->create_submission([
            'submissionattachments' => 1,
        ]);
        // Provide teaching role to student1 so they are able to bypass time limit restrictions on viewing attachments.
        $this->getDataGenerator()->enrol_user($student->id, $instance->course, 'editingteacher');
        $this->assertTrue($assign->should_provide_intro_attachments($student->id));
    }

    /**
     * Test that attachments should be provided if submissionattachments is disabled.
     *
     * @covers \assign::should_provide_intro_attachments
     */
    public function test_should_provide_intro_attachments_with_submissionattachments_disabled() {
        $this->resetAfterTest();
        list($assign, $instance, $student) = $this->create_submission();
        $this->assertTrue($assign->should_provide_intro_attachments($student->id));
    }

    /**
     * Test that attachments should not be provided if submissionattachments is enabled with no open submission.
     *
     * @covers \assign::should_provide_intro_attachments
     */
    public function test_should_provide_intro_attachments_with_submissionattachments_enabled_and_submissions_closed() {
        $this->resetAfterTest();
        // Set cut-off date to the past.
        list($assign, $instance, $student) = $this->create_submission([
            'timelimit' => '300',
            'submissionattachments' => 1,
            'cutoffdate' => time() - 300,
        ]);
        $this->assertFalse($assign->should_provide_intro_attachments($student->id));
    }

    /**
     * Test that attachments should be provided if submissionattachments is enabled with an open submission.
     *
     * @covers \assign::should_provide_intro_attachments
     */
    public function test_should_provide_intro_attachments_submissionattachments_enabled_and_an_open_submission() {
        $this->resetAfterTest();
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student) = $this->create_submission([
            'timelimit' => '300',
            'submissionattachments' => 1,
        ]);

        // Open a submission.
        $assign->get_user_submission($student->id, true);

        $this->assertTrue($assign->should_provide_intro_attachments($student->id));
    }

    /**
     * Test that a submission using a time limit is currently open.
     *
     * @covers \assign::is_attempt_in_progress
     */
    public function test_is_attempt_in_progress_with_open_submission() {
        global $DB;
        $this->resetAfterTest();
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student) = $this->create_submission([
            'timelimit' => '300',
        ]);
        $submission = $assign->get_user_submission($student->id, true);
        // Set a timestarted.
        $submission->timestarted = time() - 300;
        $DB->update_record('assign_submission', $submission);
        $this->assertTrue($assign->is_attempt_in_progress());
    }

    /**
     * Test that a submission using a time limit is started without a start time.
     *
     * @covers \assign::is_attempt_in_progress
     */
    public function test_is_attempt_in_progress_with_open_submission_and_no_timestarted() {
        $this->resetAfterTest();
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student) = $this->create_submission([
            'timelimit' => '300',
        ]);
        $assign->get_user_submission($student->id, true);
        $this->assertFalse($assign->is_attempt_in_progress());
    }

    /**
     * Test that a submission using a time limit is currently not open.
     *
     * @covers \assign::is_attempt_in_progress
     */
    public function test_is_attempt_in_progress_with_no_open_submission() {
        global $DB;
        $this->resetAfterTest();
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student) = $this->create_submission([
            'timelimit' => '300',
        ]);
        // Clear all current submissions.
        $DB->delete_records('assign_submission', ['assignment' => $instance->id]);
        $this->assertFalse($assign->is_attempt_in_progress());
    }

    /**
     * Create a submission for testing.
     * @param  array $params Optional params to use for creating assignment instance.
     * @return array an array containing all the required data for testing
     */
    protected function create_submission(array $params = []) {
        global $DB;

        // Create a course and assignment and users.
        $course = self::getDataGenerator()->create_course(array('groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1));

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params = array_merge(array(
            'course' => $course->id,
            'assignsubmission_file_maxfiles' => 1,
            'assignsubmission_file_maxsizebytes' => 1024 * 1024,
            'assignsubmission_onlinetext_enabled' => 1,
            'assignsubmission_file_enabled' => 1,
            'submissiondrafts' => 1,
            'assignfeedback_file_enabled' => 1,
            'assignfeedback_comments_enabled' => 1,
            'attemptreopenmethod' => ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
            'sendnotifications' => 0
        ), $params);

        set_config('submissionreceipts', 0, 'assign');

        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);

        $assign = new \mod_assign_testable_assign($context, $cm, $course);

        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        $this->setUser($student);

        // Create a student1 with an online text submission.
        // Simulate a submission.
        $submission = $assign->get_user_submission($student->id, true);

        $data = new \stdClass();
        $data->onlinetext_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text with a <a href="@@PLUGINFILE@@/intro.txt">link</a>',
            'format' => FORMAT_MOODLE);

        $draftidfile = file_get_unused_draft_itemid();
        $usercontext = \context_user::instance($student->id);
        $filerecord = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidfile,
            'filepath'  => '/',
            'filename'  => 't.txt',
        );
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'text contents');

        $data->files_filemanager = $draftidfile;

        $notices = array();
        $assign->save_submission($data, $notices);

        return array($assign, $instance, $student);
    }

    /**
     * Test user filtering by First name, Last name and Submission status.
     *
     * @covers \assign::is_userid_filtered
     */
    public function test_is_userid_filtered() {
        $this->resetAfterTest();

        // Generate data and simulate student submissions.
        $course = $this->getDataGenerator()->create_course();
        $params1 = ['firstname' => 'Valentin', 'lastname' => 'Ivanov'];
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student', $params1);
        $params2 = ['firstname' => 'Nikolay', 'lastname' => 'Petrov'];
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student', $params2);
        $assign = $this->create_instance($course, ['assignsubmission_onlinetext_enabled' => 1]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($student1);
        $submission = $assign->get_user_submission($student1->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student1->id, true, false);
        $this->setUser($student2);
        $submission = $assign->get_user_submission($student2->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $assign->testable_update_submission($submission, $student2->id, true, false);
        $this->setUser($teacher);

        // By default, both users should match filters.
        $this->AssertTrue($assign->is_userid_filtered($student1->id));
        $this->AssertTrue($assign->is_userid_filtered($student2->id));

        // Filter by First name starting with V.
        $_GET['tifirst'] = 'V';
        $this->AssertTrue($assign->is_userid_filtered($student1->id));
        $this->AssertFalse($assign->is_userid_filtered($student2->id));

        // Add Last name to filter out both users.
        $_GET['tilast'] = 'G';
        $this->AssertFalse($assign->is_userid_filtered($student1->id));
        $this->AssertFalse($assign->is_userid_filtered($student2->id));

        // Unsetting variables doesn't change behaviour because filters are stored in user preferences.
        unset($_GET['tifirst']);
        unset($_GET['tilast']);
        $this->AssertFalse($assign->is_userid_filtered($student1->id));
        $this->AssertFalse($assign->is_userid_filtered($student2->id));

        // Reset table preferences.
        $_GET['treset'] = '1';
        $this->AssertTrue($assign->is_userid_filtered($student1->id));
        $this->AssertTrue($assign->is_userid_filtered($student2->id));

        // Display users with submitted submissions only.
        set_user_preference('assign_filter', ASSIGN_SUBMISSION_STATUS_SUBMITTED);
        $this->AssertTrue($assign->is_userid_filtered($student1->id));
        $this->AssertFalse($assign->is_userid_filtered($student2->id));

        // Display users with drafts.
        set_user_preference('assign_filter', ASSIGN_SUBMISSION_STATUS_DRAFT);
        $this->AssertFalse($assign->is_userid_filtered($student1->id));
        $this->AssertTrue($assign->is_userid_filtered($student2->id));

        // Reset the filter.
        set_user_preference('assign_filter', '');
        $this->AssertTrue($assign->is_userid_filtered($student1->id));
        $this->AssertTrue($assign->is_userid_filtered($student2->id));
    }
}
