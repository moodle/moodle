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

namespace enrol_category;

/**
 * Category enrolment sync functional test.
 *
 * @package    enrol_category
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_test extends \advanced_testcase {

    protected function enable_plugin() {
        $enabled = enrol_get_plugins(true);
        $enabled['category'] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    protected function disable_plugin() {
        $enabled = enrol_get_plugins(true);
        unset($enabled['category']);
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    protected function enable_role_sync($roleid) {
        $syscontext = \context_system::instance();

        assign_capability('enrol/category:synchronised', CAP_ALLOW, $roleid, $syscontext, true);
    }

    protected function disable_role_sync($roleid) {
        $syscontext = \context_system::instance();

        unassign_capability('enrol/category:synchronised', $roleid, $syscontext);
    }

    /**
     * Test utility methods used in syn test, fail here means something
     * in core accesslib was changed, but it is possible that only this test
     * is affected, nto the plugin itself...
     */
    public function test_utils(): void {
        global $DB;

        $this->resetAfterTest();

        $syscontext = \context_system::instance();

        $this->assertFalse(enrol_is_enabled('category'));
        $this->enable_plugin();
        $this->assertTrue(enrol_is_enabled('category'));

        $roles = get_roles_with_capability('enrol/category:synchronised', CAP_ALLOW, $syscontext);
        $this->assertEmpty($roles);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);

        $this->enable_role_sync($studentrole->id);
        $roles = get_roles_with_capability('enrol/category:synchronised', CAP_ALLOW, $syscontext);
        $this->assertEquals(1, count($roles));
        $this->assertEquals($studentrole, reset($roles));

        $this->disable_role_sync($studentrole->id);
        $roles = get_roles_with_capability('enrol/category:synchronised', CAP_ALLOW, $syscontext);
        $this->assertEmpty($roles);
    }

    public function test_handler_sync(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/enrol/category/locallib.php');

        $this->resetAfterTest();

        // Setup a few courses and categories.

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $this->assertNotEmpty($managerrole);

        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category();
        $cat3 = $this->getDataGenerator()->create_category(array('parent'=>$cat2->id));

        $course1 = $this->getDataGenerator()->create_course(array('category'=>$cat1->id));
        $course2 = $this->getDataGenerator()->create_course(array('category'=>$cat2->id));
        $course3 = $this->getDataGenerator()->create_course(array('category'=>$cat3->id));
        $course4 = $this->getDataGenerator()->create_course(array('category'=>$cat3->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $this->enable_role_sync($studentrole->id);
        $this->enable_role_sync($teacherrole->id);
        $this->enable_plugin();

        $this->assertEquals(0, $DB->count_records('role_assignments', array()));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));

        // Test assign event.

        role_assign($managerrole->id, $user1->id, \context_coursecat::instance($cat1->id));
        role_assign($managerrole->id, $user3->id, \context_course::instance($course1->id));
        role_assign($managerrole->id, $user3->id, \context_course::instance($course2->id));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));

        role_assign($studentrole->id, $user1->id, \context_coursecat::instance($cat2->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course2->id), $user1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course3->id), $user1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course4->id), $user1->id));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));

        role_assign($managerrole->id, $user2->id, \context_coursecat::instance($cat3->id));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));

        role_assign($teacherrole->id, $user4->id, \context_coursecat::instance($cat1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course1->id), $user4->id));
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));

        // Test role unassigned event.

        role_unassign($teacherrole->id, $user4->id, \context_coursecat::instance($cat1->id)->id);
        $this->assertFalse(is_enrolled(\context_course::instance($course1->id), $user4->id));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));

        // Make sure handlers are disabled when plugin disabled.

        $this->disable_plugin();
        role_unassign($studentrole->id, $user1->id, \context_coursecat::instance($cat2->id)->id);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));

        role_assign($studentrole->id, $user3->id, \context_coursecat::instance($cat1->id));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));

    }

    public function test_sync_course(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/enrol/category/locallib.php');

        $this->resetAfterTest();

        // Setup a few courses and categories.

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $this->assertNotEmpty($managerrole);

        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category();
        $cat3 = $this->getDataGenerator()->create_category(array('parent'=>$cat2->id));

        $course1 = $this->getDataGenerator()->create_course(array('category'=>$cat1->id));
        $course2 = $this->getDataGenerator()->create_course(array('category'=>$cat2->id));
        $course3 = $this->getDataGenerator()->create_course(array('category'=>$cat3->id));
        $course4 = $this->getDataGenerator()->create_course(array('category'=>$cat3->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $this->enable_role_sync($studentrole->id);
        $this->enable_role_sync($teacherrole->id);
        $this->enable_plugin();

        $this->assertEquals(0, $DB->count_records('role_assignments', array()));
        role_assign($managerrole->id, $user1->id, \context_coursecat::instance($cat1->id));
        role_assign($managerrole->id, $user3->id, \context_course::instance($course1->id));
        role_assign($managerrole->id, $user3->id, \context_course::instance($course2->id));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));


        $this->disable_plugin(); // Stops the event handlers.
        role_assign($studentrole->id, $user1->id, \context_coursecat::instance($cat2->id));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->enable_plugin();
        enrol_category_sync_course($course2);
        $this->assertTrue(is_enrolled(\context_course::instance($course2->id), $user1->id));
        $this->assertFalse(is_enrolled(\context_course::instance($course3->id), $user1->id));
        $this->assertFalse(is_enrolled(\context_course::instance($course4->id), $user1->id));
        $this->assertEquals(1, $DB->count_records('user_enrolments', array()));

        enrol_category_sync_course($course2);
        enrol_category_sync_course($course3);
        enrol_category_sync_course($course4);
        $this->assertFalse(is_enrolled(\context_course::instance($course1->id), $user1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course2->id), $user1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course3->id), $user1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course4->id), $user1->id));
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));

        $this->disable_plugin(); // Stops the event handlers.
        role_assign($studentrole->id, $user2->id, \context_coursecat::instance($cat1->id));
        role_assign($teacherrole->id, $user4->id, \context_coursecat::instance($cat1->id));
        role_unassign($studentrole->id, $user1->id, \context_coursecat::instance($cat2->id)->id);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->enable_plugin();
        enrol_category_sync_course($course2);
        $this->assertFalse(is_enrolled(\context_course::instance($course2->id), $user1->id));
        $this->assertFalse(is_enrolled(\context_course::instance($course2->id), $user2->id));
        $this->assertFalse(is_enrolled(\context_course::instance($course2->id), $user4->id));
        enrol_category_sync_course($course1);
        enrol_category_sync_course($course3);
        enrol_category_sync_course($course4);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertTrue(is_enrolled(\context_course::instance($course1->id), $user2->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course1->id), $user4->id));

        $this->disable_role_sync($studentrole->id);
        enrol_category_sync_course($course1);
        enrol_category_sync_course($course2);
        enrol_category_sync_course($course3);
        enrol_category_sync_course($course4);
        $this->assertEquals(1, $DB->count_records('user_enrolments', array()));
        $this->assertTrue(is_enrolled(\context_course::instance($course1->id), $user4->id));

        $this->assertEquals(1, $DB->count_records('enrol', array('enrol'=>'category')));
        $this->disable_role_sync($teacherrole->id);
        enrol_category_sync_course($course1);
        enrol_category_sync_course($course2);
        enrol_category_sync_course($course3);
        enrol_category_sync_course($course4);
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(0, $DB->count_records('enrol', array('enrol'=>'category')));
    }

    public function test_sync_full(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/enrol/category/locallib.php');

        $this->resetAfterTest();

        $trace = new \null_progress_trace();

        // Setup a few courses and categories.

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);
        $managerrole = $DB->get_record('role', array('shortname'=>'manager'));
        $this->assertNotEmpty($managerrole);

        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category();
        $cat3 = $this->getDataGenerator()->create_category(array('parent'=>$cat2->id));

        $course1 = $this->getDataGenerator()->create_course(array('category'=>$cat1->id));
        $course2 = $this->getDataGenerator()->create_course(array('category'=>$cat2->id));
        $course3 = $this->getDataGenerator()->create_course(array('category'=>$cat3->id));
        $course4 = $this->getDataGenerator()->create_course(array('category'=>$cat3->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $this->enable_role_sync($studentrole->id);
        $this->enable_role_sync($teacherrole->id);
        $this->enable_plugin();

        $this->assertEquals(0, $DB->count_records('role_assignments', array()));
        role_assign($managerrole->id, $user1->id, \context_coursecat::instance($cat1->id));
        role_assign($managerrole->id, $user3->id, \context_course::instance($course1->id));
        role_assign($managerrole->id, $user3->id, \context_course::instance($course2->id));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));

        $result = enrol_category_sync_full($trace);
        $this->assertSame(0, $result);

        $this->disable_plugin();
        role_assign($studentrole->id, $user1->id, \context_coursecat::instance($cat2->id));
        $this->enable_plugin();
        $result = enrol_category_sync_full($trace);
        $this->assertSame(0, $result);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertTrue(is_enrolled(\context_course::instance($course2->id), $user1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course3->id), $user1->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course4->id), $user1->id));

        $this->disable_plugin();
        role_unassign($studentrole->id, $user1->id, \context_coursecat::instance($cat2->id)->id);
        role_assign($studentrole->id, $user2->id, \context_coursecat::instance($cat1->id));
        role_assign($teacherrole->id, $user4->id, \context_coursecat::instance($cat1->id));
        role_assign($teacherrole->id, $user3->id, \context_coursecat::instance($cat2->id));
        role_assign($managerrole->id, $user3->id, \context_course::instance($course3->id));
        $this->enable_plugin();
        $result = enrol_category_sync_full($trace);
        $this->assertSame(0, $result);
        $this->assertEquals(5, $DB->count_records('user_enrolments', array()));
        $this->assertTrue(is_enrolled(\context_course::instance($course1->id), $user2->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course1->id), $user4->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course2->id), $user3->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course3->id), $user3->id));
        $this->assertTrue(is_enrolled(\context_course::instance($course4->id), $user3->id));

        // Cleanup everything.

        $this->assertNotEmpty($DB->count_records('role_assignments', array()));
        $this->assertNotEmpty($DB->count_records('user_enrolments', array()));

        $this->disable_plugin();
        role_unassign_all(array('roleid'=>$studentrole->id));
        role_unassign_all(array('roleid'=>$managerrole->id));
        role_unassign_all(array('roleid'=>$teacherrole->id));

        $result = enrol_category_sync_full($trace);
        $this->assertSame(2, $result);
        $this->assertEquals(0, $DB->count_records('role_assignments', array()));
        $this->assertNotEmpty($DB->count_records('user_enrolments', array()));
        $this->disable_role_sync($studentrole->id);
        $this->disable_role_sync($teacherrole->id);

        $this->enable_plugin();
        $result = enrol_category_sync_full($trace);
        $this->assertSame(0, $result);
        $this->assertEquals(0, $DB->count_records('role_assignments', array()));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(0, $DB->count_records('enrol', array('enrol'=>'category')));
    }
}
