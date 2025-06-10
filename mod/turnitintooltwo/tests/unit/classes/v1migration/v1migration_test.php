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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/turnitintooltwo/classes/v1migration/v1migration.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/tests/unit/generator/lib.php');
require_once($CFG->libdir . "/gradelib.php");

/**
 * Tests for classes/v1migration/v1migration
 *
 * @package turnitintooltwo
 */
class mod_turnitintooltwo_v1migration_testcase extends test_lib {

    /** Workaround for new php.7.2 warning
     * see http://php.net/manual/en/migration72.incompatible.php#migration72.incompatible.warn-on-non-countable-types
     */
    protected function countvar($count) {
        if (is_array($count) || ($count instanceof Countable)) {
            $count = count($count);
        } else if (is_int($count)) {
            $count = 1;
        } else if (!isset($count)) {
            $count = 0;
        }
        return $count;
    }

    /**
     * Test that users get migrated from the v1 to the v2 user table.
     */
    public function test_migrate_user() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $v1assignment = new stdClass();
        $v1assignment->id = 1;

        $v1migration = new v1migration(1, $v1assignment);

        $this->resetAfterTest();

        // Generate a new users to migrate.
        $user1 = $this->getDataGenerator()->create_user();

        // Create user in v1 tables.
        $turnitintooluser = new stdClass();
        $turnitintooluser->userid = $user1->id;
        $turnitintooluser->turnitin_uid = 1001;
        $turnitintooluser->turnitin_utp = 1;
        $DB->insert_record('turnitintool_users', $turnitintooluser);

        $turnitintooltwousers = $DB->get_records('turnitintool_users', array('userid' => $user1->id));

        // Migrate users to v2 tables.
        $v1migration->migrate_user($user1->id);

        $turnitintooltwousers = $DB->get_records('turnitintooltwo_users', array('userid' => $user1->id));

        $this->assertEquals(1, count($turnitintooltwousers));
    }

    /**
     * Check whether v1 is installed.
     */
    public function v1installed() {
        global $DB;

        $module = $DB->get_record('config_plugins', array('plugin' => 'mod_turnitintool'));
        return boolval($module);
    }

    /**
     * Test that the v1 migration can be set to the relevant value.
     */
    public function test_set_settings_menu_v1_installed() {
        global $DB;
        $this->resetAfterTest();

        // Are values saved correctly.
        $saved = v1migration::togglemigrationstatus( 0 );
        $this->assertTrue($saved);
        $saved = v1migration::togglemigrationstatus( 1 );
        $this->assertTrue($saved);
        $saved = v1migration::togglemigrationstatus( 2 );
        $this->assertTrue($saved);

        // If we pass in an invalid value (which should never happen) then it will be converted to 0 to prevent an unnecessary error.
        $saved = v1migration::togglemigrationstatus( 'test' );
        $module = $DB->get_record('config_plugins', array('plugin' => 'turnitintooltwo', 'name' => 'enablemigrationtool'));
        $this->assertEquals(0, $module->value);
    }


    /**
     * Make a test Turnitin assignment module for use in various test cases.
     * @param int $courseid Moodle course ID
     * @param string $modname Module name (turnitintool or turnitintooltwo)
     * @param string $assignmentname The name of the assignment.
     * @param string The number of submissions to make.
     * @param int $tiiassignid - Specify a Turnitin assignment ID - use when creating multiple assignments to differentiate them.
     */
    public function make_test_assignment($courseid, $modname, $assignmentname = "", $submissions = 1, $tiiassignid = 0) {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        $assignment = new stdClass();
        $assignment->id = 1;
        $assignment->name = ($assignmentname == "") ? "Test Turnitin Assignment" : $assignmentname;
        $assignment->course = $courseid;

        // Initialise fields.
        $nullcheckfields = array('grade', 'allowlate', 'reportgenspeed', 'submitpapersto', 'spapercheck', 'internetcheck', 'journalcheck', 'introformat',
                            'studentreports', 'dateformat', 'usegrademark', 'gradedisplay', 'autoupdates', 'commentedittime', 'commentmaxsize',
                            'autosubmission', 'shownonsubmission', 'excludebiblio', 'excludequoted', 'excludevalue', 'erater', 'erater_handbook',
                            'erater_spelling', 'erater_grammar', 'erater_usage', 'erater_mechanics', 'erater_style', 'transmatch', 'excludetype', 'perpage');

        // Set all fields to null.
        foreach ($nullcheckfields as $field) {
            $assignment->$field = null;
        }

        // Set default values and save module.
        $v1migration = new v1migration($courseid, $assignment);
        $v1migration->set_default_values();

        $assignment->id = $DB->insert_record($modname, $assignment);

        // Create Assignment Part.
        $parts = $this->make_test_parts($modname, $assignment->id, 1, $tiiassignid);
        $part = current($parts);

        // Create Assignment Submission.
        $this->make_test_submission($modname, $part->id, $assignment->id, $submissions);

        // Set up a course module.
        $addtocm = ($modname == 'turnitintool') ? true : false;
        $this->make_test_module($courseid, $modname, $assignment->id, $addtocm);

        return $assignment;
    }

    /**
     * Create a test submission on the specified assignment part.
     * @param string $modname Module name (turnitintool or turnitintooltwo)
     * @param int $partid Part ID
     * @param int $assignmentid Assignment Module ID
     * @param int $amount Number of submissions to make.
     */
    public function make_test_submission($modname, $partid, $assignmentid, $amount = 1) {
        global $DB;

        $modulevar = $modname.'id';

        for ($i = 1; $i <= $amount; $i++) {
            $submission = new stdClass();
            $submission->userid = $i;
            $submission->$modulevar = $assignmentid;
            $submission->submission_part = $partid;
            $submission->submission_title = "Test Submission " . $i;
            $submission->submission_hash = $i.'_'.$assignmentid.'_'.$partid;

            $DB->insert_record($modname.'_submissions', $submission);
        }
    }

    /**
     * Create a grade entry for a student on an assignment.
     *
     * @param string $modname Module name (turnitintool or turnitintooltwo)
     * @param int $assignmentid Assignment Module ID
     * @param int $courseid Course ID
     * @param int $userid The user we want to grade for.
     * @param int $grade The grade we want to set.
     */
    public function make_test_grade($module, $assignmentid, $courseid, $userid, $grade) {
        $cm = get_coursemodule_from_instance($module, $assignmentid);

        $grades = new stdClass();
        $grades->rawgrade = $grade;
        $grades->userid = $userid;

        $params['idnumber'] = $cm->idnumber;

        grade_update('mod/'.$module, $courseid, 'mod', $module, $assignmentid, 0, $grades, $params);
    }

    /**
     * Test that all values which can't be null get initialised.
     */
    public function test_set_default_values() {

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Fields to set to null.
        $nullcheckfields = array('grade', 'allowlate', 'reportgenspeed', 'submitpapersto', 'spapercheck', 'internetcheck', 'journalcheck', 'introformat',
                            'studentreports', 'dateformat', 'usegrademark', 'gradedisplay', 'autoupdates', 'commentedittime', 'commentmaxsize',
                            'autosubmission', 'shownonsubmission', 'excludebiblio', 'excludequoted', 'excludevalue', 'erater', 'erater_handbook',
                            'erater_spelling', 'erater_grammar', 'erater_usage', 'erater_mechanics', 'erater_style', 'transmatch', 'excludetype', 'perpage');

        // Create Migration Assignment object.
        $v1assignment = new stdClass();
        $v1assignment->id = 1;

        $v1migration = new v1migration(1, $v1assignment);

        // Set all fields to check to null.
        foreach ($nullcheckfields as $field) {
            $v1migration->v1assignment->$field = null;
        }

        $v1migration->set_default_values();

        // Assert that all fields are no longer null.
        foreach ($nullcheckfields as $field) {
            $this->assertNotNull($v1migration->v1assignment->$field);
        }
    }

    /**
     * Test that v1 assignment is hidden and renamed.
     */
    public function test_hide_v1_assignment() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Create Assignment.
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool');
        $v1migration = new v1migration($course->id, $v1assignment);

        $v1migration->hide_v1_assignment();

        // Test that assignment has been renamed.
        $updatedassignment = $DB->get_record('turnitintool', array('id' => $v1assignment->id));
        $this->assertStringContainsString("(Migration in progress...)", $updatedassignment->name);

        // Test that assignment has been hidden.
        $cm = get_coursemodule_from_instance('turnitintool', $v1assignment->id);
        $this->assertEquals(0, $cm->visible);
        $this->assertEquals(0, $cm->visibleold);
    }

    public function test_setup_v2_module() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Create Assignment.
        $v1assignmenttitle = "Test ".uniqid();
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
        $v1migration = new v1migration($course->id, $v1assignment);

        // Get the section for the V1 assignment.
        $v1cm = get_coursemodule_from_instance('turnitintool', $v1assignment->id);

        // Create V2 Assignment.
        $v2assignment = $this->make_test_assignment($course->id, 'turnitintooltwo');

        $v1migration->setup_v2_module($course->id, $v2assignment->id);

        // Test that assignment has been assigned a course section.
        $v2cm = get_coursemodule_from_instance('turnitintooltwo', $v2assignment->id);

        $this->assertEquals($v1cm->section, $v2cm->section);
    }

    /**
     * Test that the assignment gets migrated from the v1 to the v2 tables.
     */
    public function test_migrate_assignment() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);

        // Create Assignment.
        $v1assignmenttitle = "Test ".uniqid();
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
        $v1migration = new v1migration($course->id, $v1assignment);

        // Verify there are no v2 assignments, parts or submissions.
        $v2assignments = $DB->get_records('turnitintooltwo');
        $v2parts = $DB->get_records('turnitintooltwo_parts');
        $v2submissions = $DB->get_records('turnitintooltwo_submissions');
        $this->assertEquals(0, count($v2assignments));
        $this->assertEquals(0, count($v2parts));
        $this->assertEquals(0, count($v2submissions));

        // Set grades updated timestamp.
        $timestamp = time();
        $_SESSION["migrationtool"][$v1assignment->id]["gradesupdated"] = $timestamp;

        $v2assignmentid = $v1migration->migrate();

        // Verify assignment has migrated.
        $v2assignment = $DB->get_record('turnitintooltwo', array('id' => $v2assignmentid));
        $this->assertEquals($v1assignmenttitle, $v2assignment->name);

        // Verify part has migrated.
        $v2parts = $DB->get_records('turnitintooltwo_parts', array('turnitintooltwoid' => $v2assignmentid));
        $this->assertEquals(1, count($v2parts));

        // Verify grade timestamp has been saved.
        $v2part = current($v2parts);
        $this->assertEquals($timestamp, $v2part->gradesupdated);

        // Verify submission has migrated.
        $v2parts = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignmentid));
        $this->assertEquals(1, count($v2parts));

        // Verify Session value has been set correctly after migration.
        $cm = get_coursemodule_from_instance('turnitintooltwo', $_SESSION['migrationtool'][$v1assignment->id]);
        $this->assertEquals($v2assignmentid, $_SESSION['migrationtool'][$v1assignment->id]);
    }

    /**
     * Test that if there are multiple submissions for a user in a part that only the latest gets migrated.
     */
    public function test_migrate_multiple_submission() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);

        // Create Assignment.
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool');

        // Get part details.
        $part = $DB->get_record('turnitintool_parts', array('turnitintoolid' => $v1assignment->id));

        // Create extra submission for user 1.
        $submission = new stdClass();
        $submission->userid = 1;
        $submission->turnitintoolid = $v1assignment->id;
        $submission->submission_part = $part->id;
        $submission->submission_title = "Test Duplicate Submission";
        $submission->submission_hash = uniqid();
        $DB->insert_record('turnitintool_submissions', $submission);

        // Verify there are two submissions to v1 assignment.
        $v1submissions = $DB->get_records('turnitintool_submissions', array('submission_part' => $part->id));
        $this->assertEquals(2, count($v1submissions));

        // Migrate assignment.
        $v1migration = new v1migration($course->id, $v1assignment);
        $v2assignmentid = $v1migration->migrate();

        // Verify only one submission has migrated.
        $v2submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignmentid));
        $this->assertEquals(1, count($v2submissions));
    }

    /**
     * Test that if there are multiple unenrolled users then they all get migrated.
     */
    public function test_migrate_multiple_unenrolled_users() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);

        // Create Assignment.
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', '', 0);

        // Get part details.
        $part = $DB->get_record('turnitintool_parts', array('turnitintoolid' => $v1assignment->id));

        // Create two submissions.
        $this->make_test_submission('turnitintool', $part->id, $v1assignment->id, 2);

        // Verify there are two submissions to v1 assignment.
        $v1submissions = $DB->get_records('turnitintool_submissions', array('submission_part' => $part->id));
        $this->assertEquals(2, count($v1submissions));

        // Update submissions to be from non moodle users.
        $v1submissions = $DB->get_records('turnitintool_submissions', array('submission_part' => $part->id));
        foreach ($v1submissions as $v1submission) {
            $updatesubmission = new stdClass();
            $updatesubmission->id = $v1submission->id;
            $updatesubmission->userid = 0;
            $updatesubmission->submission_nmuserid = uniqid();
            $updatesubmission->submission_nmfirstname = 'Test';
            $updatesubmission->submission_nmlastname = 'User'.uniqid();

            $DB->update_record('turnitintool_submissions', $updatesubmission);
        }

        // Migrate assignment.
        $v1migration = new v1migration($course->id, $v1assignment);
        $v2assignmentid = $v1migration->migrate();

        // Verify both submissions have migrated.
        $v2submissions = $DB->get_records('turnitintooltwo_submissions',
                                            array('turnitintooltwoid' => $v2assignmentid,
                                                    'userid' => 0));
        $this->assertEquals(2, count($v2submissions));
    }

    /**
     * Test the modal that appears when asked to migrate.
     */
    public function test_migrate_course() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $v1assignment = new stdClass();
        $v1assignment->id = 1;
        $v1migration = new v1migration(1, $v1assignment);

        $this->resetAfterTest();

        // Values for our TII course.
        $v1tiicourse = 9;
        $v2tiicourse = 12;

        // Create a V1 course and get it.
        $course = new stdClass();
        $course->courseid = 1;
        $course->ownerid = 1;
        $course->turnitin_ctl = "Test Course";
        $course->turnitin_cid = $v1tiicourse;
        $course->course_type = "TT";

        // Insert the course to the turnitintooltwo courses table.
        $DB->insert_record('turnitintool_courses', $course);
        $v1course = $DB->get_record('turnitintool_courses', array('courseid' => 1));

        /* Test 1. V1 migration with no existing V2 courses.
           Should create a new course entry in turnitintooltwo_courses table with the same turnitin_cid as above, course type TT.*/
        $response = $v1migration->migrate_course($v1course);
        $v2courses = $DB->get_records('turnitintooltwo_courses', array('turnitin_cid' => $v1tiicourse, 'course_type' => "TT"));
        $this->assertEquals(1, count($v2courses));
        $this->assertEquals($course->courseid, $response->courseid);
        $this->assertEquals($course->course_type, $response->course_type);

        // If we attempt to migrate this course again (IE migrating a second assignment on this course), there should still only be one entry.
        $response = $v1migration->migrate_course($v1course);
        $v2course = $DB->get_records('turnitintooltwo_courses', array('turnitin_cid' => $v1tiicourse, 'course_type' => "TT"));
        $this->assertEquals(1, count($v2course));
        $this->assertEquals($course->courseid, $response->courseid);
        $this->assertEquals($course->course_type, $response->course_type);

        // Clear our table.
        $DB->delete_records('turnitintooltwo_courses', array('turnitin_cid' => $v1tiicourse));

        /* Test 2. V1 migration with an existing V2 course.
           Should create a new course entry in turnitintooltwo_courses table with the same turnitin_cid as above, course type V1.
           Legacy field should be set to 1 on these tests. */

        // Create our initial V2 course.
        $course = new stdClass();
        $course->courseid = 1;
        $course->ownerid = 1;
        $course->turnitin_ctl = "Test Course";
        $course->turnitin_cid = $v2tiicourse;
        $course->course_type = "TT";

        // Insert the course to the turnitintooltwo courses table.
        $DB->insert_record('turnitintooltwo_courses', $course);

        $response = $v1migration->migrate_course($v1course);
        $v2courses = $DB->get_records('turnitintooltwo_courses', array('turnitin_cid' => $v1tiicourse, 'course_type' => "V1"));
        $this->assertEquals(1, count($v2courses));
        $this->assertEquals(1, $this->countvar($v1migration->v1assignment->legacy));
        $this->assertEquals($course->courseid, $response->courseid);
        $this->assertEquals("V1", $response->course_type);

        // We expect 0 results here since we inserted a course type of TT.
        $v2courses = $DB->get_records('turnitintooltwo_courses', array('turnitin_cid' => $v1tiicourse, 'course_type' => "TT"));
        $this->assertEquals(0, count($v2courses));
        $this->assertEquals(1, $this->countvar($v1migration->v1assignment->legacy));
        $this->assertEquals($course->courseid, $response->courseid);
        $this->assertEquals("V1", $response->course_type);

        // If we attempt to migrate this course again (IE migrating a second assignment on this course), there should still only be one entry.
        $response = $v1migration->migrate_course($v1course);
        $v2courses = $DB->get_records('turnitintooltwo_courses', array('turnitin_cid' => $v1tiicourse, 'course_type' => "V1"));
        $this->assertEquals(1, count($v2courses));
        $this->assertEquals(1, $this->countvar($v1migration->v1assignment->legacy));
        $this->assertEquals($course->courseid, $response->courseid);
        $this->assertEquals("V1", $response->course_type);

        // And still 0 results for this one.
        $v2courses = $DB->get_records('turnitintooltwo_courses', array('turnitin_cid' => $v1tiicourse, 'course_type' => "TT"));
        $this->assertEquals(0, count($v2courses));
        $this->assertEquals(1, $this->countvar($v1migration->v1assignment->legacy));
        $this->assertEquals($course->courseid, $response->courseid);
        $this->assertEquals("V1", $response->course_type);
    }

    /**
     * Test that the gradebook updates perform.
     */
    public function test_migrate_gradebook() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);

        // Create V1 Assignment.
        $v1assignmenttitle = "Test Assignment (Migrated)";
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
        $v1migration = new v1migration($course->id, $v1assignment);

        // Create V2 Assignment.
        $v2assignmenttitle = "Test Assignment";
        $v2assignment = $this->make_test_assignment($course->id, 'turnitintooltwo', $v2assignmenttitle);

        // Set migrate gradebook to 1 so it will get migrated when we call the function.
        $DB->set_field('turnitintooltwo_submissions', "migrate_gradebook", 1);

        // Test that this gradebook update was performed.
        $response = $v1migration->migrate_gradebook($v2assignment->id, $v1assignment->id, $course->id);
        $this->assertEquals("migrated", $response);

        // There should be no grades that require a migration.
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignment->id, 'migrate_gradebook' => 1));
        $this->assertEquals(0, count($submissions));

        // Create V2 Assignment with 201 submissions.
        $v2assignmenttitle = "Test Assignment";
        $v2assignment = $this->make_test_assignment($course->id, 'turnitintooltwo', $v2assignmenttitle, 201);

        $DB->set_field('turnitintooltwo_submissions', "migrate_gradebook", 1);

        // Test that we return cron when there are more than 200 submissions.
        $response = $v1migration->migrate_gradebook($v2assignment->id, $v1assignment->id, $course->id);
        $this->assertEquals("cron", $response);

        // All grades should still require migration.
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignment->id, 'migrate_gradebook' => 1));
        $this->assertEquals(201, count($submissions));

        // Test that we return migrated when using the cron workflow.
        $response = $v1migration->migrate_gradebook($v2assignment->id, $v1assignment->id, $course->id, "cron");
        $this->assertEquals("migrated", $response);

        // There should be no grades that require a migration.
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignment->id, 'migrate_gradebook' => 1));
        $this->assertEquals(0, count($submissions));
    }

    /**
     * Test that the post migration task works as expected.
     */
    public function test_post_migration() {

        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);

        // Create V1 Assignment.
        $v1assignmenttitle = "Test Assignment (Migration in progress...)";
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
        $v1migration = new v1migration($course->id, $v1assignment);

        // Create V2 Assignment.
        $v2assignmenttitle = "Test Assignment";
        $v2assignment = $this->make_test_assignment($course->id, 'turnitintooltwo', $v2assignmenttitle);

        // Perform post-migration tasks - ie deletion of V1 assignment.
        $response = $v1migration->post_migration($v2assignment->id);

        // Check that the V1 assignment no longer exists.
        $assignments = $DB->get_records('turnitintool', array('id' => $v1assignment->id));
        $this->assertEquals(0, count($assignments));

        // Should return success.
        $this->assertEquals("success", $response);
    }

    public function test_get_grades_array() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);

        $v1assignmenttitle = "Test ".uniqid();
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
        $v1migration = new v1migration($course->id, $v1assignment);

        // create a user and enrol them on the course.
        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        // Create V2 Assignment.
        $v2assignmenttitle = "Test Assignment";
        $v2assignment = $this->make_test_assignment($course->id, 'turnitintooltwo', $v2assignmenttitle, 10);

        $v1migration->setup_v2_module($course->id, $v2assignment->id);

        // Set and get the grades for this assignment.
        $this->make_test_grade("turnitintooltwo", $v2assignment->id, $course->id, $student->id, 10);
        $response = v1migration::get_grades_array("turnitintooltwo", $v2assignment->id, $course->id);

        // Should return an empty array as there are no grades.
        $this->assertEquals(array($student->id => 10), $response);
    }

    public function test_handle_overridden_grade() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);

        // Create V1 Assignment.
        $v1assignmenttitle = "Test Assignment";
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
        $v1migration = new v1migration($course->id, $v1assignment);

        // Create V2 Assignment.
        $v2assignmenttitle = "Test Assignment";
        $v2assignment = $this->make_test_assignment($course->id, 'turnitintooltwo', $v2assignmenttitle, 10);

        $v1migration->setup_v2_module($course->id, $v2assignment->id);

        $this->make_test_grade("turnitintooltwo", $v2assignment->id, $course->id, 1, 10);

        // Call the overriden grades function with a different grade to the one set above.
        v1migration::handle_overridden_grade(20, 1, $v2assignment->id, $course->id);

        $grading_info = grade_get_grades($course->id, 'mod', 'turnitintooltwo', $v2assignment->id, 1);

        // Should return an empty array as there are no grades.
        $this->assertEquals(20, $grading_info->items[0]->grades[1]->grade);
        $this->assertGreaterThan(0, $grading_info->items[0]->grades[1]->overridden);
    }

    /**
     * Test that the data returned is the data we expect based on the passed in parameters.
     */
    public function test_turnitintooltwo_getassignments() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        $_POST = array();
        $_POST["sEcho"] = 1;
        $_POST["iColumns"] = 4;
        $_POST["sColumns"] = ",,,";
        $_POST["iDisplayStart"] = 0;
        $_POST["iDisplayLength"] = 10;
        $_POST["mDataProp_0"] = 0;
        $_POST["sSearch_0"] = "";
        $_POST["bRegex_0"] = "false";
        $_POST["bSearchable_0"] = "true";
        $_POST["bSortable_0"] = "false";
        $_POST["mDataProp_1"] = 1;
        $_POST["sSearch_1"] = "";
        $_POST["bRegex_1"] = "false";
        $_POST["bSearchable_1"] = "true";
        $_POST["bSortable_1"] = "true";
        $_POST["mDataProp_2"] = 2;
        $_POST["sSearch_2"] = "";
        $_POST["bRegex_2"] = "false";
        $_POST["bSearchable_2"] = "true";
        $_POST["bSortable_2"] = "true";
        $_POST["mDataProp_3"] = 3;
        $_POST["sSearch_3"] = "";
        $_POST["bRegex_3"] = "false";
        $_POST["bSearchable_3"] = "false";
        $_POST["bSortable_3"] = "true";
        $_POST["sSearch"] = "";
        $_POST["bRegex"] = "false";
        $_POST["iSortCol_0"] = 2;
        $_POST["sSortDir_0"] = "asc";
        $_POST["iSortingCols"] = 1;
        $_POST["_"] = 1494857276336;
        $numAssignments = 20;
        $shownRecords = 10;

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();
        // Link course to Turnitin.
        $courselink = new stdClass();
        $courselink->courseid = $course->id;
        $courselink->ownerid = 0;
        $courselink->turnitin_ctl = "Test Course";
        $courselink->turnitin_cid = 0;
        $DB->insert_record('turnitintool_courses', $courselink);
        $update = new stdClass();
        $update->migrated = 1;
        for ($i = 0; $i < $numAssignments; $i++) {
            // Add variation to assignment titles for use in search test.
            if ($i % 2 == 0) {
                $v1assignmenttitle = "Test Assignment " . rand(1, 100);
            } else {
                $v1assignmenttitle = "Coursework " . rand(1, 100);
            }
            $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
            // Set the first 5 to migrated.
            if ($i < 5) {
                $update->id = $v1assignment->id;
                $DB->update_record('turnitintool', $update);
            }
        }
        // Create our output array.
        $assignments = $DB->get_records('turnitintool', NULL, NULL, "id, name, migrated");
        $outputrows = array();
        foreach ($assignments as $key => $value) {
            if ($value->migrated == 1) {
                $checkbox = '<input class="browser_checkbox" type="checkbox" value="'.$value->id.'" name="assignmentids[]" />';
                $sronly = html_writer::tag('span', get_string('yes', 'turnitintooltwo'), array('class' => 'sr-only'));
                $migrationValue = html_writer::tag('span', $sronly, array('class' => 'fa fa-check'));

                $assignmenttitle = format_string($value->name);
            } else {
                $checkbox = "";
                $sronly = html_writer::tag('span', get_string('no', 'turnitintooltwo'), array('class' => 'sr-only'));
                $migrationValue = html_writer::tag('span', $sronly, array('class' => 'fa fa-times'));

                $assignmentlink = new moodle_url('/mod/turnitintool/view.php', array('a' => $value->id, 'id' => '0'));
                $assignmenttitle = html_writer::link($assignmentlink, format_string($value->name), array('target' => '_blank' ));
            }
            $outputrows[] = array($checkbox, $value->id, $assignmenttitle, $migrationValue);
        }
        $expectedoutput = array("aaData"               => $outputrows,
                                "sEcho"                => $_POST["sEcho"],
                                "iTotalRecords"        => $numAssignments,
                                "iTotalDisplayRecords" => $numAssignments);
        $response = v1migration::turnitintooltwo_getassignments();

        $this->assertEquals($expectedoutput, $response);
    }

    /**
     * Test that assignments are deleted when given an assignment.
     */
    public function test_delete_migrated_assignment() {
        global $DB;

        if (!$this->v1installed()) {
            return false;
        }

        $this->resetAfterTest();

        // Generate a new course.
        $course = $this->getDataGenerator()->create_course();

        // Create some V1 assignments.
        $v1assignment = $this->make_test_assignment($course->id, 'turnitintool', "Assignment 1", 5);
        $cm1 = get_coursemodule_from_instance('turnitintool', $v1assignment->id);

        // Check that the assignments have been created correctly.
        $v1assignments = $DB->get_records('turnitintool');
        $v1parts = $DB->get_records('turnitintool_parts');
        $v1submissions = $DB->get_records('turnitintool_submissions');
        $this->assertEquals(1, count($v1assignments));
        $this->assertEquals(1, count($v1parts));
        $this->assertEquals(5, count($v1submissions));

        // Delete the assignment.
        v1migration::delete_migrated_assignment($v1assignment->id);

        // Verify that they have been deleted.
        $v1assignments = $DB->get_records('turnitintool');
        $v1parts = $DB->get_records('turnitintool_parts');
        $v1submissions = $DB->get_records('turnitintool_submissions');
        $this->assertEquals(0, count($v1assignments));
        $this->assertEquals(0, count($v1parts));
        $this->assertEquals(0, count($v1submissions));

        // Verify that records have been removed from the course_modules table.
        $v1cm = $DB->get_records('course_modules', array('id' => $cm1->id));
        $this->assertEquals(0, count($v1cm));
    }

    /**
     * Test that the v1 and v2 account ids being used are the same.
     */
    public function test_check_account_ids() {
        global $DB;
        $this->resetAfterTest();

        // Set Account Id for v1.
        set_config('turnitin_account_id', 1234);

        // Set Account Id for v2.
        set_config('accountid', '1234', 'turnitintooltwo');

        // Account IDs should be the same.
        $enabled = v1migration::check_account_ids();
        $this->assertTrue($enabled);

        // Set different account ID for v1.
        set_config('turnitin_account_id', 5678);

        // Account IDs should be different.
        $enabled = v1migration::check_account_ids();
        $this->assertFalse($enabled);
    }

    /**
     * Test that the v1 and v2 account ids being used are the same.
     */
    public function test_output_settings_form() {
        $this->resetAfterTest();

        // Test that warning message is shown to user if they aren't allowed to edit migration tool status.
        $form = v1migration::output_settings_form(false);

        $this->assertStringContainsString(get_string('migrationtoolaccounterror', 'turnitintooltwo'), $form);
    }
}
