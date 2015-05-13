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
 * Group external PHPunit tests
 *
 * @package    core_group
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/group/externallib.php');
require_once($CFG->dirroot . '/group/lib.php');

class core_group_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test create_groups
     */
    public function test_create_groups() {
        global $DB;

        $this->resetAfterTest(true);

        $course  = self::getDataGenerator()->create_course();

        $group1 = array();
        $group1['courseid'] = $course->id;
        $group1['name'] = 'Group Test 1';
        $group1['description'] = 'Group Test 1 description';
        $group1['descriptionformat'] = FORMAT_MOODLE;
        $group1['enrolmentkey'] = 'Test group enrol secret phrase';
        $group2 = array();
        $group2['courseid'] = $course->id;
        $group2['name'] = 'Group Test 2';
        $group2['description'] = 'Group Test 2 description';
        $group3 = array();
        $group3['courseid'] = $course->id;
        $group3['name'] = 'Group Test 3';
        $group3['description'] = 'Group Test 3 description';

        // Set the required capabilities by the external function
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Call the external function.
        $groups = core_group_external::create_groups(array($group1, $group2));

        // We need to execute the return values cleaning process to simulate the web service server.
        $groups = external_api::clean_returnvalue(core_group_external::create_groups_returns(), $groups);

        // Checks against DB values
        $this->assertEquals(2, count($groups));
        foreach ($groups as $group) {
            $dbgroup = $DB->get_record('groups', array('id' => $group['id']), '*', MUST_EXIST);
            switch ($dbgroup->name) {
                case $group1['name']:
                    $groupdescription = $group1['description'];
                    $groupcourseid = $group1['courseid'];
                    $this->assertEquals($dbgroup->descriptionformat, $group1['descriptionformat']);
                    $this->assertEquals($dbgroup->enrolmentkey, $group1['enrolmentkey']);
                    break;
                case $group2['name']:
                    $groupdescription = $group2['description'];
                    $groupcourseid = $group2['courseid'];
                    break;
                default:
                    throw new moodle_exception('unknowgroupname');
                    break;
            }
            $this->assertEquals($dbgroup->description, $groupdescription);
            $this->assertEquals($dbgroup->courseid, $groupcourseid);
        }

        // Call without required capability
        $this->unassignUserCapability('moodle/course:managegroups', $context->id, $roleid);
        $this->setExpectedException('required_capability_exception');
        $froups = core_group_external::create_groups(array($group3));
    }

    /**
     * Test get_groups
     */
    public function test_get_groups() {
        global $DB;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();
        $group1data = array();
        $group1data['courseid'] = $course->id;
        $group1data['name'] = 'Group Test 1';
        $group1data['description'] = 'Group Test 1 description';
        $group1data['descriptionformat'] = FORMAT_MOODLE;
        $group1data['enrolmentkey'] = 'Test group enrol secret phrase';
        $group2data = array();
        $group2data['courseid'] = $course->id;
        $group2data['name'] = 'Group Test 2';
        $group2data['description'] = 'Group Test 2 description';
        $group1 = self::getDataGenerator()->create_group($group1data);
        $group2 = self::getDataGenerator()->create_group($group2data);

        // Set the required capabilities by the external function
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Call the external function.
        $groups = core_group_external::get_groups(array($group1->id, $group2->id));

        // We need to execute the return values cleaning process to simulate the web service server.
        $groups = external_api::clean_returnvalue(core_group_external::get_groups_returns(), $groups);

        // Checks against DB values
        $this->assertEquals(2, count($groups));
        foreach ($groups as $group) {
            $dbgroup = $DB->get_record('groups', array('id' => $group['id']), '*', MUST_EXIST);
            switch ($dbgroup->name) {
                case $group1->name:
                    $groupdescription = $group1->description;
                    $groupcourseid = $group1->courseid;
                    $this->assertEquals($dbgroup->descriptionformat, $group1->descriptionformat);
                    $this->assertEquals($dbgroup->enrolmentkey, $group1->enrolmentkey);
                    break;
                case $group2->name:
                    $groupdescription = $group2->description;
                    $groupcourseid = $group2->courseid;
                    break;
                default:
                    throw new moodle_exception('unknowgroupname');
                    break;
            }
            $this->assertEquals($dbgroup->description, $groupdescription);
            $this->assertEquals($dbgroup->courseid, $groupcourseid);
        }

        // Call without required capability
        $this->unassignUserCapability('moodle/course:managegroups', $context->id, $roleid);
        $this->setExpectedException('required_capability_exception');
        $groups = core_group_external::get_groups(array($group1->id, $group2->id));
    }

    /**
     * Test delete_groups
     */
    public function test_delete_groups() {
        global $DB;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();
        $group1data = array();
        $group1data['courseid'] = $course->id;
        $group1data['name'] = 'Group Test 1';
        $group1data['description'] = 'Group Test 1 description';
        $group1data['descriptionformat'] = FORMAT_MOODLE;
        $group1data['enrolmentkey'] = 'Test group enrol secret phrase';
        $group2data = array();
        $group2data['courseid'] = $course->id;
        $group2data['name'] = 'Group Test 2';
        $group2data['description'] = 'Group Test 2 description';
        $group3data['courseid'] = $course->id;
        $group3data['name'] = 'Group Test 3';
        $group3data['description'] = 'Group Test 3 description';
        $group1 = self::getDataGenerator()->create_group($group1data);
        $group2 = self::getDataGenerator()->create_group($group2data);
        $group3 = self::getDataGenerator()->create_group($group3data);

        // Set the required capabilities by the external function
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Checks against DB values
        $groupstotal = $DB->count_records('groups', array());
        $this->assertEquals(3, $groupstotal);

        // Call the external function.
        core_group_external::delete_groups(array($group1->id, $group2->id));

        // Checks against DB values
        $groupstotal = $DB->count_records('groups', array());
        $this->assertEquals(1, $groupstotal);

        // Call without required capability
        $this->unassignUserCapability('moodle/course:managegroups', $context->id, $roleid);
        $this->setExpectedException('required_capability_exception');
        $froups = core_group_external::delete_groups(array($group3->id));
    }

    /**
     * Test get_groupings
     */
    public function test_get_groupings() {
        global $DB;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        $groupingdata = array();
        $groupingdata['courseid'] = $course->id;
        $groupingdata['name'] = 'Grouping Test';
        $groupingdata['description'] = 'Grouping Test description';
        $groupingdata['descriptionformat'] = FORMAT_MOODLE;

        $grouping = self::getDataGenerator()->create_grouping($groupingdata);

        // Set the required capabilities by the external function.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:managegroups', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Call the external function without specifying the optional parameter.
        $groupings = core_group_external::get_groupings(array($grouping->id));
        // We need to execute the return values cleaning process to simulate the web service server.
        $groupings = external_api::clean_returnvalue(core_group_external::get_groupings_returns(), $groupings);

        $this->assertEquals(1, count($groupings));

        $group1data = array();
        $group1data['courseid'] = $course->id;
        $group1data['name'] = 'Group Test 1';
        $group1data['description'] = 'Group Test 1 description';
        $group1data['descriptionformat'] = FORMAT_MOODLE;
        $group2data = array();
        $group2data['courseid'] = $course->id;
        $group2data['name'] = 'Group Test 2';
        $group2data['description'] = 'Group Test 2 description';
        $group2data['descriptionformat'] = FORMAT_MOODLE;

        $group1 = self::getDataGenerator()->create_group($group1data);
        $group2 = self::getDataGenerator()->create_group($group2data);

        groups_assign_grouping($grouping->id, $group1->id);
        groups_assign_grouping($grouping->id, $group2->id);

        // Call the external function specifying that groups are returned.
        $groupings = core_group_external::get_groupings(array($grouping->id), true);
        // We need to execute the return values cleaning process to simulate the web service server.
        $groupings = external_api::clean_returnvalue(core_group_external::get_groupings_returns(), $groupings);
        $this->assertEquals(1, count($groupings));
        $this->assertEquals(2, count($groupings[0]['groups']));
        foreach ($groupings[0]['groups'] as $group) {
            $dbgroup = $DB->get_record('groups', array('id' => $group['id']), '*', MUST_EXIST);
            $dbgroupinggroups = $DB->get_record('groupings_groups',
                                                array('groupingid' => $groupings[0]['id'],
                                                      'groupid' => $group['id']),
                                                '*', MUST_EXIST);
            switch ($dbgroup->name) {
                case $group1->name:
                    $groupdescription = $group1->description;
                    $groupcourseid = $group1->courseid;
                    break;
                case $group2->name:
                    $groupdescription = $group2->description;
                    $groupcourseid = $group2->courseid;
                    break;
                default:
                    throw new moodle_exception('unknowgroupname');
                    break;
            }
            $this->assertEquals($dbgroup->description, $groupdescription);
            $this->assertEquals($dbgroup->courseid, $groupcourseid);
        }
    }

    /**
     * Test get_groups
     */
    public function test_get_course_user_groups() {
        global $DB;

        $this->resetAfterTest(true);

        $student1 = self::getDataGenerator()->create_user();
        $student2 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        $course = self::getDataGenerator()->create_course();
        $emptycourse = self::getDataGenerator()->create_course();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $emptycourse->id, $teacherrole->id);

        $group1data = array();
        $group1data['courseid'] = $course->id;
        $group1data['name'] = 'Group Test 1';
        $group1data['description'] = 'Group Test 1 description';
        $group2data = array();
        $group2data['courseid'] = $course->id;
        $group2data['name'] = 'Group Test 2';
        $group2data['description'] = 'Group Test 2 description';
        $group1 = self::getDataGenerator()->create_group($group1data);
        $group2 = self::getDataGenerator()->create_group($group2data);

        groups_add_member($group1->id, $student1->id);
        groups_add_member($group1->id, $student2->id);
        groups_add_member($group2->id, $student1->id);

        $this->setUser($student1);

        $groups = core_group_external::get_course_user_groups($course->id, $student1->id);
        $groups = external_api::clean_returnvalue(core_group_external::get_course_user_groups_returns(), $groups);
        // Check that I see my groups.
        $this->assertCount(2, $groups['groups']);

        $this->setUser($student2);
        $groups = core_group_external::get_course_user_groups($course->id, $student2->id);
        $groups = external_api::clean_returnvalue(core_group_external::get_course_user_groups_returns(), $groups);
        // Check that I see my groups.
        $this->assertCount(1, $groups['groups']);

        $this->assertEquals($group1data['name'], $groups['groups'][0]['name']);
        $this->assertEquals($group1data['description'], $groups['groups'][0]['description']);

        $this->setUser($teacher);
        $groups = core_group_external::get_course_user_groups($course->id, $student1->id);
        $groups = external_api::clean_returnvalue(core_group_external::get_course_user_groups_returns(), $groups);
        // Check that a teacher can see student groups.
        $this->assertCount(2, $groups['groups']);

        $groups = core_group_external::get_course_user_groups($course->id, $student2->id);
        $groups = external_api::clean_returnvalue(core_group_external::get_course_user_groups_returns(), $groups);
        // Check that a teacher can see student groups.
        $this->assertCount(1, $groups['groups']);

        // Check permissions.
        $this->setUser($student1);
        try {
            $groups = core_group_external::get_course_user_groups($course->id, $student2->id);
        } catch (moodle_exception $e) {
            $this->assertEquals('accessdenied', $e->errorcode);
        }

        try {
            $groups = core_group_external::get_course_user_groups($emptycourse->id, $student2->id);
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        $this->setUser($teacher);
        // Check warnings.
        $groups = core_group_external::get_course_user_groups($emptycourse->id, $student1->id);
        $groups = external_api::clean_returnvalue(core_group_external::get_course_user_groups_returns(), $groups);
        $this->assertCount(1, $groups['warnings']);

    }

}
