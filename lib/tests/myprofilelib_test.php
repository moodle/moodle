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

namespace core;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/myprofilelib.php');

/**
 * Tests for myprofilelib apis.
 *
 * @package    core
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class myprofilelib_test extends \advanced_testcase {

    /**
     * @var stdClass The user.
     */
    private $user;

    /**
     * @var stdClass The course.
     */
    private $course;

    /**
     * @var \core_user\output\myprofile\tree The navigation tree.
     */
    private $tree;
    public function setUp(): void {
        // Set the $PAGE->url value so core_myprofile_navigation() doesn't complain.
        global $PAGE;
        $PAGE->set_url('/test');

        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $this->tree = new \core_user\output\myprofile\tree();
        $this->resetAfterTest();
    }

    /**
     * Tests the core_myprofile_navigation() function as an admin viewing a user's course profile.
     */
    public function test_core_myprofile_navigation_as_admin() {
        $this->setAdminUser();
        $iscurrentuser = false;

        // Test tree as admin user.
        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $categories = $reflector->getProperty('categories');
        $cats = $categories->getValue($this->tree);
        $this->assertArrayHasKey('contact', $cats);
        $this->assertArrayHasKey('coursedetails', $cats);
        $this->assertArrayHasKey('miscellaneous', $cats);
        $this->assertArrayHasKey('reports', $cats);
        $this->assertArrayHasKey('administration', $cats);
        $this->assertArrayHasKey('loginactivity', $cats);

        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('fullprofile', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_myprofile_navigation() function as a user without permission to view the full
     * profile of another another user.
     */
    public function test_core_myprofile_navigation_course_without_permission() {
        // User without permission.
        $this->setUser($this->getDataGenerator()->create_user());
        $iscurrentuser = false;

        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('fullprofile', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_myprofile_navigation() function as the currently logged in user.
     */
    public function test_core_myprofile_navigation_profile_link_as_current_user() {
        $this->setUser($this->user);
        $iscurrentuser = true;

        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('editprofile', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_myprofile_navigation() function as the admin viewing another user.
     */
    public function test_core_myprofile_navigation_profile_link_as_admin() {
        $this->setAdminUser();
        $iscurrentuser = false;

        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('editprofile', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_myprofile_navigation() function when viewing the preference page as an admin.
     */
    public function test_core_myprofile_navigation_preference_as_admin() {
        $this->setAdminUser();
        $iscurrentuser = false;

        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('preferences', $nodes->getValue($this->tree));
        $this->assertArrayHasKey('loginas', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_myprofile_navigation() function when viewing the preference
     * page as another user without the ability to use the 'loginas' functionality.
     */
    public function test_core_myprofile_navigation_preference_without_permission() {
        // Login as link for a user who doesn't have the capability to login as.
        $this->setUser($this->getDataGenerator()->create_user());
        $iscurrentuser = false;

        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('loginas', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_myprofile_navigation() function as an admin viewing another user's contact details.
     */
    public function test_core_myprofile_navigation_contact_fields_as_admin() {
        global $CFG;

        // User contact fields.
        set_config("hiddenuserfields", "country,city");
        set_config("showuseridentity", "email,address,phone1,phone2,institution,department,idnumber");
        $hiddenfields = explode(',', $CFG->hiddenuserfields);
        $identityfields = explode(',', $CFG->showuseridentity);
        $this->setAdminUser();
        $iscurrentuser = false;

        // Make sure fields are not empty.
        $fields = array(
            'country' => 'AU',
            'city' => 'Silent hill',
            'email' => 'Rulelikeaboss@example.com',
            'address' => 'Didn\'t I mention silent hill already ?',
            'phone1' => '123',
            'phone2' => '234',
            'institution' => 'strange land',
            'department' => 'video game/movie',
            'idnumber' => 'SLHL'
        );
        foreach ($fields as $field => $value) {
            $this->user->$field = $value;
        }

        // User with proper permissions.
        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, null);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        foreach ($hiddenfields as $field) {
            $this->assertArrayHasKey($field, $nodes->getValue($this->tree));
        }
        foreach ($identityfields as $field) {
            $this->assertArrayHasKey($field, $nodes->getValue($this->tree));
        }
    }

    /**
     * Tests the core_myprofile_navigation() function as a user viewing another user's profile
     * ensuring that the contact details are not shown.
     */
    public function test_core_myprofile_navigation_contact_field_without_permission() {
        global $CFG;

        $iscurrentuser = false;
        $hiddenfields = explode(',', $CFG->hiddenuserfields);
        $identityfields = explode(',', $CFG->showuseridentity);

        // User without permission.
        $this->setUser($this->getDataGenerator()->create_user());
        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, null);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        foreach ($hiddenfields as $field) {
            $this->assertArrayNotHasKey($field, $nodes->getValue($this->tree));
        }
        foreach ($identityfields as $field) {
            $this->assertArrayNotHasKey($field, $nodes->getValue($this->tree));
        }
    }

    /**
     * Data provider for {@see test_core_myprofile_navigation_contact_timezone}
     *
     * @return array[]
     */
    public function core_myprofile_navigation_contact_timezone_provider(): array {
        return [
            'Hidden field' => ['timezone', '99', '99', null],
            'Forced timezone' => ['', 'Europe/London', 'Pacific/Tahiti', 'Europe/London'],
            'User timezone (default)' => ['', '99', '99', 'Australia/Perth'],
            'User timezone (selected)' => ['', '99', 'Pacific/Tahiti', 'Pacific/Tahiti'],
        ];
    }

    /**
     * Test timezone node added to user profile navigation
     *
     * @param string $hiddenuserfields
     * @param string $forcetimezone Timezone identifier or '99' (User can choose their own)
     * @param string $usertimezone Timezone identifier or '99' (Use server default)
     * @param string|null $expectresult
     * @return bool
     *
     * @dataProvider core_myprofile_navigation_contact_timezone_provider
     */
    public function test_core_myprofile_navigation_contact_timezone(string $hiddenuserfields, string $forcetimezone,
            string $usertimezone, ?string $expectresult = null): void {

        set_config('hiddenuserfields', $hiddenuserfields);
        set_config('forcetimezone', $forcetimezone);

        // Set the timezone of our test user, and load their navigation tree.
        $this->user->timezone = $usertimezone;
        $this->setUser($this->user);

        core_myprofile_navigation($this->tree, $this->user, true, null);

        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');

        /** @var \core_user\output\myprofile\node[] $tree */
        $tree = $nodes->getValue($this->tree);
        if ($expectresult !== null) {
            $this->assertArrayHasKey('timezone', $tree);
            $this->assertEquals($expectresult, $tree['timezone']->content);
        } else {
            $this->assertArrayNotHasKey('timezone', $tree);
        }
    }

    /**
     * Tests the core_myprofile_navigation() function as an admin viewing another user's
     * profile ensuring the login activity links are shown.
     */
    public function test_core_myprofile_navigation_login_activity() {
        // First access, last access, last ip.
        $this->setAdminUser();
        $iscurrentuser = false;

        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, null);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('firstaccess', $nodes->getValue($this->tree));
        $this->assertArrayHasKey('lastaccess', $nodes->getValue($this->tree));
        $this->assertArrayHasKey('lastip', $nodes->getValue($this->tree));
    }

    /**
     * Tests the core_myprofile_navigation() function as a user viewing another user's profile
     * ensuring the login activity links are not shown.
     */
    public function test_core_myprofile_navigationn_login_activity_without_permission() {
        // User without permission.
        set_config("hiddenuserfields", "firstaccess,lastaccess,lastip");
        $this->setUser($this->getDataGenerator()->create_user());
        $iscurrentuser = false;

        core_myprofile_navigation($this->tree, $this->user, $iscurrentuser, null);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('firstaccess', $nodes->getValue($this->tree));
        $this->assertArrayNotHasKey('lastaccess', $nodes->getValue($this->tree));
        $this->assertArrayNotHasKey('lastip', $nodes->getValue($this->tree));
    }
}
