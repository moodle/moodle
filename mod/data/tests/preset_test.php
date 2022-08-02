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

namespace mod_data;

/**
 * Preset tests class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2022 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\preset
 */
class preset_test extends \advanced_testcase {

    /**
     * Test for is_directory_a_preset().
     *
     * @dataProvider is_directory_a_preset_provider
     * @covers ::is_directory_a_preset
     * @param string $directory
     * @param bool $expected
     */
    public function test_is_directory_a_preset(string $directory, bool $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $result = preset::is_directory_a_preset($directory);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_is_directory_a_preset().
     *
     * @return array
     */
    public function is_directory_a_preset_provider(): array {
        global $CFG;

        return [
            'Valid preset directory' => [
                'directory' => $CFG->dirroot . '/mod/data/preset/imagegallery',
                'expected' => true,
            ],
            'Invalid preset directory' => [
                'directory' => $CFG->dirroot . '/mod/data/field/checkbox',
                'expected' => false,
            ],
            'Unexisting preset directory' => [
                'directory' => $CFG->dirroot . 'unexistingdirectory',
                'expected' => false,
            ],
        ];
    }

    /**
     * Test for get_name_from_plugin().
     *
     * @covers ::get_name_from_plugin
     */
    public function test_get_name_from_plugin() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // The expected name for plugins with modulename in lang is this value.
        $name = preset::get_name_from_plugin('imagegallery');
        $this->assertEquals('Image gallery', $name);

        // However, if the plugin doesn't exist or the modulename is not defined, the preset shortname will be returned.
        $presetshortname = 'nonexistingpreset';
        $name = preset::get_name_from_plugin($presetshortname);
        $this->assertEquals($presetshortname, $name);
    }

}
