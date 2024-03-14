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
 * Tests for report library functions.
 *
 * @package    report_outline
 * @copyright  2014 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace report_outline;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class report_outline_lib_testcase
 *
 * @package    report_outline
 * @copyright  2014 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class lib_test extends \advanced_testcase {

    /**
     * @var stdClass The user.
     */
    private $user;

    /**
     * @var stdClass The course.
     */
    private $course;

    /**
     * @var context_course Course context.
     */
    private $coursecontext;

    /**
     * @var \core_user\output\myprofile\tree The navigation tree.
     */
    private $tree;

    /**
     * @var int Dummy role for testing.
     */
    private $roleid;

    public function setUp(): void {
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $this->tree = new \core_user\output\myprofile\tree();
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        $this->resetAfterTest();
    }

    /**
     * Test report_log_supports_logstore.
     */
    public function test_report_participation_supports_logstore() {
        $logmanager = get_log_manager();
        $allstores = \core_component::get_plugin_list_with_class('logstore', 'log\store');

        $supportedstores = array(
            'logstore_standard' => '\logstore_standard\log\store'
        );

        // Make sure all supported stores are installed.
        $expectedstores = array_keys(array_intersect($allstores, $supportedstores));
        $stores = $logmanager->get_supported_logstores('report_outline');
        $stores = array_keys($stores);
        foreach ($expectedstores as $expectedstore) {
            $this->assertContains($expectedstore, $stores);
        }
    }

    /**
     * Tests the report_outline_myprofile_navigation() function as an admin user.
     */
    public function test_report_outline_myprofile_navigation() {
        $this->setAdminUser();
        $iscurrentuser = false;

        report_outline_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('outline', $nodes->getValue($this->tree));
        $this->assertArrayHasKey('complete', $nodes->getValue($this->tree));
    }

    /**
     * Tests the report_outline_myprofile_navigation() function as a user without permission.
     */
    public function test_report_outline_myprofile_navigation_without_permission() {
        $this->setUser($this->user);
        $iscurrentuser = true;

        report_outline_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('outline', $nodes->getValue($this->tree));
        $this->assertArrayNotHasKey('complete', $nodes->getValue($this->tree));
    }

    /**
     * Test that the current user can not access user report without report/outline:viewuserreport permission.
     */
    public function test_report_outline_can_not_access_user_report_without_viewuserreport_permission() {
        $this->getDataGenerator()->role_assign($this->roleid, $this->user->id, $this->coursecontext->id);
        $this->setUser($this->user);

        $this->assertFalse(report_outline_can_access_user_report($this->user, $this->course));
    }

    /**
     * Test that the current user can access user report with report/outline:viewuserreport permission.
     */
    public function test_report_outline_can_access_user_report_with_viewuserreport_permission() {
        assign_capability('report/outline:viewuserreport', CAP_ALLOW, $this->roleid, $this->coursecontext->id, true);
        $this->getDataGenerator()->role_assign($this->roleid, $this->user->id, $this->coursecontext->id);
        $this->setUser($this->user);

        $this->assertTrue(report_outline_can_access_user_report($this->user, $this->course));
    }
}
