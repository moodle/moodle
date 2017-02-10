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
        $course = new stdClass();
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        $this->course = $this->getDataGenerator()->create_course($course);
        $this->data = $this->getDataGenerator()->create_module('data', array('course' => $this->course->id));
        $this->context = context_module::instance($this->data->cmid);
        $this->cm = get_coursemodule_from_instance('data', $this->data->id);

        // Create users.
        $this->student1 = self::getDataGenerator()->create_user();
        $this->student2 = self::getDataGenerator()->create_user();
        $this->student3 = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student1->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->student2->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->student3->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');

        $this->group1 = $this->getDataGenerator()->create_group(array('courseid' => $this->course->id));
        $this->group2 = $this->getDataGenerator()->create_group(array('courseid' => $this->course->id));
        groups_add_member($this->group1, $this->student1);
        groups_add_member($this->group1, $this->student2);
        groups_add_member($this->group2, $this->student3);
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

    /**
     * Test get_data_access_information for student.
     */
    public function test_get_data_access_information_student() {
        global $DB;
        // Modify the database to add access restrictions.
        $this->data->timeavailablefrom = time() + DAYSECS;
        $this->data->requiredentries = 2;
        $this->data->requiredentriestoview = 2;
        $DB->update_record('data', $this->data);

        // Test user with full capabilities.
        $this->setUser($this->student1);

        $result = mod_data_external::get_data_access_information($this->data->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_data_access_information_returns(), $result);

        $this->assertEquals($this->group1->id, $result['groupid']);

        $this->assertFalse($result['canmanageentries']);
        $this->assertFalse($result['canapprove']);
        $this->assertTrue($result['canaddentry']);  // It return true because it doen't check time restrictions.
        $this->assertFalse($result['timeavailable']);
        $this->assertFalse($result['inreadonlyperiod']);
        $this->assertEquals(0, $result['numentries']);
        $this->assertEquals($this->data->requiredentries, $result['entrieslefttoadd']);
        $this->assertEquals($this->data->requiredentriestoview, $result['entrieslefttoview']);
    }

    /**
     * Test get_data_access_information for teacher.
     */
    public function test_get_data_access_information_teacher() {
        global $DB;
        // Modify the database to add access restrictions.
        $this->data->timeavailablefrom = time() + DAYSECS;
        $this->data->requiredentries = 2;
        $this->data->requiredentriestoview = 2;
        $DB->update_record('data', $this->data);

        // Test user with full capabilities.
        $this->setUser($this->teacher);

        $result = mod_data_external::get_data_access_information($this->data->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_data_access_information_returns(), $result);

        $this->assertEquals(0, $result['groupid']);

        $this->assertTrue($result['canmanageentries']);
        $this->assertTrue($result['canapprove']);
        $this->assertTrue($result['canaddentry']);  // It return true because it doen't check time restrictions.
        $this->assertTrue($result['timeavailable']);
        $this->assertFalse($result['inreadonlyperiod']);
        $this->assertEquals(0, $result['numentries']);
        $this->assertEquals(0, $result['entrieslefttoadd']);
        $this->assertEquals(0, $result['entrieslefttoview']);
    }

    /**
     * Helper method to populate the database with some entries.
     *
     * @return array the entry ids created
     */
    public function populate_database_with_entries() {
        global $DB;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldtypes = array('checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url');

        $count = 1;
        // Creating test Fields with default parameter values.
        foreach ($fieldtypes as $fieldtype) {
            $fieldname = 'field-' . $count;
            $record = new StdClass();
            $record->name = $fieldname;
            $record->type = $fieldtype;
            $record->required = 1;

            $generator->create_field($record, $this->data);
            $count++;
        }
        // Get all the fields created.
        $fields = $DB->get_records('data_fields', array('dataid' => $this->data->id), 'id');

        // Populate with contents, creating a new entry.
        $contents = array();
        $contents[] = array('opt1', 'opt2', 'opt3', 'opt4');
        $contents[] = '01-01-2037'; // It should be lower than 2038, to avoid failing on 32-bit windows.
        $contents[] = 'menu1';
        $contents[] = array('multimenu1', 'multimenu2', 'multimenu3', 'multimenu4');
        $contents[] = '12345';
        $contents[] = 'radioopt1';
        $contents[] = 'text for testing';
        $contents[] = '<p>text area testing<br /></p>';
        $contents[] = array('example.url', 'sampleurl');
        $count = 0;
        $fieldcontents = array();
        foreach ($fields as $fieldrecord) {
            $fieldcontents[$fieldrecord->id] = $contents[$count++];
        }

        $this->setUser($this->student1);
        $entry11 = $generator->create_entry($this->data, $fieldcontents, $this->group1->id);
        $this->setUser($this->student2);
        $entry12 = $generator->create_entry($this->data, $fieldcontents, $this->group1->id);

        $this->setUser($this->student3);
        $entry21 = $generator->create_entry($this->data, $fieldcontents, $this->group2->id);
        return [$entry11, $entry12, $entry21];
    }

    /**
     * Test get_entries
     */
    public function test_get_entries() {
        global $DB;
        list($entry11, $entry12, $entry21) = self::populate_database_with_entries();

        // First of all, expect to see only my group entries (not other users in other groups ones).
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->data->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals($entry11, $result['entries'][0]['id']);
        $this->assertEquals($this->student1->id, $result['entries'][0]['userid']);
        $this->assertEquals($this->group1->id, $result['entries'][0]['groupid']);
        $this->assertEquals($this->data->id, $result['entries'][0]['dataid']);
        $this->assertEquals($entry12, $result['entries'][1]['id']);
        $this->assertEquals($this->student2->id, $result['entries'][1]['userid']);
        $this->assertEquals($this->group1->id, $result['entries'][1]['groupid']);
        $this->assertEquals($this->data->id, $result['entries'][1]['dataid']);
        // Other user in same group.
        $this->setUser($this->student2);
        $result = mod_data_external::get_entries($this->data->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);

        // Now try with the user in the second group that must see only one entry.
        $this->setUser($this->student3);
        $result = mod_data_external::get_entries($this->data->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(1, $result['totalcount']);
        $this->assertEquals($entry21, $result['entries'][0]['id']);
        $this->assertEquals($this->student3->id, $result['entries'][0]['userid']);
        $this->assertEquals($this->group2->id, $result['entries'][0]['groupid']);
        $this->assertEquals($this->data->id, $result['entries'][0]['dataid']);

        // Now, as teacher we should see all (we have permissions to view all groups).
        $this->setUser($this->teacher);
        $result = mod_data_external::get_entries($this->data->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(3, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);

        $entries = $DB->get_records('data_records', array('dataid' => $this->data->id), 'id');
        $this->assertCount(3, $entries);
        $count = 0;
        foreach ($entries as $entry) {
            $this->assertEquals($entry->id, $result['entries'][$count]['id']);
            $count++;
        }

        // Basic test passing the parameter (instead having to calculate it).
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->data->id, $this->group1->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);

        // Test ordering (reverse).
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->data->id, $this->group1->id, false, null, 'DESC');
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals($entry12, $result['entries'][0]['id']);

        // Test pagination.
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->data->id, $this->group1->id, false, null, null, 0, 1);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals($entry11, $result['entries'][0]['id']);

        $result = mod_data_external::get_entries($this->data->id, $this->group1->id, false, null, null, 1, 1);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals($entry12, $result['entries'][0]['id']);

        // Now test the return contents.
        data_generate_default_template($this->data, 'listtemplate', 0, false, true); // Generate a default list template.
        $result = mod_data_external::get_entries($this->data->id, $this->group1->id, true, null, null, 0, 2);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertCount(9, $result['entries'][0]['contents']);
        $this->assertCount(9, $result['entries'][1]['contents']);
        // Search for some content.
        $this->assertTrue(strpos($result['listviewcontents'], 'opt1') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'January') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'menu1') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'text for testing') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'sampleurl') !== false);
    }
}
