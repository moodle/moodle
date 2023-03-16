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

/**
 * Unit tests for {@see activity_packager}.
 *
 * @coversDefaultClass \core\moodlenet\activity_packager
 * @package core
 * @copyright 2023 Michael Hawkins <michaelh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_packager_test extends \advanced_testcase {

    /**
     * Test fetching and overriding a backup task setting.
     *
     * @covers ::override_task_setting
     * @covers ::get_all_task_settings
     * @return void
     */
    public function test_override_task_setting() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $assigndata = [
            'course' => $course->id,
            'name' => 'Extremely interesting assignment',
            'intro' => 'A great assignment to share',
        ];
        $assign = $generator->create_module('assign', $assigndata);
        $cminfo = get_fast_modinfo($course->id)->get_cm($assign->cmid);
        $packager = new activity_packager($cminfo, $USER->id);

        // Fetch all backup task settings.
        $rc = new \ReflectionClass(activity_packager::class);
        $rcmgetall = $rc->getMethod('get_all_task_settings');
        $rcmgetall->setAccessible(true);
        $tasksettings = $rcmgetall->invoke($packager);

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
        $rcmoverridesetting->setAccessible(true);
        $rcmoverridesetting->invoke($packager, $tasksettings, $testsettingname, $overridevalue);
        $tasksettings = $rcmgetall->invoke($packager);
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
    }

    /**
     * Test overriding a backup task setting.
     *
     * @covers ::get_package
     * @covers ::package
     * @return void
     */
    public function test_get_package() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $currenttime = time();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);

        $assigndata = [
            'course' => $course->id,
            'name' => 'Extremely interesting assignment',
            'intro' => 'A great assignment to share',
        ];
        $assign = $generator->create_module('assign', $assigndata);
        $cminfo = get_fast_modinfo($course->id)->get_cm($assign->cmid);
        $packager = new activity_packager($cminfo, $USER->id);
        $package = $packager->get_package();

        $this->assertEquals(2, count($package));

        // Confirm there are backup file contents returned.
        $this->assertTrue(array_key_exists('filecontents', $package));
        $this->assertNotEmpty($package['filecontents']);

        // Confirm the expected stored_file object is returned.
        $this->assertTrue(array_key_exists('storedfile', $package));
        $this->assertInstanceOf(\stored_file::class, $package['storedfile']);

        // Check some known values in the returned stored_file object to confirm they match the file we have packaged.
        $this->assertNotEmpty($package['storedfile']->get_contenthash());
        $this->assertEquals($coursecontext->id, $package['storedfile']->get_contextid());
        $this->assertEquals('core', $package['storedfile']->get_component());
        $this->assertEquals('moodlenet_resource', $package['storedfile']->get_filearea());
        $this->assertEquals('assign_backup.mbz', $package['storedfile']->get_filename());
        $this->assertGreaterThan(0, $package['storedfile']->get_filesize());
        $timecreated = $package['storedfile']->get_timecreated();
        $this->assertGreaterThanOrEqual($currenttime, $timecreated);
        $this->assertEquals($timecreated, $package['storedfile']->get_timemodified());
    }
}
