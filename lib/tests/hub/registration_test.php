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

namespace core\hub;

/**
 * Class containing unit tests for the site registration class.
 *
 * @package   core
 * @copyright  2023 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\hub\registration
 */
class registration_test extends \advanced_testcase {

    /**
     * Test getting site registration information.
     */
    public function test_get_site_info(): void {
        global $CFG;
        $this->resetAfterTest();

        // Create some courses with end dates.
        $generator = $this->getDataGenerator();
        $generator->create_course(['enddate' => time() + 1000]);
        $generator->create_course(['enddate' => time() + 1000]);

        $generator->create_course(); // Course with no end date.

        $siteinfo = registration::get_site_info();

        $this->assertNull($siteinfo['policyagreed']);
        $this->assertEquals($CFG->dbtype, $siteinfo['dbtype']);
        $this->assertEquals('manual', $siteinfo['primaryauthtype']);
        $this->assertEquals(1, $siteinfo['coursesnodates']);
    }

    /**
     * Test getting the plugin usage data.
     */
    public function test_get_plugin_usage(): void {
        global $DB;
        $this->resetAfterTest();

        // Create some courses with end dates.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create some assignments.
        $generator->create_module('assign', ['course' => $course->id]);
        $generator->create_module('assign', ['course' => $course->id]);
        $generator->create_module('assign', ['course' => $course->id]);

        // Create some quizzes.
        $generator->create_module('quiz', ['course' => $course->id]);
        $generator->create_module('quiz', ['course' => $course->id]);

        // Add some blocks.
        $generator->create_block('online_users');
        $generator->create_block('online_users');
        $generator->create_block('online_users');
        $generator->create_block('online_users');

        // Disabled a plugin.
        $DB->set_field('modules', 'visible', 0, ['name' => 'feedback']);

        // Check our plugin usage counts and enabled states are correct.
        $pluginusage = registration::get_plugin_usage_data();
        $this->assertEquals(3, $pluginusage['mod']['assign']['count']);
        $this->assertEquals(2, $pluginusage['mod']['quiz']['count']);
        $this->assertEquals(4, $pluginusage['block']['online_users']['count']);
        $this->assertEquals(0, $pluginusage['mod']['feedback']['enabled']);
        $this->assertEquals(1, $pluginusage['mod']['assign']['enabled']);
    }
}
