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

namespace core_adminpresets\local\setting;

/**
 * Tests for the adminpresets_admin_setting_configexecutable class.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_adminpresets\local\setting\adminpresets_admin_setting_configexecutable
 */
final class adminpresets_admin_setting_configexecutable_test extends \advanced_testcase {
    /**
     * Test the behaviour of save_value() method.
     *
     * @covers ::save_value
     * @dataProvider save_value_provider
     *
     * @param bool $preventexecpath Whether to set $CFG->preventexecpath.
     * @param string $newpath Executable path value to save.
     * @param bool $expectedsaved Whether the value should be saved (true) or rejected (false).
     */
    public function test_save_value(bool $preventexecpath, string $newpath, bool $expectedsaved): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $pathtophp = '';
        $isdefined = false;
        if ($CFG->pathtophp != '') {
            $pathtophp = $CFG->pathtophp;
            $isdefined = true;
        }

        if ($preventexecpath) {
            $CFG->preventexecpath = true;
        } else {
            unset($CFG->preventexecpath);
        }

        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $setting = $generator->get_admin_preset_setting('systempaths', 'pathtophp');

        $result = $setting->save_value(false, $newpath);

        if ($expectedsaved) {
            $this->assertIsInt($result);
            $this->assertCount(1, $DB->get_records('config_log', ['id' => $result]));
            $configlog = $DB->get_record('config_log', ['id' => $result]);
            $this->assertEquals($newpath, $configlog->value);
            if (!$isdefined) {
                $this->assertEquals($newpath, get_config('core', 'pathtophp'));
            } else {
                $this->assertEquals($pathtophp, get_config('core', 'pathtophp'));
            }
        } else {
            $this->assertFalse($result);
            $this->assertEquals($pathtophp, get_config('core', 'pathtophp'));
        }
    }

    /**
     * Data provider for test_save_value().
     *
     * @return array
     */
    public static function save_value_provider(): array {
        global $CFG;
        return [
            'preventexecpath set: save_value returns false without writing' => [
                'preventexecpath' => true,
                'newpath'         => PHP_BINARY,
                'expectedsaved'   => false,
            ],
            'preventexecpath not set, non-existent path: save_value returns false' => [
                'preventexecpath' => false,
                'newpath'         => '/this/path/does/not/exist/phpbinary',
                'expectedsaved'   => false,
            ],
            'preventexecpath not set, valid executable: value is saved' => [
                'preventexecpath' => false,
                'newpath'         => PHP_BINARY,
                'expectedsaved'   => PHP_BINARY !== $CFG->pathtophp, // Only expect saved if the new path is different from existing.
            ],
        ];
    }

    /**
     * Test that save_value() returns false for a file that exists but is not executable.
     *
     * @covers ::save_value
     */
    public function test_save_value_non_executable_file(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $pathtophp = '';
        if ($CFG->pathtophp != '') {
            $pathtophp = $CFG->pathtophp;
        }

        unset($CFG->preventexecpath);

        // Create a temporary file and remove executable permission.
        $tempfile = tempnam(sys_get_temp_dir(), 'moodle_test_');
        $this->assertNotFalse($tempfile);
        chmod($tempfile, 0644);

        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $setting = $generator->get_admin_preset_setting('systempaths', 'pathtophp');

        try {
            $result = $setting->save_value(false, $tempfile);
        } finally {
            unlink($tempfile);
        }

        $this->assertFalse($result);
        $this->assertEquals($pathtophp, get_config('core', 'pathtophp'));
    }
}
