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
 * Unit tests for lib.php
 *
 * @package    mod_data
 * @category   phpunit
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_data;

use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/data/lib.php');

/**
 * Unit tests for lib.php
 *
 * @package    mod_data
 * @copyright  2013 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /**
     * @var moodle_database
     */
    protected $DB = null;

    /**
     * Tear Down to reset DB.
     */
    public function tearDown(): void {
        global $DB;

        if (isset($this->DB)) {
            $DB = $this->DB;
            $this->DB = null;
        }
        parent::tearDown();
    }

    /**
     * Confirms that completionentries is working
     * Sets it to 1, confirms that
     * it is not complete. Inserts a record and
     * confirms that it is complete.
     */
    public function test_data_completion(): void {
        global $DB, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enablecompletion = 1;
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $record = new \stdClass();
        $record->course = $course->id;
        $record->name = "Mod data completion test";
        $record->intro = "Some intro of some sort";
        $record->completionentries = "1";
        /* completion=2 means Show activity commplete when condition is met and completionentries means 1 record is
         * required for the activity to be considered complete
         */
        $module = $this->getDataGenerator()->create_module('data', $record, array('completion' => 2, 'completionentries' => 1));

        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm, true, 0);
        /* Confirm it is not complete as there are no entries */
        $this->assertNotEquals(1, $completiondata->completionstate);

        $field = data_get_field_new('text', $module);
        $fielddetail = new \stdClass();
        $fielddetail->d = $module->id;
        $fielddetail->mode = 'add';
        $fielddetail->type = 'text';
        $fielddetail->sesskey = sesskey();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';
        $contentid = $DB->insert_record('data_content', $datacontent);

        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm);
        /* Confirm it is complete because it has 1 entry */
        $this->assertEquals(1, $completiondata->completionstate);
    }

    public function test_data_delete_record(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a record for deleting.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $record = new \stdClass();
        $record->course = $course->id;
        $record->name = "Mod data delete test";
        $record->intro = "Some intro of some sort";

        $module = $this->getDataGenerator()->create_module('data', $record);

        $field = data_get_field_new('text', $module);

        $fielddetail = new \stdClass();
        $fielddetail->d = $module->id;
        $fielddetail->mode = 'add';
        $fielddetail->type = 'text';
        $fielddetail->sesskey = sesskey();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';

        $contentid = $DB->insert_record('data_content', $datacontent);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);

        // Check to make sure that we have a database record.
        $data = $DB->get_records('data', array('id' => $module->id));
        $this->assertEquals(1, count($data));

        $datacontent = $DB->get_records('data_content', array('id' => $contentid));
        $this->assertEquals(1, count($datacontent));

        $datafields = $DB->get_records('data_fields', array('id' => $field->field->id));
        $this->assertEquals(1, count($datafields));

        $datarecords = $DB->get_records('data_records', array('id' => $recordid));
        $this->assertEquals(1, count($datarecords));

        // Test to see if a failed delete returns false.
        $result = data_delete_record(8798, $module, $course->id, $cm->id);
        $this->assertFalse($result);

        // Delete the record.
        $result = data_delete_record($recordid, $module, $course->id, $cm->id);

        // Check that all of the record is gone.
        $datacontent = $DB->get_records('data_content', array('id' => $contentid));
        $this->assertEquals(0, count($datacontent));

        $datarecords = $DB->get_records('data_records', array('id' => $recordid));
        $this->assertEquals(0, count($datarecords));

        // Make sure the function returns true on a successful deletion.
        $this->assertTrue($result);
    }

    /**
     * Test comment_created event.
     */
    public function test_data_comment_created_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        // Create a record for deleting.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $record = new \stdClass();
        $record->course = $course->id;
        $record->name = "Mod data delete test";
        $record->intro = "Some intro of some sort";
        $record->comments = 1;

        $module = $this->getDataGenerator()->create_module('data', $record);
        $field = data_get_field_new('text', $module);

        $fielddetail = new \stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';

        $contentid = $DB->insert_record('data_content', $datacontent);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);

        $context = \context_module::instance($module->cmid);
        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->course = $course;
        $cmt->cm = $cm;
        $cmt->area = 'database_entry';
        $cmt->itemid = $recordid;
        $cmt->showcount = true;
        $cmt->component = 'mod_data';
        $comment = new \comment($cmt);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->add('New comment');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_data\event\comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new \moodle_url('/mod/data/view.php', array('id' => $cm->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_deleted event.
     */
    public function test_data_comment_deleted_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        // Create a record for deleting.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $record = new \stdClass();
        $record->course = $course->id;
        $record->name = "Mod data delete test";
        $record->intro = "Some intro of some sort";
        $record->comments = 1;

        $module = $this->getDataGenerator()->create_module('data', $record);
        $field = data_get_field_new('text', $module);

        $fielddetail = new \stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';

        $contentid = $DB->insert_record('data_content', $datacontent);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);

        $context = \context_module::instance($module->cmid);
        $cmt = new \stdClass();
        $cmt->context = $context;
        $cmt->course = $course;
        $cmt->cm = $cm;
        $cmt->area = 'database_entry';
        $cmt->itemid = $recordid;
        $cmt->showcount = true;
        $cmt->component = 'mod_data';
        $comment = new \comment($cmt);
        $newcomment = $comment->add('New comment 1');

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->delete($newcomment->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_data\event\comment_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new \moodle_url('/mod/data/view.php', array('id' => $module->cmid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Checks that data_user_can_manage_entry will return true if the user
     * has the mod/data:manageentries capability.
     */
    public function test_data_user_can_manage_entry_return_true_with_capability(): void {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new \stdClass();

        $this->setUser($user);

        assign_capability('mod/data:manageentries', CAP_ALLOW, $roleid, $context);

        $this->assertTrue(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns true if the user has mod/data:manageentries capability');
    }

    /**
     * Checks that data_user_can_manage_entry will return false if the data
     * is set to readonly.
     */
    public function test_data_user_can_manage_entry_return_false_readonly(): void {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        // Causes readonly mode to be enabled.
        $data = new \stdClass();
        $now = time();
        // Add a small margin around the periods to prevent errors with slow tests.
        $data->timeviewfrom = $now - 1;
        $data->timeviewto = $now + 5;

        $this->assertFalse(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns false if the data is read only');
    }

    /**
     * Checks that data_user_can_manage_entry will return false if the record
     * can't be found in the database.
     */
    public function test_data_user_can_manage_entry_return_false_no_record(): void {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new \stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        // Pass record id instead of object to force DB lookup.
        $this->assertFalse(data_user_can_manage_entry(1, $data, $context),
            'data_user_can_manage_entry() returns false if the record cannot be found');
    }

    /**
     * Checks that data_user_can_manage_entry will return false if the record
     * isn't owned by the user.
     */
    public function test_data_user_can_manage_entry_return_false_not_owned_record(): void {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new \stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // Make sure the record isn't owned by this user.
        $record->userid = $user->id + 1;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $this->assertFalse(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns false if the record isnt owned by the user');
    }

    /**
     * Checks that data_user_can_manage_entry will return true if the data
     * doesn't require approval.
     */
    public function test_data_user_can_manage_entry_return_true_data_no_approval(): void {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new \stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // The record doesn't need approval.
        $data->approval = false;
        // Make sure the record is owned by this user.
        $record->userid = $user->id;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $this->assertTrue(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns true if the record doesnt require approval');
    }

    /**
     * Checks that data_user_can_manage_entry will return true if the record
     * isn't yet approved.
     */
    public function test_data_user_can_manage_entry_return_true_record_unapproved(): void {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new \stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // The record needs approval.
        $data->approval = true;
        // Make sure the record is owned by this user.
        $record->userid = $user->id;
        // The record hasn't yet been approved.
        $record->approved = false;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $this->assertTrue(data_user_can_manage_entry($record, $data, $context),
            'data_user_can_manage_entry() returns true if the record is not yet approved');
    }

    /**
     * Checks that data_user_can_manage_entry will return the 'manageapproved'
     * value if the record has already been approved.
     */
    public function test_data_user_can_manage_entry_return_manageapproved(): void {

        $this->resetAfterTest();
        $testdata = $this->create_user_test_data();

        $user = $testdata['user'];
        $course = $testdata['course'];
        $roleid = $testdata['roleid'];
        $context = $testdata['context'];
        $record = $testdata['record'];
        $data = new \stdClass();
        // Causes readonly mode to be disabled.
        $now = time();
        $data->timeviewfrom = $now + 100;
        $data->timeviewto = $now - 100;
        // The record needs approval.
        $data->approval = true;
        // Can the user managed approved records?
        $data->manageapproved = false;
        // Make sure the record is owned by this user.
        $record->userid = $user->id;
        // The record has been approved.
        $record->approved = true;

        $this->setUser($user);

        // Need to make sure they don't have this capability in order to fall back to
        // the other checks.
        assign_capability('mod/data:manageentries', CAP_PROHIBIT, $roleid, $context);

        $canmanageentry = data_user_can_manage_entry($record, $data, $context);

        // Make sure the result of the check is what ever the manageapproved setting
        // is set to.
        $this->assertEquals($data->manageapproved, $canmanageentry,
            'data_user_can_manage_entry() returns the manageapproved setting on approved records');
    }

    /**
     * Helper method to create a set of test data for data_user_can_manage tests
     *
     * @return array contains user, course, roleid, module, context and record
     */
    private function create_user_test_data() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $roleid = $this->getDataGenerator()->create_role();
        $record = new \stdClass();
        $record->name = "test name";
        $record->intro = "test intro";
        $record->comments = 1;
        $record->course = $course->id;
        $record->userid = $user->id;

        $module = $this->getDataGenerator()->create_module('data', $record);
        $cm = get_coursemodule_from_instance('data', $module->id, $course->id);
        $context = \context_module::instance($module->cmid);

        $this->getDataGenerator()->role_assign($roleid, $user->id, $context->id);

        return array(
            'user' => $user,
            'course' => $course,
            'roleid' => $roleid,
            'module' => $module,
            'context' => $context,
            'record' => $record
        );
    }

    /**
     * Tests for mod_data_rating_can_see_item_ratings().
     *
     * @throws coding_exception
     * @throws rating_exception
     */
    public function test_mod_data_rating_can_see_item_ratings(): void {
        global $DB;

        $this->resetAfterTest();

        // Setup test data.
        $course = new \stdClass();
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        $course = $this->getDataGenerator()->create_course($course);
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id));
        $cm = get_coursemodule_from_instance('data', $data->id);
        $context = \context_module::instance($cm->id);

        // Create users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Groups and stuff.
        $role = $DB->get_record('role', array('shortname' => 'teacher'), '*', MUST_EXIST);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $role->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $role->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $role->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, $role->id);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group1, $user1);
        groups_add_member($group1, $user2);
        groups_add_member($group2, $user3);
        groups_add_member($group2, $user4);

        // Add data.
        $field = data_get_field_new('text', $data);

        $fielddetail = new \stdClass();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';

        $field->define_field($fielddetail);
        $field->insert_field();

        // Add a record with a group id of zero (all participants).
        $recordid1 = data_add_record($data, 0);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid1;
        $datacontent['content'] = 'Obelix';
        $DB->insert_record('data_content', $datacontent);

        $recordid = data_add_record($data, $group1->id);

        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Asterix';
        $DB->insert_record('data_content', $datacontent);

        // Now try to access it as various users.
        unassign_capability('moodle/site:accessallgroups', $role->id);
        // Eveyone should have access to the record with the group id of zero.
        $params1 = array('contextid' => 2,
                        'component' => 'mod_data',
                        'ratingarea' => 'entry',
                        'itemid' => $recordid1,
                        'scaleid' => 2);

        $params = array('contextid' => 2,
                        'component' => 'mod_data',
                        'ratingarea' => 'entry',
                        'itemid' => $recordid,
                        'scaleid' => 2);

        $this->setUser($user1);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user2);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user3);
        $this->assertFalse(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user4);
        $this->assertFalse(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));

        // Now try with accessallgroups cap and make sure everything is visible.
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $role->id, $context->id);
        $this->setUser($user1);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user2);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user3);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user4);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));

        // Change group mode and verify visibility.
        $course->groupmode = VISIBLEGROUPS;
        $DB->update_record('course', $course);
        unassign_capability('moodle/site:accessallgroups', $role->id);
        $this->setUser($user1);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user2);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user3);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));
        $this->setUser($user4);
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params));
        $this->assertTrue(mod_data_rating_can_see_item_ratings($params1));

    }

    /**
     * Tests for mod_data_refresh_events.
     */
    public function test_data_refresh_events(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $timeopen = time();
        $timeclose = time() + 86400;

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $params['course'] = $course->id;
        $params['timeavailablefrom'] = $timeopen;
        $params['timeavailableto'] = $timeclose;
        $data = $generator->create_instance($params);

        // Normal case, with existing course.
        $this->assertTrue(data_refresh_events($course->id));
        $eventparams = array('modulename' => 'data', 'instance' => $data->id, 'eventtype' => 'open');
        $openevent = $DB->get_record('event', $eventparams, '*', MUST_EXIST);
        $this->assertEquals($openevent->timestart, $timeopen);

        $eventparams = array('modulename' => 'data', 'instance' => $data->id, 'eventtype' => 'close');
        $closeevent = $DB->get_record('event', $eventparams, '*', MUST_EXIST);
        $this->assertEquals($closeevent->timestart, $timeclose);
        // In case the course ID is passed as a numeric string.
        $this->assertTrue(data_refresh_events('' . $course->id));
        // Course ID not provided.
        $this->assertTrue(data_refresh_events());
        $eventparams = array('modulename' => 'data');
        $events = $DB->get_records('event', $eventparams);
        foreach ($events as $event) {
            if ($event->modulename === 'data' && $event->instance === $data->id && $event->eventtype === 'open') {
                $this->assertEquals($event->timestart, $timeopen);
            }
            if ($event->modulename === 'data' && $event->instance === $data->id && $event->eventtype === 'close') {
                $this->assertEquals($event->timestart, $timeclose);
            }
        }
    }

    /**
     * Data provider for tests of data_get_config.
     *
     * @return array
     */
    public static function data_get_config_provider(): array {
        $initialdata = (object) [
            'template_foo' => true,
            'template_bar' => false,
            'template_baz' => null,
        ];

        $database = (object) [
            'config' => json_encode($initialdata),
        ];

        return [
            'Return full dataset (no key/default)' => [
                [$database],
                $initialdata,
            ],
            'Return full dataset (no default)' => [
                [$database, null],
                $initialdata,
            ],
            'Return full dataset' => [
                [$database, null, null],
                $initialdata,
            ],
            'Return requested key only, value true, no default' => [
                [$database, 'template_foo'],
                true,
            ],
            'Return requested key only, value false, no default' => [
                [$database, 'template_bar'],
                false,
            ],
            'Return requested key only, value null, no default' => [
                [$database, 'template_baz'],
                null,
            ],
            'Return unknown key, value null, no default' => [
                [$database, 'template_bum'],
                null,
            ],
            'Return requested key only, value true, default null' => [
                [$database, 'template_foo', null],
                true,
            ],
            'Return requested key only, value false, default null' => [
                [$database, 'template_bar', null],
                false,
            ],
            'Return requested key only, value null, default null' => [
                [$database, 'template_baz', null],
                null,
            ],
            'Return unknown key, value null, default null' => [
                [$database, 'template_bum', null],
                null,
            ],
            'Return requested key only, value true, default 42' => [
                [$database, 'template_foo', 42],
                true,
            ],
            'Return requested key only, value false, default 42' => [
                [$database, 'template_bar', 42],
                false,
            ],
            'Return requested key only, value null, default 42' => [
                [$database, 'template_baz', 42],
                null,
            ],
            'Return unknown key, value null, default 42' => [
                [$database, 'template_bum', 42],
                42,
            ],
        ];
    }

    /**
     * Tests for data_get_config.
     *
     * @dataProvider    data_get_config_provider
     * @param   array   $funcargs       The args to pass to data_get_config
     * @param   mixed   $expectation    The expected value
     */
    public function test_data_get_config($funcargs, $expectation): void {
        $this->assertEquals($expectation, call_user_func_array('data_get_config', $funcargs));
    }

    /**
     * Data provider for tests of data_set_config.
     *
     * @return array
     */
    public static function data_set_config_provider(): array {
        $basevalue = (object) ['id' => rand(1, 1000)];
        $config = [
            'template_foo'  => true,
            'template_bar'  => false,
        ];

        $withvalues = clone $basevalue;
        $withvalues->config = json_encode((object) $config);

        return [
            'Empty config, New value' => [
                $basevalue,
                'etc',
                'newvalue',
                true,
                json_encode((object) ['etc' => 'newvalue'])
            ],
            'Has config, New value' => [
                clone $withvalues,
                'etc',
                'newvalue',
                true,
                json_encode((object) array_merge($config, ['etc' => 'newvalue']))
            ],
            'Has config, Update value, string' => [
                clone $withvalues,
                'template_foo',
                'newvalue',
                true,
                json_encode((object) array_merge($config, ['template_foo' => 'newvalue']))
            ],
            'Has config, Update value, true' => [
                clone $withvalues,
                'template_bar',
                true,
                true,
                json_encode((object) array_merge($config, ['template_bar' => true]))
            ],
            'Has config, Update value, false' => [
                clone $withvalues,
                'template_foo',
                false,
                true,
                json_encode((object) array_merge($config, ['template_foo' => false]))
            ],
            'Has config, Update value, null' => [
                clone $withvalues,
                'template_foo',
                null,
                true,
                json_encode((object) array_merge($config, ['template_foo' => null]))
            ],
            'Has config, No update, value true' => [
                clone $withvalues,
                'template_foo',
                true,
                false,
                $withvalues->config,
            ],
        ];
    }

    /**
     * Tests for data_set_config.
     *
     * @dataProvider    data_set_config_provider
     * @param   object  $database       The example row for the entry
     * @param   string  $key            The config key to set
     * @param   mixed   $value          The value of the key
     * @param   bool    $expectupdate   Whether we expected an update
     * @param   mixed   $newconfigvalue The expected value
     */
    public function test_data_set_config($database, $key, $value, $expectupdate, $newconfigvalue): void {
        global $DB;

        // Mock the database.
        // Note: Use the actual test class here rather than the abstract because are testing concrete methods.
        $this->DB = $DB;
        $DB = $this->getMockBuilder(get_class($DB))
            ->onlyMethods(['set_field'])
            ->getMock();

        $DB->expects($this->exactly((int) $expectupdate))
            ->method('set_field')
            ->with(
                'data',
                'config',
                $newconfigvalue,
                ['id' => $database->id]
            );

        // Perform the update.
        data_set_config($database, $key, $value);

        // Ensure that the value was updated by reference in $database.
        $config = json_decode($database->config);
        $this->assertEquals($value, $config->$key);
    }

    public function test_mod_data_get_tagged_records(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course1 = $this->getDataGenerator()->create_course();

        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';

        $data1 = $this->getDataGenerator()->create_module('data', array('course' => $course1->id, 'approval' => true));
        $field1 = $datagenerator->create_field($fieldrecord, $data1);

        $datagenerator->create_entry($data1, [$field1->field->id => 'value11'], 0, ['Cats', 'Dogs']);
        $datagenerator->create_entry($data1, [$field1->field->id => 'value12'], 0, ['Cats', 'mice']);
        $datagenerator->create_entry($data1, [$field1->field->id => 'value13'], 0, ['Cats']);
        $datagenerator->create_entry($data1, [$field1->field->id => 'value14'], 0);

        $tag = \core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);
        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value12', $res->content);
        $this->assertStringContainsString('value13', $res->content);
        $this->assertStringNotContainsString('value14', $res->content);
    }

    public function test_mod_data_get_tagged_records_approval(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();

        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';

        $data1 = $this->getDataGenerator()->create_module('data', array('course' => $course1->id));
        $field1 = $datagenerator->create_field($fieldrecord, $data1);
        $data2 = $this->getDataGenerator()->create_module('data', array('course' => $course2->id, 'approval' => true));
        $field2 = $datagenerator->create_field($fieldrecord, $data2);

        $record11 = $datagenerator->create_entry($data1, [$field1->field->id => 'value11'], 0, ['Cats', 'Dogs']);
        $record21 = $datagenerator->create_entry($data2, [$field2->field->id => 'value21'], 0, ['Cats'], ['approved' => false]);
        $tag = \core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);
        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
        $this->assertEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);

        // Create and enrol a user.
        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole->id, 'manual');
        $this->setUser($student);

        // User can search data records inside a course.
        \core_tag_index_builder::reset_caches();
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringNotContainsString('value21', $res->content);

        $recordtoupdate = new \stdClass();
        $recordtoupdate->id = $record21;
        $recordtoupdate->approved = true;
        $DB->update_record('data_records', $recordtoupdate);

        \core_tag_index_builder::reset_caches();
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
    }

    public function test_mod_data_get_tagged_records_time(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();

        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';

        $timefrom = time() - YEARSECS;
        $timeto = time() - WEEKSECS;

        $data1 = $this->getDataGenerator()->create_module('data', array('course' => $course1->id, 'approval' => true));
        $field1 = $datagenerator->create_field($fieldrecord, $data1);
        $data2 = $this->getDataGenerator()->create_module('data', array('course' => $course2->id,
                                                                        'timeviewfrom' => $timefrom,
                                                                        'timeviewto'   => $timeto));
        $field2 = $datagenerator->create_field($fieldrecord, $data2);
        $record11 = $datagenerator->create_entry($data1, [$field1->field->id => 'value11'], 0, ['Cats', 'Dogs']);
        $record21 = $datagenerator->create_entry($data2, [$field2->field->id => 'value21'], 0, ['Cats']);
        $tag = \core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);
        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
        $this->assertEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);

        // Create and enrol a user.
        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole->id, 'manual');
        $this->setUser($student);

        // User can search data records inside a course.
        \core_tag_index_builder::reset_caches();
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringNotContainsString('value21', $res->content);

        $data2->timeviewto = time() + YEARSECS;
        $DB->update_record('data', $data2);

        \core_tag_index_builder::reset_caches();
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
    }

    public function test_mod_data_get_tagged_records_course_enrolment(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();

        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';

        $data1 = $this->getDataGenerator()->create_module('data', array('course' => $course1->id, 'approval' => true));
        $field1 = $datagenerator->create_field($fieldrecord, $data1);
        $data2 = $this->getDataGenerator()->create_module('data', array('course' => $course2->id));
        $field2 = $datagenerator->create_field($fieldrecord, $data2);

        $record11 = $datagenerator->create_entry($data1, [$field1->field->id => 'value11'], 0, ['Cats', 'Dogs']);
        $record21 = $datagenerator->create_entry($data2, [$field2->field->id => 'value21'], 0, ['Cats']);
        $tag = \core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);
        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
        $this->assertEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);

        // Create and enrol a user.
        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->setUser($student);
        \core_tag_index_builder::reset_caches();

        // User can search data records inside a course.
        $coursecontext = \context_course::instance($course1->id);
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringNotContainsString('value21', $res->content);

        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole->id, 'manual');

        \core_tag_index_builder::reset_caches();
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
    }

    public function test_mod_data_get_tagged_records_course_groups(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();

        $groupa = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'name' => 'groupA'));
        $groupb = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'name' => 'groupB'));

        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';

        $data1 = $this->getDataGenerator()->create_module('data', array('course' => $course1->id, 'approval' => true));
        $field1 = $datagenerator->create_field($fieldrecord, $data1);
        $data2 = $this->getDataGenerator()->create_module('data', array('course' => $course2->id));
        $field2 = $datagenerator->create_field($fieldrecord, $data2);
        set_coursemodule_groupmode($data2->cmid, SEPARATEGROUPS);

        $record11 = $datagenerator->create_entry($data1, [$field1->field->id => 'value11'],
                0, ['Cats', 'Dogs']);
        $record21 = $datagenerator->create_entry($data2, [$field2->field->id => 'value21'],
                $groupa->id, ['Cats']);
        $record22 = $datagenerator->create_entry($data2, [$field2->field->id => 'value22'],
                $groupb->id, ['Cats']);
        $tag = \core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);
        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
        $this->assertStringContainsString('value22', $res->content);
        $this->assertEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);

        // Create and enrol a user.
        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole->id, 'manual');
        groups_add_member($groupa, $student);
        $this->setUser($student);
        \core_tag_index_builder::reset_caches();

        // User can search data records inside a course.
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
        $this->assertStringNotContainsString('value22', $res->content);

        groups_add_member($groupb, $student);
        \core_tag_index_builder::reset_caches();
        $res = mod_data_get_tagged_records($tag, false, 0, 0, 1, 0);

        $this->assertStringContainsString('value11', $res->content);
        $this->assertStringContainsString('value21', $res->content);
        $this->assertStringContainsString('value22', $res->content);
    }

    /**
     * Test check_updates_since callback.
     */
    public function test_check_updates_since(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        // Create user.
        $student = self::getDataGenerator()->create_user();
        // User enrolment.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->setCurrentTimeStart();
        $record = array(
            'course' => $course->id,
        );
        $data = $this->getDataGenerator()->create_module('data', $record);
        $cm = get_coursemodule_from_instance('data', $data->id, $course->id);
        $cm = \cm_info::create($cm);
        $this->setUser($student);

        // Check that upon creation, the updates are only about the new configuration created.
        $onehourago = time() - HOURSECS;
        $updates = data_check_updates_since($cm, $onehourago);
        foreach ($updates as $el => $val) {
            if ($el == 'configuration') {
                $this->assertTrue($val->updated);
                $this->assertTimeCurrent($val->timeupdated);
            } else {
                $this->assertFalse($val->updated);
            }
        }

        // Add a couple of entries.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldtypes = array('checkbox', 'date');

        $count = 1;
        // Creating test Fields with default parameter values.
        foreach ($fieldtypes as $fieldtype) {
            // Creating variables dynamically.
            $fieldname = 'field-' . $count;
            $record = new \stdClass();
            $record->name = $fieldname;
            $record->type = $fieldtype;
            $record->required = 1;

            ${$fieldname} = $datagenerator->create_field($record, $data);
            $count++;
        }

        $fields = $DB->get_records('data_fields', array('dataid' => $data->id), 'id');

        $contents = array();
        $contents[] = array('opt1', 'opt2', 'opt3', 'opt4');
        $contents[] = '01-01-2037'; // It should be lower than 2038, to avoid failing on 32-bit windows.
        $count = 0;
        $fieldcontents = array();
        foreach ($fields as $fieldrecord) {
            $fieldcontents[$fieldrecord->id] = $contents[$count++];
        }

        $datarecor1did = $datagenerator->create_entry($data, $fieldcontents);
        $datarecor2did = $datagenerator->create_entry($data, $fieldcontents);
        $records = $DB->get_records('data_records', array('dataid' => $data->id));
        $this->assertCount(2, $records);
        // Check we received the entries updated.
        $updates = data_check_updates_since($cm, $onehourago);
        $this->assertTrue($updates->entries->updated);
        $this->assertEqualsCanonicalizing([$datarecor1did, $datarecor2did], $updates->entries->itemids);
    }

    public function test_data_core_calendar_provide_event_action_in_hidden_section(): void {
        global $CFG;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
                'timeavailablefrom' => time() - DAYSECS, 'timeavailableto' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Set sections 0 as hidden.
        set_section_visible($course->id, 0, 0);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_data_core_calendar_provide_event_action_for_non_user(): void {
        global $CFG;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
            'timeavailablefrom' => time() - DAYSECS, 'timeavailableto' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_data_core_calendar_provide_event_action_open(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
            'timeavailablefrom' => time() - DAYSECS, 'timeavailableto' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('add', 'data'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_data_core_calendar_provide_event_action_open_for_user(): void {
        global $CFG;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
            'timeavailablefrom' => time() - DAYSECS, 'timeavailableto' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('add', 'data'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_data_core_calendar_provide_event_action_closed(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
            'timeavailableto' => time() - DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory);

        // No event on the dashboard if module is closed.
        $this->assertNull($actionevent);
    }

    public function test_data_core_calendar_provide_event_action_closed_for_user(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
            'timeavailableto' => time() - DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Now log out.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory, $student->id);

        // No event on the dashboard if module is closed.
        $this->assertNull($actionevent);
    }

    public function test_data_core_calendar_provide_event_action_open_in_future(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
            'timeavailablefrom' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('add', 'data'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_data_core_calendar_provide_event_action_open_in_future_for_user(): void {
        global $CFG;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id,
            'timeavailablefrom' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('add', 'data'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_data_core_calendar_provide_event_action_no_time_specified(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('add', 'data'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_data_core_calendar_provide_event_action_no_time_specified_for_user(): void {
        global $CFG;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a database activity.
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $data->id, DATA_EVENT_TYPE_OPEN);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_data_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('add', 'data'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid
     * @param int $instanceid The data id.
     * @param string $eventtype The event type. eg. DATA_EVENT_TYPE_OPEN.
     * @param int|null $timestart The start timestamp for the event
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype, $timestart = null) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'data';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        if ($timestart) {
            $event->timestart = $timestart;
        } else {
            $event->timestart = time();
        }

        return \calendar_event::create($event);
    }

    /**
     * Test the callback responsible for returning the completion rule descriptions.
     * This function should work given either an instance of the module (cm_info), such as when checking the active rules,
     * or if passed a stdClass of similar structure, such as when checking the the default completion settings for a mod type.
     */
    public function test_mod_data_completion_get_active_rule_descriptions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionentries' rule, one doesn't.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 2]);
        $data1 = $this->getDataGenerator()->create_module('data', [
            'course' => $course->id,
            'completion' => 2,
            'completionentries' => 3
        ]);
        $data2 = $this->getDataGenerator()->create_module('data', [
            'course' => $course->id,
            'completion' => 2,
            'completionentries' => 0
        ]);
        $cm1 = \cm_info::create(get_coursemodule_from_instance('data', $data1->id));
        $cm2 = \cm_info::create(get_coursemodule_from_instance('data', $data2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new \stdClass();
        $moddefaults->customdata = ['customcompletionrules' => ['completionentries' => 3]];
        $moddefaults->completion = 2;

        $activeruledescriptions = [get_string('completionentriesdesc', 'data', 3)];
        $this->assertEquals(mod_data_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_data_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_data_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_data_get_completion_active_rule_descriptions(new \stdClass()), []);
    }

    /**
     * An unknown event type should not change the data instance.
     */
    public function test_mod_data_core_calendar_event_timestart_updated_unknown_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $datagenerator = $generator->get_plugin_generator('mod_data');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $data = $datagenerator->create_instance(['course' => $course->id]);
        $data->timeavailablefrom = $timeopen;
        $data->timeavailableto = $timeclose;
        $DB->update_record('data', $data);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'data',
            'instance' => $data->id,
            'eventtype' => DATA_EVENT_TYPE_OPEN . "SOMETHING ELSE",
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        mod_data_core_calendar_event_timestart_updated($event, $data);
        $data = $DB->get_record('data', ['id' => $data->id]);
        $this->assertEquals($timeopen, $data->timeavailablefrom);
        $this->assertEquals($timeclose, $data->timeavailableto);
    }

    /**
     * A DATA_EVENT_TYPE_OPEN event should update the timeavailablefrom property of the data activity.
     */
    public function test_mod_data_core_calendar_event_timestart_updated_open_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $datagenerator = $generator->get_plugin_generator('mod_data');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeopen = $timeopen - DAYSECS;
        $data = $datagenerator->create_instance(['course' => $course->id]);
        $data->timeavailablefrom = $timeopen;
        $data->timeavailableto = $timeclose;
        $data->timemodified = $timemodified;
        $DB->update_record('data', $data);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'data',
            'instance' => $data->id,
            'eventtype' => DATA_EVENT_TYPE_OPEN,
            'timestart' => $newtimeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();
        mod_data_core_calendar_event_timestart_updated($event, $data);
        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });
        $data = $DB->get_record('data', ['id' => $data->id]);

        // Ensure the timeavailablefrom property matches the event timestart.
        $this->assertEquals($newtimeopen, $data->timeavailablefrom);
        // Ensure the timeavailableto isn't changed.
        $this->assertEquals($timeclose, $data->timeavailableto);
        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $data->timemodified);
        // Confirm that a module updated event is fired when the module is changed.
        $this->assertNotEmpty($moduleupdatedevents);
    }

    /**
     * A DATA_EVENT_TYPE_CLOSE event should update the timeavailableto property of the data activity.
     */
    public function test_mod_data_core_calendar_event_timestart_updated_close_event(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $datagenerator = $generator->get_plugin_generator('mod_data');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeclose = $timeclose + DAYSECS;
        $data = $datagenerator->create_instance(['course' => $course->id]);
        $data->timeavailablefrom = $timeopen;
        $data->timeavailableto = $timeclose;
        $data->timemodified = $timemodified;
        $DB->update_record('data', $data);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'data',
            'instance' => $data->id,
            'eventtype' => DATA_EVENT_TYPE_CLOSE,
            'timestart' => $newtimeclose,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();
        mod_data_core_calendar_event_timestart_updated($event, $data);
        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });
        $data = $DB->get_record('data', ['id' => $data->id]);

        // Ensure the timeavailableto property matches the event timestart.
        $this->assertEquals($newtimeclose, $data->timeavailableto);
        // Ensure the timeavailablefrom isn't changed.
        $this->assertEquals($timeopen, $data->timeavailablefrom);
        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $data->timemodified);
        // Confirm that a module updated event is fired when the module is changed.
        $this->assertNotEmpty($moduleupdatedevents);
    }

    /**
     * An unknown event type should not have any limits.
     */
    public function test_mod_data_core_calendar_get_valid_event_timestart_range_unknown_event(): void {
        global $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $data = new \stdClass();
        $data->timeavailablefrom = $timeopen;
        $data->timeavailableto = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'data',
            'instance' => 1,
            'eventtype' => DATA_EVENT_TYPE_OPEN . "SOMETHING ELSE",
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list ($min, $max) = mod_data_core_calendar_get_valid_event_timestart_range($event, $data);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * The open event should be limited by the data's timeclose property, if it's set.
     */
    public function test_mod_data_core_calendar_get_valid_event_timestart_range_open_event(): void {
        global $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $data = new \stdClass();
        $data->timeavailablefrom = $timeopen;
        $data->timeavailableto = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'data',
            'instance' => 1,
            'eventtype' => DATA_EVENT_TYPE_OPEN,
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // The max limit should be bounded by the timeclose value.
        list ($min, $max) = mod_data_core_calendar_get_valid_event_timestart_range($event, $data);
        $this->assertNull($min);
        $this->assertEquals($timeclose, $max[0]);

        // No timeclose value should result in no upper limit.
        $data->timeavailableto = 0;
        list ($min, $max) = mod_data_core_calendar_get_valid_event_timestart_range($event, $data);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * The close event should be limited by the data's timeavailablefrom property, if it's set.
     */
    public function test_mod_data_core_calendar_get_valid_event_timestart_range_close_event(): void {
        global $CFG;

        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $data = new \stdClass();
        $data->timeavailablefrom = $timeopen;
        $data->timeavailableto = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'data',
            'instance' => 1,
            'eventtype' => DATA_EVENT_TYPE_CLOSE,
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // The max limit should be bounded by the timeclose value.
        list ($min, $max) = mod_data_core_calendar_get_valid_event_timestart_range($event, $data);
        $this->assertEquals($timeopen, $min[0]);
        $this->assertNull($max);

        // No timeavailableto value should result in no upper limit.
        $data->timeavailablefrom = 0;
        list ($min, $max) = mod_data_core_calendar_get_valid_event_timestart_range($event, $data);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * A user who does not have capabilities to add events to the calendar should be able to create an database.
     */
    public function test_creation_with_no_calendar_capabilities(): void {
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $user = self::getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $roleid = self::getDataGenerator()->create_role();
        self::getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context, true);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_data');
        // Create an instance as a user without the calendar capabilities.
        $this->setUser($user);
        $time = time();
        $params = array(
            'course' => $course->id,
            'timeavailablefrom' => $time + 200,
            'timeavailableto' => $time + 2000,
            'timeviewfrom' => $time + 400,
            'timeviewto' => $time + 2000,
        );
        $generator->create_instance($params);
    }

    /**
     * Test for data_generate_default_template(). This method covers different scenarios for checking when the returned value
     * is empty or not, but doesn't check if the content has the expected value when it's not empty.
     *
     * @covers ::data_generate_default_template
     */
    public function test_data_generate_default_template(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, ['course' => $course]);

        // Check the result is empty when $data and/or $template are null.
        $nullactivity = null;
        $result = data_generate_default_template($nullactivity, 'listtemplate', 0, false, false);
        $this->assertEmpty($result);
        $result = data_generate_default_template($activity, null, 0, false, false);
        $this->assertEmpty($result);
        $result = data_generate_default_template($nullactivity, null, 0, false, false);
        $this->assertEmpty($result);

        // Check the result is empty when any of the templates that are empty are given.
        $emptytemplates = [
            'csstemplate',
            'jstemplate',
            'listtemplateheader',
            'listtemplatefooter',
            'rsstitletemplate',
        ];
        foreach ($emptytemplates as $emptytemplate) {
            $result = data_generate_default_template($activity, $emptytemplate, 0, false, false);
            $this->assertEmpty($result);
        }

        $templates = [
            'listtemplate',
            'singletemplate',
            'asearchtemplate',
        ];
        // Check the result is empty when the database has no fields.
        foreach ($templates as $template) {
            $result = data_generate_default_template($activity, $template, 0, false, false);
            $this->assertEmpty($result);
            $this->assertEmpty($activity->{$template});
        }

        // Add a field to the activity.
        $fieldrecord = new stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $datagenerator->create_field($fieldrecord, $activity);

        // Check the result is not empty when the database has no entries.
        foreach ($templates as $template) {
            $result = data_generate_default_template($activity, $template, 0, false, false);
            $this->assertNotEmpty($result);
            $this->assertEmpty($activity->{$template});
        }

        // Check the result is not empty when the database has no entries and the result is saved when $update = true.
        foreach ($templates as $template) {
            $result = data_generate_default_template($activity, $template, 0, false, true);
            $this->assertNotEmpty($result);
            $this->assertNotEmpty($activity->{$template});
        }
    }

    /**
     * Test for data_replace_field_in_templates().
     *
     * @covers ::data_replace_field_in_templates
     */
    public function test_data_replace_field_in_templates(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $templatecontent = "Field [[myfield]], [[myfield#id]], [[myfield#name]], [[myfield#description]], ";

        $params = ['course' => $course];
        foreach (manager::TEMPLATES_LIST as $templatename => $templatefile) {
            $params[$templatename] = $templatecontent;
        }
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, $params);

        $generator = $this->getDataGenerator()->get_plugin_generator(manager::PLUGINNAME);
        $fieldrecord = (object)['name' => 'myfield', 'type' => 'text', 'description' => 'This is a field'];
        $generator->create_field($fieldrecord, $activity);

        data_replace_field_in_templates($activity, 'myfield', 'newfieldname');
        $dbactivity = $DB->get_record(manager::MODULE, ['id' => $activity->id]);

        $newcontent = "Field [[newfieldname]], [[newfieldname#id]], [[newfieldname#name]], [[newfieldname#description]], ";
        // Field compatible templates.
        $this->assertEquals($newcontent, $dbactivity->listtemplate);
        $this->assertEquals($newcontent, $dbactivity->singletemplate);
        $this->assertEquals($newcontent, $dbactivity->asearchtemplate);
        $this->assertEquals($newcontent, $dbactivity->addtemplate);
        $this->assertEquals($newcontent, $dbactivity->rsstemplate);
        // Other templates.
        $this->assertEquals($templatecontent, $dbactivity->listtemplateheader);
        $this->assertEquals($templatecontent, $dbactivity->listtemplatefooter);
        $this->assertEquals($templatecontent, $dbactivity->csstemplate);
        $this->assertEquals($templatecontent, $dbactivity->jstemplate);
        $this->assertEquals($templatecontent, $dbactivity->rsstitletemplate);
    }

    /**
     * Test for data_append_new_field_to_templates().
     *
     * @covers ::data_append_new_field_to_templates
     * @dataProvider data_append_new_field_to_templates_provider
     * @param bool $hasfield if the field is present in the templates
     * @param bool $hasotherfields if the field is not present in the templates
     * @param bool $expected the expected return
     */
    public function test_data_append_new_field_to_templates(bool $hasfield, bool $hasotherfields, bool $expected): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $templatecontent = "Template content";
        if ($hasfield) {
            $templatecontent .= "Has [[myfield]].";
        }
        if ($hasotherfields) {
            $templatecontent .= "And also ##otherfields##.";
        }

        $course = $this->getDataGenerator()->create_course();
        $params = ['course' => $course];
        foreach (manager::TEMPLATES_LIST as $templatename => $templatefile) {
            $params[$templatename] = $templatecontent;
        }
        $activity = $this->getDataGenerator()->create_module(manager::MODULE, $params);

        $result = data_append_new_field_to_templates($activity, 'myfield');
        $this->assertEquals($expected, $result);

        // Check fields with auto add fields.
        $dbactivity = $DB->get_record(manager::MODULE, ['id' => $activity->id]);
        if ($hasfield || $hasotherfields) {
            $this->assertEquals($dbactivity->singletemplate, $templatecontent);
            $this->assertEquals($dbactivity->addtemplate, $templatecontent);
            $this->assertEquals($dbactivity->rsstemplate, $templatecontent);
        } else {
            $regexp = '|Template content.*\[\[myfield\]\]|';
            // We don't want line breaks for the validations.
            $this->assertMatchesRegularExpression($regexp, str_replace("\n", '', $dbactivity->singletemplate));
            $this->assertMatchesRegularExpression($regexp, str_replace("\n", '', $dbactivity->addtemplate));
            $this->assertMatchesRegularExpression($regexp, str_replace("\n", '', $dbactivity->rsstemplate));
        }
        // No auto add field templates.
        $this->assertEquals($dbactivity->asearchtemplate, $templatecontent);
        $this->assertEquals($dbactivity->listtemplate, $templatecontent);
        $this->assertEquals($dbactivity->listtemplateheader, $templatecontent);
        $this->assertEquals($dbactivity->listtemplatefooter, $templatecontent);
        $this->assertEquals($dbactivity->csstemplate, $templatecontent);
        $this->assertEquals($dbactivity->jstemplate, $templatecontent);
        $this->assertEquals($dbactivity->rsstitletemplate, $templatecontent);
    }

    /**
     * Data provider for test_data_append_new_field_to_templates().
     *
     * @return array of scenarios
     */
    public static function data_append_new_field_to_templates_provider(): array {
        return [
            'Plain template' => [
                'hasfield' => false,
                'hasotherfields' => false,
                'expected' => true,
            ],
            'Field already present' => [
                'hasfield' => true,
                'hasotherfields' => false,
                'expected' => false,
            ],
            '##otherfields## tag present' => [
                'hasfield' => false,
                'hasotherfields' => true,
                'expected' => false,
            ],
            'Field already present and ##otherfields## tag present' => [
                'hasfield' => true,
                'hasotherfields' => true,
                'expected' => false,
            ],
        ];
    }

    /**
     * Test that format that are not supported are raising an exception
     *
     * @param string $type
     * @param string $expected
     * @covers \data_get_field_new
     * @dataProvider format_parser_provider
     */
    public function test_create_field(string $type, string $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $data = $this->getDataGenerator()->create_module('data', ['course' => $course->id]  );
        if ($expected === 'exception') {
            $this->expectException(\moodle_exception::class);
        }
        $field = data_get_field_new($type, $data);
        $this->assertStringContainsString($expected, get_class($field));
    }

    /**
     * Data provider for test_format_parser
     *
     * @return array[]
     */
    public static function format_parser_provider(): array {
        return [
            'text' => [
                'type' => 'text',
                'expected' => 'data_field_text',
            ],
            'picture' => [
                'type' => 'picture',
                'expected' => 'data_field_picture',
            ],
            'wrong type' => [
                'type' => '../wrongformat123',
                'expected' => 'exception',
            ],
        ];
    }
}
