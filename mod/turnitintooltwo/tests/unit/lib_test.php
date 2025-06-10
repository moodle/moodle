<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/turnitintooltwo/lib.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/classes/v1migration/v1migration.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/tests/unit/generator/lib.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/tests/unit/classes/v1migration/v1migration_test.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_assignment.class.php');

/**
 * Tests for lib file.
 *
 * @package turnitintooltwo
 */
class mod_lib_testcase extends test_lib {
    /**
     * Test that we have the correct course type.
     */
    public function test_turnitintooltwo_get_course_type() {
        $this->resetAfterTest();

        $response = turnitintooltwo_get_course_type(1);
        $this->assertEquals($response, "V1");

        $response = turnitintooltwo_get_course_type(0);
        $this->assertEquals($response, "TT");

        $response = turnitintooltwo_get_course_type("foo");
        $this->assertEquals($response, "TT");
    }

    /**
     * Test that we have the correct repository depending on the config settings.
     */
    public function test_turnitintooltwo_override_repository() {
        $this->resetAfterTest();

        // Note that $submitpapersto would only ever be 0, 1 or 2 but this is to illustrate
        // that it won't be overridden by the turnitintooltwo_override_repository method.
        $submitpapersto = 6;

        // Test that repository is not overridden for value of 0.
        set_config('repositoryoption', ADMIN_REPOSITORY_OPTION_STANDARD, 'turnitintooltwo');
        $response = turnitintooltwo_override_repository($submitpapersto);
        $this->assertEquals($response, $submitpapersto);

        // Test that repository is not overridden for value of 1.
        set_config('repositoryoption', ADMIN_REPOSITORY_OPTION_EXPANDED, 'turnitintooltwo');
        $response = turnitintooltwo_override_repository($submitpapersto);
        $this->assertEquals($response, $submitpapersto);

        // Standard Repository is being forced.
        set_config('repositoryoption', ADMIN_REPOSITORY_OPTION_FORCE_STANDARD, 'turnitintooltwo');
        $response = turnitintooltwo_override_repository($submitpapersto);
        $this->assertEquals($response, SUBMIT_TO_STANDARD_REPOSITORY);

        // No Repository is being forced.
        set_config('repositoryoption', ADMIN_REPOSITORY_OPTION_FORCE_NO, 'turnitintooltwo');
        $response = turnitintooltwo_override_repository($submitpapersto);
        $this->assertEquals($response, SUBMIT_TO_NO_REPOSITORY);

        // Institutional Repository is being forced.
        set_config('repositoryoption', ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL, 'turnitintooltwo');
        $response = turnitintooltwo_override_repository($submitpapersto);
        $this->assertEquals($response, SUBMIT_TO_INSTITUTIONAL_REPOSITORY);
    }

    /**
     * Test that the cron processes gradebook migrations.
     */
    public function test_turnitintooltwo_cron_migrate_gradebook() {
        $this->resetAfterTest();

        global $DB;

        $v1migrationtest = new mod_turnitintooltwo_v1migration_testcase();

        if (!$v1migrationtest->v1installed()) {
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
        $v1assignment = $v1migrationtest->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle);
        $v1migration = new v1migration($course->id, $v1assignment);

        // Create V2 Assignment.
        $v2assignmenttitle = "Test Assignment";
        $v2assignment = $v1migrationtest->make_test_assignment($course->id, 'turnitintooltwo', $v2assignmenttitle);

        // Set migrate gradebook to 1 so it will get migrated when we call the function.
        $DB->set_field('turnitintooltwo_submissions', "migrate_gradebook", 1);

        turnitintooltwo_cron_migrate_gradebook();

        /**
         * Test that we migrate the gradebook when using the cron workflow.
         * There should be no grades that require a migration.
         */
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignment->id, 'migrate_gradebook' => 1));
        $this->assertEquals(0, count($submissions));

        // Test that the V1 assignment has been deleted.
        $updatedassignment = $DB->get_record('turnitintool', array('id' => $v1assignment->id));
        $this->assertFalse($updatedassignment);
    }

    /**
     * Test that the cron only processes less than 1000 gradebook migrations when there are over 1000 submissions requiring a gradebook migration.
     */
    public function test_turnitintooltwo_cron_migrate_gradebook_1000() {
        $this->resetAfterTest();

        global $DB;

        $v1migrationtest = new mod_turnitintooltwo_v1migration_testcase();

        if (!$v1migrationtest->v1installed()) {
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
        $v1assignment = $v1migrationtest->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle, 1, 1);
        $v1migrationtest->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle, 1, 2);
        $v1migrationtest->make_test_assignment($course->id, 'turnitintool', $v1assignmenttitle, 1, 3);
        $v1migration = new v1migration($course->id, $v1assignment);

        // Create V2 Assignment.
        $v2assignment1 = $v1migrationtest->make_test_assignment($course->id, 'turnitintooltwo', "Test Assignment 1", 400, 1);
        $v2assignment2 = $v1migrationtest->make_test_assignment($course->id, 'turnitintooltwo', "Test Assignment 2", 400, 2);
        $v2assignment3 = $v1migrationtest->make_test_assignment($course->id, 'turnitintooltwo', "Test Assignment 3", 400, 3);

        // Set migrate gradebook to 1 so the assignments will get migrated when we call the function.
        $DB->set_field('turnitintooltwo_submissions', "migrate_gradebook", 1);

        turnitintooltwo_cron_migrate_gradebook();

        /**
         * Test that we migrate the gradebook when using the cron workflow.
         * There should be 400 grades that require a migration afterwards since migrating the third assignment would take us over 1000.
         */
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('migrate_gradebook' => 1));
        $this->assertEquals(400, count($submissions));

        // Assignments 1 and 2 should have 0 left.
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignment1->id, 'migrate_gradebook' => 1));
        $this->assertEquals(0, count($submissions));
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignment2->id, 'migrate_gradebook' => 1));
        $this->assertEquals(0, count($submissions));

        // Asssignment 3 should have 400 left.
        $submissions = $DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $v2assignment3->id, 'migrate_gradebook' => 1));
        $this->assertEquals(400, count($submissions));
    }

    /**
     * Test that the data passed in from a course reset is handled the way we expect it to be.
     */
    public function test_turnitintooltwo_reset_part_update() {
        global $DB;

        $this->resetAfterTest();

        $v2assignment = $this->make_test_tii_assignment();
        $this->make_test_parts("turnitintooltwo", $v2assignment->turnitintooltwo->id, 1);

        $part = $DB->get_record('turnitintooltwo_parts', array('turnitintooltwoid' => $v2assignment->turnitintooltwo->id));

        $update = new stdClass();
        $update->id = $part->id;
        $update->tiiassignid = $part->tiiassignid;
        $update->turnitintooltwoid = $v2assignment->turnitintooltwo->id;
        $update->partname = "Part Test";
        $update->deleted = 0;
        $update->maxmarks = 100;
        $update->dtstart = 1501597347;
        $update->dtdue = 1502202147;
        $update->dtpost = 1502202147;
        $update->unanon = 0;
        $update->submitted = 0;

        turnitintooltwo_reset_part_update($update, 1);

        $partDB = $DB->get_records('turnitintooltwo_parts', array('id' => $part->id));

        // These fields aren't set during the function we're testing but will be returned in the get_records below so will be compared in our assert.
        $update->migrated = 0;
        $update->gradesupdated = 0;

        $this->assertEquals($partDB[$part->id], $update);
    }

    /**
     * Test that the data passed in from a course reset is handled the way we expect it to be.
     */
    public function test_turnitintooltwo_generate_part_dates() {
        $this->resetAfterTest();

        $turnitintooltwoassignment = $this->make_test_tii_assignment();
        $this->make_test_parts("turnitintooltwo", $turnitintooltwoassignment->turnitintooltwo->id, 1);

        $turnitintooltwoassignment = new turnitintooltwo_assignment($turnitintooltwoassignment->turnitintooltwo->id);

        // Test functionality when we keep existing dates.
        $response_start = turnitintooltwo_generate_part_dates(0, "start", $turnitintooltwoassignment->turnitintooltwo, 1);
        $response_due   = turnitintooltwo_generate_part_dates(0, "due", $turnitintooltwoassignment->turnitintooltwo, 1);
        $response_post  = turnitintooltwo_generate_part_dates(0, "post", $turnitintooltwoassignment->turnitintooltwo, 1);

        $this->assertEquals(gmdate("Y-m-d\TH:i:s\Z", $turnitintooltwoassignment->turnitintooltwo->dtstart1), $response_start);
        $this->assertEquals(gmdate("Y-m-d\TH:i:s\Z", $turnitintooltwoassignment->turnitintooltwo->dtdue1), $response_due);
        $this->assertEquals(gmdate("Y-m-d\TH:i:s\Z", $turnitintooltwoassignment->turnitintooltwo->dtpost1), $response_post);

        // Check functionality with new dates. We won't know what the current time will be when the function is called so we check that new dates is not equal to the assignment date.

        $response_start = turnitintooltwo_generate_part_dates(1, "start", $turnitintooltwoassignment->turnitintooltwo, 1);
        $response_due   = turnitintooltwo_generate_part_dates(1, "due", $turnitintooltwoassignment->turnitintooltwo, 1);
        $response_post  = turnitintooltwo_generate_part_dates(1, "post", $turnitintooltwoassignment->turnitintooltwo, 1);

        $this->assertNotEquals(gmdate("Y-m-d\TH:i:s\Z", $turnitintooltwoassignment->turnitintooltwo->dtstart1), $response_start);
        $this->assertNotEquals(gmdate("Y-m-d\TH:i:s\Z", $turnitintooltwoassignment->turnitintooltwo->dtdue1), $response_due);
        $this->assertNotEquals(gmdate("Y-m-d\TH:i:s\Z", $turnitintooltwoassignment->turnitintooltwo->dtpost1), $response_post);

        // We can check that the start and due dates are more than 7 days ahead of the start date by comparing the timestamps returned. 7 days in seconds = 604,800
        $this->assertGreaterThanOrEqual(604800, strtotime($response_due) - strtotime($response_start));
        $this->assertGreaterThanOrEqual(604800, strtotime($response_post) - strtotime($response_start));
    }

    /**
     * Test that the data returned from the report gen speed param function is what we expect.
     */
    public function test_turnitintooltwo_get_report_gen_speed_params() {
	    $this->resetAfterTest();

	    $expected = new stdClass();
	    $expected->num_resubmissions = 3;
	    $expected->num_hours = 24;

	    $result = turnitintooltwo_get_report_gen_speed_params();

	    $this->assertEquals($expected, $result);
    }

    public function test_turnitintooltwo_availability_status() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course and assignment.
        $course = $this->getDataGenerator()->create_course();

        $turnitintooltwoassignment = $this->make_test_tii_assignment();
        $cmid = $this->make_test_module($turnitintooltwoassignment->turnitintooltwo->course,
            'turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo->id);
        $context = context_module::instance($cmid);
        $cm = $DB->get_record("course_modules", array('id' => $cmid));

        // Set to the student user.
        $generator = $this->getDataGenerator();

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        self::setUser($student);

        // Create data object for passing in.
        $data = new stdClass();
        $data->id = $cm->instance;
        $data->timeclose = 0;
        $data->timeopen = 0;

        // Usual case.
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, false);
        $this->assertEquals(true, $status);
        $this->assertCount(0, $warnings);

        // Turnitintooltwo not open.
        $data->timeopen = time() + DAYSECS;
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, false);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('notopenyet', $warnings);

        // Turnitintooltwo closed.
        $data->timeopen = 0;
        $data->timeclose = time() - DAYSECS;
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, false);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('expired', $warnings);

        // Turnitintooltwo not open and closed.
        $data->timeopen = time() + DAYSECS;
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, false);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('notopenyet', $warnings);
        $this->assertArrayHasKey('expired', $warnings);

        // Now additional checkings with different parameters values.
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, true, $context);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('notopenyet', $warnings);
        $this->assertArrayHasKey('expired', $warnings);

        // Turnitintooltwo not open.
        $data->timeopen = time() + DAYSECS;
        $data->timeclose = 0;
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, true, $context);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('notopenyet', $warnings);

        // Turnitintooltwo closed.
        $data->timeopen = 0;
        $data->timeclose = time() - DAYSECS;
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, true, $context);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('expired', $warnings);

        // Turnitintooltwo not open and closed.
        $data->timeopen = time() + DAYSECS;
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, true, $context);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('notopenyet', $warnings);
        $this->assertArrayHasKey('expired', $warnings);

        // As teacher now.
        self::setUser($teacher);

        // Turnitintooltwo not open and closed.
        $data->timeopen = time() + DAYSECS;
        list($status, $warnings) = mod_turnitintooltwo_get_availability_status($data, false);
        $this->assertEquals(false, $status);
        $this->assertArrayHasKey('notopenyet', $warnings);
        $this->assertArrayHasKey('expired', $warnings);
    }

    public function test_turnitintooltwo_update_event() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $turnitintooltwoassignment = $this->make_test_tii_assignment();
        $turnitintooltwo = $turnitintooltwoassignment->turnitintooltwo;
        $this->make_test_parts("turnitintooltwo", $turnitintooltwo->id, 1);
        $part = $DB->get_record('turnitintooltwo_parts', array('turnitintooltwoid' => $turnitintooltwo->id));

        $turnitintooltwo->intro = "Intro";
        $part->dtdue = time();

        // Create an event.
        $assignment = new turnitintooltwo_assignment(1, $turnitintooltwo);
        $assignment->create_event($turnitintooltwo->id, $part->partname, time());

        $event = $DB->get_record_select("event", " modulename = ? AND instance = ? AND name LIKE ? ",
            array('turnitintooltwo', $turnitintooltwo->id, '% - '.$part->partname));

        // Reset event values so we can then go on to check if our method will update them.
        $updatedevent = new stdClass();
        $updatedevent->id = $event->id;
        $updatedevent->userid = 0;
        $updatedevent->timestart = 0;
        if ($CFG->branch >= 33) {
            $updatedevent->timesort = 0;
            $updatedevent->type = 0;
        }
        $DB->update_record('event', $updatedevent);

        // Check the event will update for a standard call to the method.
        turnitintooltwo_update_event($turnitintooltwo, $part);
        $response = $DB->get_record("event", array("id" => $event->id));

        if ($CFG->branch >= 33) {
            $this->assertEquals(1, $response->type);
            $this->assertNotEquals(0, $response->timesort);
        }
        $this->assertNotEquals(0, $response->timestart);

        // Reset event values.
        $DB->update_record('event', $updatedevent);

        // Check the event will update for a call to the method with an extra course parameter.
        turnitintooltwo_update_event($turnitintooltwo, $part, true);
        $response = $DB->get_record("event", array("id" => $event->id));

        if ($CFG->branch >= 33) {
            $this->assertEquals(1, $response->type);
            $this->assertNotEquals(0, $response->timesort);
        }
        $this->assertNotEquals(0, $response->timestart);

        // Reset event values.
        $DB->update_record('event', $updatedevent);

        // This test is only relevant to 3.3+;
        if ($CFG->branch >= 33) {
            // Check that we can convert an old event to a new event.
            turnitintooltwo_update_event($turnitintooltwo, $part, null, true);
            $response = $DB->get_record("event", array("id" => $event->id));

            if ($CFG->branch >= 33) {
                $this->assertEquals(1, $response->type);
                $this->assertNotEquals(0, $response->timesort);
            }
            $this->assertNotEquals(0, $response->timestart);

            // We can check that a second call to convert an event will not update the event by resetting only the
            // timestart value and checking it is not updated,
            $updatedevent = new stdClass();
            $updatedevent->id = $event->id;
            $updatedevent->timestart = 0;
            $DB->update_record('event', $updatedevent);

            // This call should not update values, so timestart should still equal 0.
            turnitintooltwo_update_event($turnitintooltwo, $part, null, true);
            $response = $DB->get_record("event", array("id" => $event->id));

            $this->assertEquals(0, $response->timestart);
        }

        // Remove event and check that the update event method will create one if one doesn't exist.
        $DB->delete_records('event', array('id' => $updatedevent->id));
        $this->assertEquals(0, $DB->count_records('event', array('id' => $updatedevent->id)));

        turnitintooltwo_update_event($turnitintooltwo, $part);
        $dbselect = " name = ? ";
        $dbparams = array($turnitintooltwo->name." - ".$part->partname);
        $this->assertEquals(1, $DB->count_records_select('event', $dbselect, $dbparams));
    }
}
