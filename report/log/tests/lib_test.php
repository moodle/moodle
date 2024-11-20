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
 * @package    report_log
 * @copyright  2014 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace report_log;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class report_log_events_testcase.
 *
 * @package    report_log
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
     * @var \core_user\output\myprofile\tree The navigation tree.
     */
    private $tree;

    public function setUp(): void {
        parent::setUp();
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $this->tree = new \core_user\output\myprofile\tree();
        $this->resetAfterTest();
    }

    /**
     * Test report_log_supports_logstore.
     */
    public function test_report_log_supports_logstore(): void {
        $logmanager = get_log_manager();
        $allstores = \core_component::get_plugin_list_with_class('logstore', 'log\store');

        $supportedstores = array(
            'logstore_database' => '\logstore_database\log\store',
            'logstore_standard' => '\logstore_standard\log\store'
        );

        // Make sure all supported stores are installed.
        $expectedstores = array_keys(array_intersect($allstores, $supportedstores));
        $stores = $logmanager->get_supported_logstores('report_log');
        $stores = array_keys($stores);
        foreach ($expectedstores as $expectedstore) {
            $this->assertContains($expectedstore, $stores);
        }
    }

    /**
     * Tests the report_log_myprofile_navigation() function as an admin viewing the logs for a user.
     */
    public function test_report_log_myprofile_navigation(): void {
        // Set as the admin.
        $this->setAdminUser();
        $iscurrentuser = false;

        // Check the node tree is correct.
        report_log_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayHasKey('alllogs', $nodes->getValue($this->tree));
        $this->assertArrayHasKey('todayslogs', $nodes->getValue($this->tree));
    }

    /**
     * Tests the report_log_myprofile_navigation() function as a user without permission.
     */
    public function test_report_log_myprofile_navigation_without_permission(): void {
        // Set to the other user.
        $this->setUser($this->user);
        $iscurrentuser = true;

        // Check the node tree is correct.
        report_log_myprofile_navigation($this->tree, $this->user, $iscurrentuser, $this->course);
        $reflector = new \ReflectionObject($this->tree);
        $nodes = $reflector->getProperty('nodes');
        $this->assertArrayNotHasKey('alllogs', $nodes->getValue($this->tree));
        $this->assertArrayNotHasKey('todayslogs', $nodes->getValue($this->tree));
    }
}
