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

    /** @var stdClass Test module context. */
    protected $context;

    /** @var stdClass Test course.*/
    protected $course;

    /** @var stdClass Test course module. */
    protected $cm;

    /** @var  stdClass Test database activity. */
    protected $database;

    /** @var stdClass Test group 1. */
    protected $group1;

    /** @var stdClass Test group 2. */
    protected $group2;

    /** @var stdClass Test student 1. */
    protected $student1;

    /** @var stdClass Test student 2. */
    protected $student2;

    /** @var stdClass Test student 3. */
    protected $student3;

    /** @var stdClass Test student 4. */
    protected $student4;

    /** @var stdClass Student role. */
    protected $studentrole;

    /** @var stdClass Test teacher. */
    protected $teacher;

    /** @var stdClass Teacher role. */
    protected $teacherrole;

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
        $this->database = $this->getDataGenerator()->create_module('data', array('course' => $this->course->id));
        $this->context = context_module::instance($this->database->cmid);
        $this->cm = get_coursemodule_from_instance('data', $this->database->id);

        // Create users.
        $this->student1 = self::getDataGenerator()->create_user(['firstname' => 'Olivia', 'lastname' => 'Smith']);
        $this->student2 = self::getDataGenerator()->create_user(['firstname' => 'Ezra', 'lastname' => 'Johnson']);
        $this->student3 = self::getDataGenerator()->create_user(['firstname' => 'Amelia', 'lastname' => 'Williams']);
        $this->teacher = self::getDataGenerator()->create_user(['firstname' => 'Asher', 'lastname' => 'Jones']);

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
        $this->expectException('moodle_exception');
        mod_data_external::view_database(0);
    }

    /**
     * Test view_database not enrolled user.
     */
    public function test_view_database_not_enrolled_user() {

        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);

        $this->expectException('moodle_exception');
        mod_data_external::view_database(0);
    }

    /**
     * Test view_database no capabilities.
     */
    public function test_view_database_no_capabilities() {
        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is allowed for students by default.
        assign_capability('mod/data:view', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->expectException('moodle_exception');
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

        $result = mod_data_external::view_database($this->database->id);
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
        $this->database->timeavailablefrom = time() + DAYSECS;
        $this->database->requiredentries = 2;
        $this->database->requiredentriestoview = 2;
        $DB->update_record('data', $this->database);

        // Test user with full capabilities.
        $this->setUser($this->student1);

        $result = mod_data_external::get_data_access_information($this->database->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_data_access_information_returns(), $result);

        $this->assertEquals($this->group1->id, $result['groupid']);

        $this->assertFalse($result['canmanageentries']);
        $this->assertFalse($result['canapprove']);
        $this->assertTrue($result['canaddentry']);  // It return true because it doen't check time restrictions.
        $this->assertFalse($result['timeavailable']);
        $this->assertFalse($result['inreadonlyperiod']);
        $this->assertEquals(0, $result['numentries']);
        $this->assertEquals($this->database->requiredentries, $result['entrieslefttoadd']);
        $this->assertEquals($this->database->requiredentriestoview, $result['entrieslefttoview']);
    }

    /**
     * Test get_data_access_information for teacher.
     */
    public function test_get_data_access_information_teacher() {
        global $DB;
        // Modify the database to add access restrictions.
        $this->database->timeavailablefrom = time() + DAYSECS;
        $this->database->requiredentries = 2;
        $this->database->requiredentriestoview = 2;
        $DB->update_record('data', $this->database);

        // Test user with full capabilities.
        $this->setUser($this->teacher);

        $result = mod_data_external::get_data_access_information($this->database->id);
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

        // Force approval.
        $DB->set_field('data', 'approval', 1, array('id' => $this->database->id));
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

            $generator->create_field($record, $this->database);
            $count++;
        }
        // Get all the fields created.
        $fields = $DB->get_records('data_fields', array('dataid' => $this->database->id), 'id');

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
        $entry11 = $generator->create_entry($this->database, $fieldcontents, $this->group1->id, ['Cats', 'Dogs']);
        $this->setUser($this->student2);
        $entry12 = $generator->create_entry($this->database, $fieldcontents, $this->group1->id, ['Cats']);
        $entry13 = $generator->create_entry($this->database, $fieldcontents, $this->group1->id);
        // Entry not in group.
        $entry14 = $generator->create_entry($this->database, $fieldcontents, 0);

        $this->setUser($this->student3);
        $entry21 = $generator->create_entry($this->database, $fieldcontents, $this->group2->id);

        // Approve all except $entry13.
        $DB->set_field('data_records', 'approved', 1, ['id' => $entry11]);
        $DB->set_field('data_records', 'approved', 1, ['id' => $entry12]);
        $DB->set_field('data_records', 'approved', 1, ['id' => $entry14]);
        $DB->set_field('data_records', 'approved', 1, ['id' => $entry21]);

        return [$entry11, $entry12, $entry13, $entry14, $entry21];
    }

    /**
     * Test get_entries
     */
    public function test_get_entries() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        // First of all, expect to see only my group entries (not other users in other groups ones).
        // We may expect entries without group also.
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->database->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(3, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);
        $this->assertEquals($entry11, $result['entries'][0]['id']);
        $this->assertCount(2, $result['entries'][0]['tags']);
        $this->assertEquals($this->student1->id, $result['entries'][0]['userid']);
        $this->assertEquals($this->group1->id, $result['entries'][0]['groupid']);
        $this->assertEquals($this->database->id, $result['entries'][0]['dataid']);
        $this->assertEquals($entry12, $result['entries'][1]['id']);
        $this->assertCount(1, $result['entries'][1]['tags']);
        $this->assertEquals('Cats', $result['entries'][1]['tags'][0]['rawname']);
        $this->assertEquals($this->student2->id, $result['entries'][1]['userid']);
        $this->assertEquals($this->group1->id, $result['entries'][1]['groupid']);
        $this->assertEquals($this->database->id, $result['entries'][1]['dataid']);
        $this->assertEquals($entry14, $result['entries'][2]['id']);
        $this->assertEquals($this->student2->id, $result['entries'][2]['userid']);
        $this->assertEquals(0, $result['entries'][2]['groupid']);
        $this->assertEquals($this->database->id, $result['entries'][2]['dataid']);
        // Other user in same group.
        $this->setUser($this->student2);
        $result = mod_data_external::get_entries($this->database->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(4, $result['entries']);  // I can see my entry not approved yet.
        $this->assertEquals(4, $result['totalcount']);

        // Now try with the user in the second group that must see only two entries (his group entry and the one without group).
        $this->setUser($this->student3);
        $result = mod_data_external::get_entries($this->database->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals($entry14, $result['entries'][0]['id']);
        $this->assertEquals($this->student2->id, $result['entries'][0]['userid']);
        $this->assertEquals(0, $result['entries'][0]['groupid']);
        $this->assertEquals($this->database->id, $result['entries'][0]['dataid']);
        $this->assertEquals($entry21, $result['entries'][1]['id']);
        $this->assertEquals($this->student3->id, $result['entries'][1]['userid']);
        $this->assertEquals($this->group2->id, $result['entries'][1]['groupid']);
        $this->assertEquals($this->database->id, $result['entries'][1]['dataid']);

        // Now, as teacher we should see all (we have permissions to view all groups).
        $this->setUser($this->teacher);
        $result = mod_data_external::get_entries($this->database->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(5, $result['entries']);  // I can see the not approved one.
        $this->assertEquals(5, $result['totalcount']);

        $entries = $DB->get_records('data_records', array('dataid' => $this->database->id), 'id');
        $this->assertCount(5, $entries);
        $count = 0;
        foreach ($entries as $entry) {
            $this->assertEquals($entry->id, $result['entries'][$count]['id']);
            $count++;
        }

        // Basic test passing the parameter (instead having to calculate it).
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->database->id, $this->group1->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(3, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);

        // Test ordering (reverse).
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->database->id, $this->group1->id, false, null, 'DESC');
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(3, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);
        $this->assertEquals($entry14, $result['entries'][0]['id']);

        // Test pagination.
        $this->setUser($this->student1);
        $result = mod_data_external::get_entries($this->database->id, $this->group1->id, false, null, null, 0, 1);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);
        $this->assertEquals($entry11, $result['entries'][0]['id']);

        $result = mod_data_external::get_entries($this->database->id, $this->group1->id, false, null, null, 1, 1);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);
        $this->assertEquals($entry12, $result['entries'][0]['id']);

        // Now test the return contents.
        data_generate_default_template($this->database, 'listtemplate', 0, false, true); // Generate a default list template.
        $result = mod_data_external::get_entries($this->database->id, $this->group1->id, true, null, null, 0, 2);
        $result = external_api::clean_returnvalue(mod_data_external::get_entries_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);
        $this->assertCount(9, $result['entries'][0]['contents']);
        $this->assertCount(9, $result['entries'][1]['contents']);
        // Search for some content.
        $this->assertTrue(strpos($result['listviewcontents'], 'opt1') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'January') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'menu1') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'text for testing') !== false);
        $this->assertTrue(strpos($result['listviewcontents'], 'sampleurl') !== false);
    }

    /**
     * Test get_entry_visible_groups.
     */
    public function test_get_entry_visible_groups() {
        global $DB;

        $DB->set_field('course', 'groupmode', VISIBLEGROUPS, ['id' => $this->course->id]);
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        // Check I can see my approved group entries.
        $this->setUser($this->student1);
        $result = mod_data_external::get_entry($entry11);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals($entry11, $result['entry']['id']);
        $this->assertTrue($result['entry']['approved']);
        $this->assertTrue($result['entry']['canmanageentry']); // Is mine, I can manage it.

        // Entry from other group.
        $result = mod_data_external::get_entry($entry21);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals($entry21, $result['entry']['id']);
    }

    /**
     * Test get_entry_separated_groups.
     */
    public function test_get_entry_separated_groups() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        // Check I can see my approved group entries.
        $this->setUser($this->student1);
        $result = mod_data_external::get_entry($entry11);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals($entry11, $result['entry']['id']);
        $this->assertTrue($result['entry']['approved']);
        $this->assertTrue($result['entry']['canmanageentry']); // Is mine, I can manage it.

        // Retrieve contents.
        data_generate_default_template($this->database, 'singletemplate', 0, false, true);
        $result = mod_data_external::get_entry($entry11, true);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(9, $result['entry']['contents']);
        $this->assertTrue(strpos($result['entryviewcontents'], 'opt1') !== false);
        $this->assertTrue(strpos($result['entryviewcontents'], 'January') !== false);
        $this->assertTrue(strpos($result['entryviewcontents'], 'menu1') !== false);
        $this->assertTrue(strpos($result['entryviewcontents'], 'text for testing') !== false);
        $this->assertTrue(strpos($result['entryviewcontents'], 'sampleurl') !== false);

        // This is in my group but I'm not the author.
        $result = mod_data_external::get_entry($entry12);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals($entry12, $result['entry']['id']);
        $this->assertTrue($result['entry']['approved']);
        $this->assertFalse($result['entry']['canmanageentry']); // Not mine.

        $this->setUser($this->student3);
        $result = mod_data_external::get_entry($entry21);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals($entry21, $result['entry']['id']);
        $this->assertTrue($result['entry']['approved']);
        $this->assertTrue($result['entry']['canmanageentry']); // Is mine, I can manage it.

        // As teacher I should be able to see all the entries.
        $this->setUser($this->teacher);
        $result = mod_data_external::get_entry($entry11);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertEquals($entry11, $result['entry']['id']);

        $result = mod_data_external::get_entry($entry12);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertEquals($entry12, $result['entry']['id']);
        // This is the not approved one.
        $result = mod_data_external::get_entry($entry13);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertEquals($entry13, $result['entry']['id']);

        $result = mod_data_external::get_entry($entry21);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertEquals($entry21, $result['entry']['id']);

        // Now, try to get an entry not approved yet.
        $this->setUser($this->student1);
        $this->expectException('moodle_exception');
        $result = mod_data_external::get_entry($entry13);
    }

    /**
     * Test get_entry from other group in separated groups.
     */
    public function test_get_entry_other_group_separated_groups() {
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        // We should not be able to view other gropu entries (in separated groups).
        $this->setUser($this->student1);
        $this->expectException('moodle_exception');
        $result = mod_data_external::get_entry($entry21);
    }

    /**
     * Test get_fields.
     */
    public function test_get_fields() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        $result = mod_data_external::get_fields($this->database->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_fields_returns(), $result);

        // Basically compare we retrieve all the fields and the correct values.
        $fields = $DB->get_records('data_fields', array('dataid' => $this->database->id), 'id');
        foreach ($result['fields'] as $field) {
            $this->assertEquals($field, (array) $fields[$field['id']]);
        }
    }

    /**
     * Test get_fields_database_without_fields.
     */
    public function test_get_fields_database_without_fields() {

        $this->setUser($this->student1);
        $result = mod_data_external::get_fields($this->database->id);
        $result = external_api::clean_returnvalue(mod_data_external::get_fields_returns(), $result);

        $this->assertEmpty($result['fields']);
    }

    /**
     * Test search_entries.
     */
    public function test_search_entries() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        // Empty search, it should return all the visible entries.
        $result = mod_data_external::search_entries($this->database->id, 0, false);
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(3, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);

        // Search for something that does not exists.
        $result = mod_data_external::search_entries($this->database->id, 0, false, 'abc');
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);
        $this->assertEquals(0, $result['totalcount']);

        // Search by text matching all the entries.
        $result = mod_data_external::search_entries($this->database->id, 0, false, 'text');
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(3, $result['entries']);
        $this->assertEquals(3, $result['totalcount']);
        $this->assertEquals(3, $result['maxcount']);

        // Now as the other student I should receive my not approved entry. Apply ordering here.
        $this->setUser($this->student2);
        $result = mod_data_external::search_entries($this->database->id, 0, false, 'text', [], DATA_APPROVED, 'ASC');
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(4, $result['entries']);
        $this->assertEquals(4, $result['totalcount']);
        $this->assertEquals(4, $result['maxcount']);
        // The not approved one should be the first.
        $this->assertEquals($entry13, $result['entries'][0]['id']);

        // Now as the other group student.
        $this->setUser($this->student3);
        $result = mod_data_external::search_entries($this->database->id, 0, false, 'text');
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals(2, $result['maxcount']);
        $this->assertEquals($this->student2->id, $result['entries'][0]['userid']);
        $this->assertEquals($this->student3->id, $result['entries'][1]['userid']);

        // Same normal text search as teacher.
        $this->setUser($this->teacher);
        $result = mod_data_external::search_entries($this->database->id, 0, false, 'text');
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(5, $result['entries']);  // I can see all groups and non approved.
        $this->assertEquals(5, $result['totalcount']);
        $this->assertEquals(5, $result['maxcount']);

        // Pagination.
        $this->setUser($this->teacher);
        $result = mod_data_external::search_entries($this->database->id, 0, false, 'text', [], DATA_TIMEADDED, 'ASC', 0, 2);
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(2, $result['entries']);  // Only 2 per page.
        $this->assertEquals(5, $result['totalcount']);
        $this->assertEquals(5, $result['maxcount']);

        // Now advanced search or not dinamic fields (user firstname for example).
        $this->setUser($this->student1);
        $advsearch = [
            ['name' => 'fn', 'value' => json_encode($this->student2->firstname)]
        ];
        $result = mod_data_external::search_entries($this->database->id, 0, false, '', $advsearch);
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(2, $result['entries']);
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals(3, $result['maxcount']);
        $this->assertEquals($this->student2->id, $result['entries'][0]['userid']);  // I only found mine!

        // Advanced search for fields.
        $field = $DB->get_record('data_fields', array('type' => 'url'));
        $advsearch = [
            ['name' => 'f_' . $field->id , 'value' => 'sampleurl']
        ];
        $result = mod_data_external::search_entries($this->database->id, 0, false, '', $advsearch);
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(3, $result['entries']);  // Found two entries matching this.
        $this->assertEquals(3, $result['totalcount']);
        $this->assertEquals(3, $result['maxcount']);

        // Combined search.
        $field2 = $DB->get_record('data_fields', array('type' => 'number'));
        $advsearch = [
            ['name' => 'f_' . $field->id , 'value' => 'sampleurl'],
            ['name' => 'f_' . $field2->id , 'value' => '12345'],
            ['name' => 'ln', 'value' => json_encode($this->student2->lastname)]
        ];
        $result = mod_data_external::search_entries($this->database->id, 0, false, '', $advsearch);
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(2, $result['entries']);  // Only one matching everything.
        $this->assertEquals(2, $result['totalcount']);
        $this->assertEquals(3, $result['maxcount']);

        // Combined search (no results).
        $field2 = $DB->get_record('data_fields', array('type' => 'number'));
        $advsearch = [
            ['name' => 'f_' . $field->id , 'value' => 'sampleurl'],
            ['name' => 'f_' . $field2->id , 'value' => '98780333'], // Non existent number.
        ];
        $result = mod_data_external::search_entries($this->database->id, 0, false, '', $advsearch);
        $result = external_api::clean_returnvalue(mod_data_external::search_entries_returns(), $result);
        $this->assertCount(0, $result['entries']);  // Only one matching everything.
        $this->assertEquals(0, $result['totalcount']);
        $this->assertEquals(3, $result['maxcount']);
    }

    /**
     * Test approve_entry.
     */
    public function test_approve_entry() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->teacher);
        $this->assertEquals(0, $DB->get_field('data_records', 'approved', array('id' => $entry13)));
        $result = mod_data_external::approve_entry($entry13);
        $result = external_api::clean_returnvalue(mod_data_external::approve_entry_returns(), $result);
        $this->assertEquals(1, $DB->get_field('data_records', 'approved', array('id' => $entry13)));
    }

    /**
     * Test unapprove_entry.
     */
    public function test_unapprove_entry() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->teacher);
        $this->assertEquals(1, $DB->get_field('data_records', 'approved', array('id' => $entry11)));
        $result = mod_data_external::approve_entry($entry11, false);
        $result = external_api::clean_returnvalue(mod_data_external::approve_entry_returns(), $result);
        $this->assertEquals(0, $DB->get_field('data_records', 'approved', array('id' => $entry11)));
    }

    /**
     * Test approve_entry missing permissions.
     */
    public function test_approve_entry_missing_permissions() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        $this->expectException('moodle_exception');
        mod_data_external::approve_entry($entry13);
    }

    /**
     * Test delete_entry as teacher. Check I can delete any entry.
     */
    public function test_delete_entry_as_teacher() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->teacher);
        $result = mod_data_external::delete_entry($entry11);
        $result = external_api::clean_returnvalue(mod_data_external::delete_entry_returns(), $result);
        $this->assertEquals(0, $DB->count_records('data_records', array('id' => $entry11)));

        // Entry in other group.
        $result = mod_data_external::delete_entry($entry21);
        $result = external_api::clean_returnvalue(mod_data_external::delete_entry_returns(), $result);
        $this->assertEquals(0, $DB->count_records('data_records', array('id' => $entry21)));
    }

    /**
     * Test delete_entry as student. Check I can delete my own entries.
     */
    public function test_delete_entry_as_student() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        $result = mod_data_external::delete_entry($entry11);
        $result = external_api::clean_returnvalue(mod_data_external::delete_entry_returns(), $result);
        $this->assertEquals(0, $DB->count_records('data_records', array('id' => $entry11)));
    }

    /**
     * Test delete_entry as student in read only mode period. Check I cannot delete my own entries in that period.
     */
    public function test_delete_entry_as_student_in_read_only_period() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();
        // Set a time period.
        $this->database->timeviewfrom = time() - HOURSECS;
        $this->database->timeviewto = time() + HOURSECS;
        $DB->update_record('data', $this->database);

        $this->setUser($this->student1);
        $this->expectException('moodle_exception');
        mod_data_external::delete_entry($entry11);
    }

    /**
     * Test delete_entry with an user missing permissions.
     */
    public function test_delete_entry_missing_permissions() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        $this->expectException('moodle_exception');
        mod_data_external::delete_entry($entry21);
    }

    /**
     * Test add_entry.
     */
    public function test_add_entry() {
        global $DB;
        // First create the record structure and add some entries.
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        $newentrydata = [];
        $fields = $DB->get_records('data_fields', array('dataid' => $this->database->id), 'id');
        // Prepare the new entry data.
        foreach ($fields as $field) {
            $subfield = $value = '';

            switch ($field->type) {
                case 'checkbox':
                    $value = ['opt1', 'opt2'];
                    break;
                case 'date':
                    // Add two extra.
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'day',
                        'value' => json_encode('5')
                    ];
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'month',
                        'value' => json_encode('1')
                    ];
                    $subfield = 'year';
                    $value = '1981';
                    break;
                case 'menu':
                    $value = 'menu1';
                    break;
                case 'multimenu':
                    $value = ['multimenu1', 'multimenu4'];
                    break;
                case 'number':
                    $value = 6;
                    break;
                case 'radiobutton':
                    $value = 'radioopt1';
                    break;
                case 'text':
                    $value = 'some text';
                    break;
                case 'textarea':
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'content1',
                        'value' => json_encode(FORMAT_MOODLE)
                    ];
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'itemid',
                        'value' => json_encode(0)
                    ];
                    $value = 'more text';
                    break;
                case 'url':
                    $value = 'https://moodle.org';
                    $subfield = 0;
                    break;
            }

            $newentrydata[] = [
                'fieldid' => $field->id,
                'subfield' => $subfield,
                'value' => json_encode($value)
            ];
        }
        $result = mod_data_external::add_entry($this->database->id, 0, $newentrydata);
        $result = external_api::clean_returnvalue(mod_data_external::add_entry_returns(), $result);

        $newentryid = $result['newentryid'];
        $result = mod_data_external::get_entry($newentryid, true);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertEquals($this->student1->id, $result['entry']['userid']);
        $this->assertCount(9, $result['entry']['contents']);
        foreach ($result['entry']['contents'] as $content) {
            $field = $fields[$content['fieldid']];
            // Stored content same that the one retrieved by WS.
            $dbcontent = $DB->get_record('data_content', array('fieldid' => $field->id, 'recordid' => $newentryid));
            $this->assertEquals($dbcontent->content, $content['content']);

            // Now double check everything stored is correct.
            if ($field->type == 'checkbox') {
                $this->assertEquals('opt1##opt2', $content['content']);
                continue;
            }
            if ($field->type == 'date') {
                $this->assertEquals(347500800, $content['content']); // Date in gregorian format.
                continue;
            }
            if ($field->type == 'menu') {
                $this->assertEquals('menu1', $content['content']);
                continue;
            }
            if ($field->type == 'multimenu') {
                $this->assertEquals('multimenu1##multimenu4', $content['content']);
                continue;
            }
            if ($field->type == 'number') {
                $this->assertEquals(6, $content['content']);
                continue;
            }
            if ($field->type == 'radiobutton') {
                $this->assertEquals('radioopt1', $content['content']);
                continue;
            }
            if ($field->type == 'text') {
                $this->assertEquals('some text', $content['content']);
                continue;
            }
            if ($field->type == 'textarea') {
                $this->assertEquals('more text', $content['content']);
                $this->assertEquals(FORMAT_MOODLE, $content['content1']);
                continue;
            }
            if ($field->type == 'url') {
                $this->assertEquals('https://moodle.org', $content['content']);
                continue;
            }
            $this->assertEquals('multimenu1##multimenu4', $content['content']);
        }

        // Now, try to add another entry but removing some required data.
        unset($newentrydata[0]);
        $result = mod_data_external::add_entry($this->database->id, 0, $newentrydata);
        $result = external_api::clean_returnvalue(mod_data_external::add_entry_returns(), $result);
        $this->assertEquals(0, $result['newentryid']);
        $this->assertCount(0, $result['generalnotifications']);
        $this->assertCount(1, $result['fieldnotifications']);
        $this->assertEquals('field-1', $result['fieldnotifications'][0]['fieldname']);
        $this->assertEquals(get_string('errormustsupplyvalue', 'data'), $result['fieldnotifications'][0]['notification']);
    }

    /**
     * Test add_entry empty_form.
     */
    public function test_add_entry_empty_form() {
        $result = mod_data_external::add_entry($this->database->id, 0, []);
        $result = external_api::clean_returnvalue(mod_data_external::add_entry_returns(), $result);
        $this->assertEquals(0, $result['newentryid']);
        $this->assertCount(1, $result['generalnotifications']);
        $this->assertCount(0, $result['fieldnotifications']);
        $this->assertEquals(get_string('emptyaddform', 'data'), $result['generalnotifications'][0]);
    }

    /**
     * Test add_entry read_only_period.
     */
    public function test_add_entry_read_only_period() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();
        // Set a time period.
        $this->database->timeviewfrom = time() - HOURSECS;
        $this->database->timeviewto = time() + HOURSECS;
        $DB->update_record('data', $this->database);

        $this->setUser($this->student1);
        $this->expectExceptionMessage(get_string('noaccess', 'data'));
        $this->expectException('moodle_exception');
        mod_data_external::add_entry($this->database->id, 0, []);
    }

    /**
     * Test add_entry max_num_entries.
     */
    public function test_add_entry_max_num_entries() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();
        // Set a time period.
        $this->database->maxentries = 1;
        $DB->update_record('data', $this->database);

        $this->setUser($this->student1);
        $this->expectExceptionMessage(get_string('noaccess', 'data'));
        $this->expectException('moodle_exception');
        mod_data_external::add_entry($this->database->id, 0, []);
    }

    /**
     * Test update_entry.
     */
    public function test_update_entry() {
        global $DB;
        // First create the record structure and add some entries.
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        $newentrydata = [];
        $fields = $DB->get_records('data_fields', array('dataid' => $this->database->id), 'id');
        // Prepare the new entry data.
        foreach ($fields as $field) {
            $subfield = $value = '';

            switch ($field->type) {
                case 'checkbox':
                    $value = ['opt1', 'opt2'];
                    break;
                case 'date':
                    // Add two extra.
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'day',
                        'value' => json_encode('5')
                    ];
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'month',
                        'value' => json_encode('1')
                    ];
                    $subfield = 'year';
                    $value = '1981';
                    break;
                case 'menu':
                    $value = 'menu1';
                    break;
                case 'multimenu':
                    $value = ['multimenu1', 'multimenu4'];
                    break;
                case 'number':
                    $value = 6;
                    break;
                case 'radiobutton':
                    $value = 'radioopt2';
                    break;
                case 'text':
                    $value = 'some text';
                    break;
                case 'textarea':
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'content1',
                        'value' => json_encode(FORMAT_MOODLE)
                    ];
                    $newentrydata[] = [
                        'fieldid' => $field->id,
                        'subfield' => 'itemid',
                        'value' => json_encode(0)
                    ];
                    $value = 'more text';
                    break;
                case 'url':
                    $value = 'https://moodle.org';
                    $subfield = 0;
                    break;
            }

            $newentrydata[] = [
                'fieldid' => $field->id,
                'subfield' => $subfield,
                'value' => json_encode($value)
            ];
        }
        $result = mod_data_external::update_entry($entry11, $newentrydata);
        $result = external_api::clean_returnvalue(mod_data_external::update_entry_returns(), $result);
        $this->assertTrue($result['updated']);
        $this->assertCount(0, $result['generalnotifications']);
        $this->assertCount(0, $result['fieldnotifications']);

        $result = mod_data_external::get_entry($entry11, true);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertEquals($this->student1->id, $result['entry']['userid']);
        $this->assertCount(9, $result['entry']['contents']);
        foreach ($result['entry']['contents'] as $content) {
            $field = $fields[$content['fieldid']];
            // Stored content same that the one retrieved by WS.
            $dbcontent = $DB->get_record('data_content', array('fieldid' => $field->id, 'recordid' => $entry11));
            $this->assertEquals($dbcontent->content, $content['content']);

            // Now double check everything stored is correct.
            if ($field->type == 'checkbox') {
                $this->assertEquals('opt1##opt2', $content['content']);
                continue;
            }
            if ($field->type == 'date') {
                $this->assertEquals(347500800, $content['content']); // Date in gregorian format.
                continue;
            }
            if ($field->type == 'menu') {
                $this->assertEquals('menu1', $content['content']);
                continue;
            }
            if ($field->type == 'multimenu') {
                $this->assertEquals('multimenu1##multimenu4', $content['content']);
                continue;
            }
            if ($field->type == 'number') {
                $this->assertEquals(6, $content['content']);
                continue;
            }
            if ($field->type == 'radiobutton') {
                $this->assertEquals('radioopt2', $content['content']);
                continue;
            }
            if ($field->type == 'text') {
                $this->assertEquals('some text', $content['content']);
                continue;
            }
            if ($field->type == 'textarea') {
                $this->assertEquals('more text', $content['content']);
                $this->assertEquals(FORMAT_MOODLE, $content['content1']);
                continue;
            }
            if ($field->type == 'url') {
                $this->assertEquals('https://moodle.org', $content['content']);
                continue;
            }
            $this->assertEquals('multimenu1##multimenu4', $content['content']);
        }

        // Now, try to update the entry but removing some required data.
        unset($newentrydata[0]);
        $result = mod_data_external::update_entry($entry11, $newentrydata);
        $result = external_api::clean_returnvalue(mod_data_external::update_entry_returns(), $result);
        $this->assertFalse($result['updated']);
        $this->assertCount(0, $result['generalnotifications']);
        $this->assertCount(1, $result['fieldnotifications']);
        $this->assertEquals('field-1', $result['fieldnotifications'][0]['fieldname']);
        $this->assertEquals(get_string('errormustsupplyvalue', 'data'), $result['fieldnotifications'][0]['notification']);
    }

    /**
     * Test update_entry sending empty data.
     */
    public function test_update_entry_empty_data() {
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $this->setUser($this->student1);
        $result = mod_data_external::update_entry($entry11, []);
        $result = external_api::clean_returnvalue(mod_data_external::update_entry_returns(), $result);
        $this->assertFalse($result['updated']);
        $this->assertCount(1, $result['generalnotifications']);
        $this->assertCount(9, $result['fieldnotifications']);
        $this->assertEquals(get_string('emptyaddform', 'data'), $result['generalnotifications'][0]);
    }

    /**
     * Test update_entry in read only period.
     */
    public function test_update_entry_read_only_period() {
        global $DB;
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();
        // Set a time period.
        $this->database->timeviewfrom = time() - HOURSECS;
        $this->database->timeviewto = time() + HOURSECS;
        $DB->update_record('data', $this->database);

        $this->setUser($this->student1);
        $this->expectExceptionMessage(get_string('noaccess', 'data'));
        $this->expectException('moodle_exception');
        mod_data_external::update_entry($entry11, []);
    }

    /**
     * Test update_entry other_user.
     */
    public function test_update_entry_other_user() {
        // Try to update other user entry.
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();
        $this->setUser($this->student2);
        $this->expectExceptionMessage(get_string('noaccess', 'data'));
        $this->expectException('moodle_exception');
        mod_data_external::update_entry($entry11, []);
    }

    /**
     * Test get_entry_rating_information.
     */
    public function test_get_entry_rating_information() {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/rating/lib.php');

        $DB->set_field('data', 'assessed', RATING_AGGREGATE_SUM, array('id' => $this->database->id));
        $DB->set_field('data', 'scale', 100, array('id' => $this->database->id));
        list($entry11, $entry12, $entry13, $entry14, $entry21) = self::populate_database_with_entries();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $this->course->id, $this->studentrole->id, 'manual');

        // Rate the entry as user1.
        $rating1 = new stdClass();
        $rating1->contextid = $this->context->id;
        $rating1->component = 'mod_data';
        $rating1->ratingarea = 'entry';
        $rating1->itemid = $entry11;
        $rating1->rating = 50;
        $rating1->scaleid = 100;
        $rating1->userid = $user1->id;
        $rating1->timecreated = time();
        $rating1->timemodified = time();
        $rating1->id = $DB->insert_record('rating', $rating1);

        // Rate the entry as user2.
        $rating2 = new stdClass();
        $rating2->contextid = $this->context->id;
        $rating2->component = 'mod_data';
        $rating2->ratingarea = 'entry';
        $rating2->itemid = $entry11;
        $rating2->rating = 100;
        $rating2->scaleid = 100;
        $rating2->userid = $user2->id;
        $rating2->timecreated = time() + 1;
        $rating2->timemodified = time() + 1;
        $rating2->id = $DB->insert_record('rating', $rating2);

        // As student, retrieve ratings information.
        $this->setUser($this->student2);
        $result = mod_data_external::get_entry($entry11);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(1, $result['ratinginfo']['ratings']);
        $this->assertFalse($result['ratinginfo']['ratings'][0]['canviewaggregate']);
        $this->assertFalse($result['ratinginfo']['canviewall']);
        $this->assertFalse($result['ratinginfo']['ratings'][0]['canrate']);
        $this->assertTrue(!isset($result['ratinginfo']['ratings'][0]['count']));

        // Now, as teacher, I should see the info correctly.
        $this->setUser($this->teacher);
        $result = mod_data_external::get_entry($entry11);
        $result = external_api::clean_returnvalue(mod_data_external::get_entry_returns(), $result);
        $this->assertCount(1, $result['ratinginfo']['ratings']);
        $this->assertTrue($result['ratinginfo']['ratings'][0]['canviewaggregate']);
        $this->assertTrue($result['ratinginfo']['canviewall']);
        $this->assertTrue($result['ratinginfo']['ratings'][0]['canrate']);
        $this->assertEquals(2, $result['ratinginfo']['ratings'][0]['count']);
        $this->assertEquals(100, $result['ratinginfo']['ratings'][0]['aggregate']); // Expect maximium scale value.
    }
}
