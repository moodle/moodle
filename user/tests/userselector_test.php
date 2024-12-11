<?php
// This file is part of Moodle - https://moodle.org/
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

namespace core_user;

use testable_user_selector;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/user/selector/lib.php');
require_once($CFG->dirroot.'/user/tests/fixtures/testable_user_selector.php');

/**
 * Tests for the implementation of {@link user_selector_base} class.
 *
 * @package   core_user
 * @category  test
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class userselector_test extends \advanced_testcase {

    /**
     * Setup the environment for the tests.
     */
    protected function setup_hidden_siteidentity() {
        global $CFG, $DB;

        $CFG->showuseridentity = 'idnumber,country,city';
        $CFG->hiddenuserfields = 'country,city';

        $env = new \stdClass();

        $env->student = $this->getDataGenerator()->create_user();
        $env->teacher = $this->getDataGenerator()->create_user();
        $env->manager = $this->getDataGenerator()->create_user();

        $env->course = $this->getDataGenerator()->create_course();
        $env->coursecontext = \context_course::instance($env->course->id);

        $env->teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $env->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $env->managerrole = $DB->get_record('role', array('shortname' => 'manager'));

        role_assign($env->studentrole->id, $env->student->id, $env->coursecontext->id);
        role_assign($env->teacherrole->id, $env->teacher->id, $env->coursecontext->id);
        role_assign($env->managerrole->id, $env->manager->id, SYSCONTEXTID);

        return $env;
    }

    /**
     * No identity fields are not shown to student user (no permission to view identity fields).
     */
    public function test_hidden_siteidentity_fields_no_access(): void {
        $this->resetAfterTest();
        $env = $this->setup_hidden_siteidentity();
        $this->setUser($env->student);

        $selector = new testable_user_selector('test');

        foreach ($selector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectNotHasProperty('idnumber', $user);
                $this->assertObjectNotHasProperty('country', $user);
                $this->assertObjectNotHasProperty('city', $user);
            }
        }
    }

    /**
     * Teacher can see students' identity fields only within the course.
     */
    public function test_hidden_siteidentity_fields_course_only_access(): void {
        $this->resetAfterTest();
        $env = $this->setup_hidden_siteidentity();
        $this->setUser($env->teacher);

        $systemselector = new testable_user_selector('test');
        $courseselector = new testable_user_selector('test', ['accesscontext' => $env->coursecontext]);

        foreach ($systemselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectNotHasProperty('idnumber', $user);
                $this->assertObjectNotHasProperty('country', $user);
                $this->assertObjectNotHasProperty('city', $user);
            }
        }

        foreach ($courseselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectHasProperty('country', $user);
                $this->assertObjectHasProperty('city', $user);
            }
        }
    }

    /**
     * Teacher can be prevented from seeing students' identity fields even within the course.
     */
    public function test_hidden_siteidentity_fields_course_prevented_access(): void {
        $this->resetAfterTest();
        $env = $this->setup_hidden_siteidentity();
        $this->setUser($env->teacher);

        assign_capability('moodle/course:viewhiddenuserfields', CAP_PREVENT, $env->teacherrole->id, $env->coursecontext->id);

        $courseselector = new testable_user_selector('test', ['accesscontext' => $env->coursecontext]);

        foreach ($courseselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectNotHasProperty('country', $user);
                $this->assertObjectNotHasProperty('city', $user);
            }
        }
    }

    /**
     * Manager can see students' identity fields anywhere.
     */
    public function test_hidden_siteidentity_fields_anywhere_access(): void {
        $this->resetAfterTest();
        $env = $this->setup_hidden_siteidentity();
        $this->setUser($env->manager);

        $systemselector = new testable_user_selector('test');
        $courseselector = new testable_user_selector('test', ['accesscontext' => $env->coursecontext]);

        foreach ($systemselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectHasProperty('country', $user);
                $this->assertObjectHasProperty('city', $user);
            }
        }

        foreach ($courseselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectHasProperty('country', $user);
                $this->assertObjectHasProperty('city', $user);
            }
        }
    }

    /**
     * Manager can be prevented from seeing hidden fields outside the course.
     */
    public function test_hidden_siteidentity_fields_schismatic_access(): void {
        $this->resetAfterTest();
        $env = $this->setup_hidden_siteidentity();
        $this->setUser($env->manager);

        // Revoke the capability to see hidden user fields outside the course.
        // Note that inside the course, the manager can still see the hidden identifiers as this is currently
        // controlled by a separate capability for legacy reasons. This is counter-intuitive behaviour and is
        // likely to be fixed in MDL-51630.
        assign_capability('moodle/user:viewhiddendetails', CAP_PREVENT, $env->managerrole->id, SYSCONTEXTID, true);

        $systemselector = new testable_user_selector('test');
        $courseselector = new testable_user_selector('test', ['accesscontext' => $env->coursecontext]);

        foreach ($systemselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectNotHasProperty('country', $user);
                $this->assertObjectNotHasProperty('city', $user);
            }
        }

        foreach ($courseselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectHasProperty('country', $user);
                $this->assertObjectHasProperty('city', $user);
            }
        }
    }

    /**
     * Two capabilities must be currently set to prevent manager from seeing hidden fields.
     */
    public function test_hidden_siteidentity_fields_hard_to_prevent_access(): void {
        $this->resetAfterTest();
        $env = $this->setup_hidden_siteidentity();
        $this->setUser($env->manager);

        assign_capability('moodle/user:viewhiddendetails', CAP_PREVENT, $env->managerrole->id, SYSCONTEXTID, true);
        assign_capability('moodle/course:viewhiddenuserfields', CAP_PREVENT, $env->managerrole->id, SYSCONTEXTID, true);

        $systemselector = new testable_user_selector('test');
        $courseselector = new testable_user_selector('test', ['accesscontext' => $env->coursecontext]);

        foreach ($systemselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectNotHasProperty('country', $user);
                $this->assertObjectNotHasProperty('city', $user);
            }
        }

        foreach ($courseselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectNotHasProperty('country', $user);
                $this->assertObjectNotHasProperty('city', $user);
            }
        }
    }

    /**
     * For legacy reasons, user selectors supported ability to override $CFG->showuseridentity.
     *
     * However, this was found as violating the principle of respecting site privacy settings. So the feature has been
     * dropped in Moodle 3.6.
     */
    public function test_hidden_siteidentity_fields_explicit_extrafields(): void {
        $this->resetAfterTest();
        $env = $this->setup_hidden_siteidentity();
        $this->setUser($env->manager);

        $implicitselector = new testable_user_selector('test');
        $explicitselector = new testable_user_selector('test', ['extrafields' => ['email', 'department']]);

        $this->assertDebuggingCalled();

        foreach ($implicitselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectHasProperty('country', $user);
                $this->assertObjectHasProperty('city', $user);
                $this->assertObjectNotHasProperty('email', $user);
                $this->assertObjectNotHasProperty('department', $user);
            }
        }

        foreach ($explicitselector->find_users('') as $found) {
            foreach ($found as $user) {
                $this->assertObjectHasProperty('idnumber', $user);
                $this->assertObjectHasProperty('country', $user);
                $this->assertObjectHasProperty('city', $user);
                $this->assertObjectNotHasProperty('email', $user);
                $this->assertObjectNotHasProperty('department', $user);
            }
        }
    }
}
