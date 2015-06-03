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
 * Tests for myprofilelib apis.
 *
 * @package    core
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/tests/fixtures/myprofile_fixtures.php');
require_once($CFG->dirroot . '/lib/myprofilelib.php');

/**
 * Tests for myprofilelib apis.
 *
 * @package    core
 * @copyright  2015 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class core_myprofilelib_testcase extends advanced_testcase {

    /**
     * Tests for report_log_myprofile_navigation() api.
     */
    public function test_core_myprofile_navigation() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $tree = new phpunit_fixture_myprofile_tree();
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $iscurrentuser = false;

        // Test tree as admin user.
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $cats = $tree->get_categories();
        $this->assertArrayHasKey('contact', $cats);
        $this->assertArrayHasKey('coursedetails', $cats);
        $this->assertArrayHasKey('miscellaneous', $cats);
        $this->assertArrayHasKey('reports', $cats);
        $this->assertArrayHasKey('administration', $cats);
        $this->assertArrayHasKey('loginactivity', $cats);

        // Course node.
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('fullprofile', $nodes);

        // User without permission cannot access full course profile.
        $this->setUser($user2);
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('fullprofile', $nodes);

        // Edit profile link.
        $this->setUser($user);
        $iscurrentuser = true;
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('editprofile', $nodes);

        // Edit profile link as admin user.
        $this->setAdminUser();
        $iscurrentuser = false;
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('editprofile', $nodes);

        // Preference page.
        $this->setAdminUser();
        $iscurrentuser = false;
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('preferences', $nodes);

        // Login as.
        $this->setAdminUser();
        $iscurrentuser = false;
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('loginas', $nodes);

        // Login as link for a user who doesn't have the cap.
        $this->setUser($user2);
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('loginas', $nodes);

        // User contact fields.
        set_config("hiddenuserfields", "country,city,webpage,icqnumber,skypeid,yahooid,aimid,msnid");
        set_config("showuseridentity", "email,address,phone1,phone2,institution,department,idnumber");
        $hiddenfields = explode(',', $CFG->hiddenuserfields);
        $identityfields = explode(',', $CFG->showuseridentity);
        $this->setAdminUser();
        $iscurrentuser = false;

        // Make sure fields are not empty.
        $fields = array(
            'country' => 'AU',
            'city' => 'Silent hill',
            'url' => 'Ghosts',
            'icq' => 'Wth is ICQ?',
            'skype' => 'derp',
            'yahoo' => 'are you living in the 90\'s?',
            'aim' => 'are you for real?',
            'msn' => '...',
            'email' => 'Rulelikeaboss@example.com',
            'address' => 'Didn\'t I mention silent hill already ?',
            'phone1' => '123',
            'phone2' => '234',
            'institution' => 'strange land',
            'department' => 'video game/movie',
            'idnumber' => 'SLHL'
        );
        foreach ($fields as $field => $value) {
            $user->$field = $value;
        }

        // User with proper permissions.
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, null);
        $nodes = $tree->get_nodes();
        foreach ($hiddenfields as $field) {
            $this->assertArrayHasKey($field, $nodes);
        }
        foreach ($identityfields as $field) {
            $this->assertArrayHasKey($field, $nodes);
        }

        // User without permission.
        $this->setUser($user2);
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, null);
        $nodes = $tree->get_nodes();
        foreach ($hiddenfields as $field) {
            $this->assertArrayNotHasKey($field, $nodes);
        }
        foreach ($identityfields as $field) {
            $this->assertArrayNotHasKey($field, $nodes);
        }

        // First access, last access, last ip.
        $this->setAdminUser();
        $iscurrentuser = false;
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, null);
        $nodes = $tree->get_nodes();
        $this->assertArrayHasKey('firstaccess', $nodes);
        $this->assertArrayHasKey('lastaccess', $nodes);
        $this->assertArrayHasKey('lastip', $nodes);

        // User without permission.
        set_config("hiddenuserfields", "firstaccess,lastaccess,lastip");
        $this->setUser($user2);
        $iscurrentuser = false;
        $tree = new phpunit_fixture_myprofile_tree();
        core_myprofile_navigation($tree, $user, $iscurrentuser, null);
        $nodes = $tree->get_nodes();
        $this->assertArrayNotHasKey('firstaccess', $nodes);
        $this->assertArrayNotHasKey('lastaccess', $nodes);
        $this->assertArrayNotHasKey('lastip', $nodes);
    }
}
