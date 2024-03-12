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

namespace core\moodlenet;

use core\context\user;

/**
 * Test coverage for moodlenet course packager.
 *
 * @package   core
 * @copyright 2023 Safat Shahin <safat.shahin@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\moodlenet\course_packager
 */
class course_packager_test extends \advanced_testcase {

    /**
     * Test fetching and overriding a backup task setting.
     *
     * @covers ::override_task_setting
     * @covers ::get_all_task_settings
     * @covers ::get_backup_controller
     */
    public function test_override_task_setting(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Load the course packager.
        $packager = new course_packager($course, $USER->id);

        // Fetch all backup task settings.
        $rc = new \ReflectionClass(course_packager::class);
        $rcmgetbackup = $rc->getMethod('get_backup_controller');
        $controller = $rcmgetbackup->invoke($packager);
        $rcmgetall = $rc->getMethod('get_all_task_settings');
        $tasksettings = $rcmgetall->invoke($packager, $controller);

        // Fetch the default settings and grab an example value (setting_root_users).
        $rootsettings = $tasksettings[\backup_root_task::class];
        $testsettingname = 'setting_root_users';

        $oldvalue = 99;
        foreach ($rootsettings as $setting) {
            $name = $setting->get_ui_name();
            if ($name == $testsettingname) {
                $oldvalue = $setting->get_value();
                break;
            }
        }

        // Check we found the setting value (either 0 or 1 are valid).
        $this->assertNotEquals(99, $oldvalue);
        $this->assertLessThanOrEqual(1, $oldvalue);

        // Override the setting_root_users value, then re-fetch the settings to check the change is reflected.
        $overridevalue = ($oldvalue == 1) ? 0 : 1;
        $rcmoverridesetting = $rc->getMethod('override_task_setting');
        $rcmoverridesetting->invoke($packager, $tasksettings, $testsettingname, $overridevalue);
        $tasksettings = $rcmgetall->invoke($packager, $controller);
        $rootsettings = $tasksettings[\backup_root_task::class];

        $newvalue = 99;
        foreach ($rootsettings as $setting) {
            $name = $setting->get_ui_name();
            if ($name == $testsettingname) {
                $newvalue = $setting->get_value();
                break;
            }
        }

        $this->assertEquals($overridevalue, $newvalue);

        // We have finished with the backup controller, so destroy it.
        $controller->destroy();
    }

    /**
     * Test the course package file.
     *
     * @covers ::get_package
     * @covers ::package
     */
    public function test_get_package(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $currenttime = time();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Load the course packager.
        $packager = new course_packager($course, $USER->id);
        $package = $packager->get_package();

        // Confirm the expected stored_file object is returned.
        $this->assertInstanceOf(\stored_file::class, $package);

        // Check some known values in the returned stored_file object to confirm they match the file we have packaged.
        $this->assertNotEmpty($package->get_contenthash());
        $this->assertEquals(user::instance($USER->id)->id, $package->get_contextid());
        $this->assertEquals('user', $package->get_component());
        $this->assertEquals('draft', $package->get_filearea());
        $this->assertEquals($course->shortname . '_backup.mbz', $package->get_filename());
        $this->assertGreaterThan(0, $package->get_filesize());
        $timecreated = $package->get_timecreated();
        $this->assertGreaterThanOrEqual($currenttime, $timecreated);
        $this->assertEquals($timecreated, $package->get_timemodified());
    }
}
