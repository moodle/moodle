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
 * Database module external functions tests
 *
 * @package    mod_data
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Database module external functions tests
 *
 * @package    mod_data
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class mod_data_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->data = $this->getDataGenerator()->create_module('data', array('course' => $this->course->id));
        $this->context = context_module::instance($this->data->cmid);
        $this->cm = get_coursemodule_from_instance('data', $this->data->id);

        // Create users.
        $this->student1 = self::getDataGenerator()->create_user();
        $this->student2 = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student1->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->student2->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /**
     * Test get databases by courses
     */
    public function test_mod_data_get_databases_by_courses() {
        global $DB;

        $this->resetAfterTest(true);

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Set to the student user.
        self::setUser($student);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // First database.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $database1 = self::getDataGenerator()->create_module('data', $record);

        // Second database.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course2->id;
        $database2 = self::getDataGenerator()->create_module('data', $record);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Users enrolments.
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course1->id, $teacherrole->id, 'manual');

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $student->id, $studentrole->id);

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'comments', 'timeavailablefrom',
                            'timeavailableto', 'timeviewfrom', 'timeviewto', 'requiredentries', 'requiredentriestoview',
                            'intro', 'introformat', 'introfiles', 'maxentries', 'rssarticles', 'singletemplate', 'listtemplate',
                            'listtemplateheader', 'listtemplatefooter', 'addtemplate', 'rsstemplate', 'rsstitletemplate',
                            'csstemplate', 'jstemplate', 'asearchtemplate', 'approval', 'defaultsort', 'defaultsortdir', 'manageapproved');

        // Add expected coursemodule.
        $database1->coursemodule = $database1->cmid;
        $database1->introfiles = [];
        $database2->coursemodule = $database2->cmid;
        $database2->introfiles = [];

        $expected1 = array();
        $expected2 = array();
        foreach ($expectedfields as $field) {
            if ($field == 'approval' or $field == 'manageapproved') {
                $database1->{$field} = (bool) $database1->{$field};
                $database2->{$field} = (bool) $database2->{$field};
            }
            $expected1[$field] = $database1->{$field};
            $expected2[$field] = $database2->{$field};
        }
        $expected1['comments'] = (bool) $expected1['comments'];
        $expected2['comments'] = (bool) $expected2['comments'];

        $expecteddatabases = array();
        $expecteddatabases[] = $expected2;
        $expecteddatabases[] = $expected1;

        // Call the external function passing course ids.
        $result = mod_data_external::get_databases_by_courses(array($course2->id, $course1->id));
        $result = external_api::clean_returnvalue(mod_data_external::get_databases_by_courses_returns(), $result);
        $this->assertEquals($expecteddatabases, $result['databases']);

        // Call the external function without passing course id.
        $result = mod_data_external::get_databases_by_courses();
        $result = external_api::clean_returnvalue(mod_data_external::get_databases_by_courses_returns(), $result);
        $this->assertEquals($expecteddatabases, $result['databases']);

        // Unenrol user from second course and alter expected databases.
        $enrol->unenrol_user($instance2, $student->id);
        array_shift($expecteddatabases);

        // Call the external function without passing course id.
        $result = mod_data_external::get_databases_by_courses();
        $result = external_api::clean_returnvalue(mod_data_external::get_databases_by_courses_returns(), $result);
        $this->assertEquals($expecteddatabases, $result['databases']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_data_external::get_databases_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($teacher);

        $additionalfields = array('scale', 'assessed', 'assesstimestart', 'assesstimefinish', 'editany', 'notification', 'timemodified');

        foreach ($additionalfields as $field) {
            if ($field == 'editany') {
                $database1->{$field} = (bool) $database1->{$field};
            }
            $expecteddatabases[0][$field] = $database1->{$field};
        }
        $result = mod_data_external::get_databases_by_courses();
        $result = external_api::clean_returnvalue(mod_data_external::get_databases_by_courses_returns(), $result);
        $this->assertEquals($expecteddatabases, $result['databases']);

        // Admin should get all the information.
        self::setAdminUser();

        $result = mod_data_external::get_databases_by_courses(array($course1->id));
        $result = external_api::clean_returnvalue(mod_data_external::get_databases_by_courses_returns(), $result);
        $this->assertEquals($expecteddatabases, $result['databases']);
    }

    /**
     * Test view_database invalid id.
     */
    public function test_view_database_invalid_id() {

        // Test invalid instance id.
        $this->setExpectedException('moodle_exception');
        mod_data_external::view_database(0);
    }

    /**
     * Test view_database not enrolled user.
     */
    public function test_view_database_not_enrolled_user() {

        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);

        $this->setExpectedException('moodle_exception');
        mod_data_external::view_database(0);
    }

    /**
     * Test view_database no capabilities.
     */
    public function test_view_database_no_capabilities() {
        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is allowed for students by default.
        assign_capability('mod/data:viewpage', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setExpectedException('moodle_exception');
        mod_data_external::view_database(0);
    }

    /**
     * Test view_database.
     */
    public function test_view_database() {

        // Test user with full capabilities.
        $this->setUser($this->student1);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_data_external::view_database($this->data->id);
        $result = external_api::clean_returnvalue(mod_data_external::view_database_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_data\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodledata = new \moodle_url('/mod/data/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodledata, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }
}
