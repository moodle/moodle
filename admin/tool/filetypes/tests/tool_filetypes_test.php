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

use tool_filetypes\utils;

/**
 * Unit tests for the custom file types.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_filetypes\utils
 */
class tool_filetypes_test extends advanced_testcase {
    /**
     * Tests is_extension_invalid() function.
     *
     * @covers ::is_extension_invalid
     */
    public function test_is_extension_invalid(): void {
        // The pdf file extension already exists in default moodle minetypes.
        $this->assertTrue(utils::is_extension_invalid('pdf'));

        // The frog extension does not.
        $this->assertFalse(utils::is_extension_invalid('frog'));

        // However you could use the pdf extension when editing the pdf extension.
        $this->assertFalse(utils::is_extension_invalid('pdf', 'pdf'));

        // Blank extension is invalid.
        $this->assertTrue(utils::is_extension_invalid(''));

        // Extensions with dot are invalid.
        $this->assertTrue(utils::is_extension_invalid('.frog'));
    }

    /**
     * Tests is_defaulticon_allowed() function.
     *
     * @covers ::is_defaulticon_allowed
     */
    public function test_is_defaulticon_allowed(): void {
        // You ARE allowed to set a default icon for a MIME type that hasn't
        // been used yet.
        $this->assertTrue(utils::is_defaulticon_allowed('application/x-frog'));

        // You AREN'T allowed to set default icon for text/plain as there is
        // already a type that has that set.
        $this->assertFalse(utils::is_defaulticon_allowed('text/plain'));

        // But you ARE still allowed to set it when actually editing txt, which
        // is the default.
        $this->assertTrue(utils::is_defaulticon_allowed('text/plain', 'txt'));
    }

    /**
     * Tests get_icons_from_path() function.
     *
     * @covers ::get_icons_from_path
     */
    public function test_get_icons_from_path(): void {
        // Get icons from the fixtures folder.
        $icons = utils::get_icons_from_path(__DIR__ . '/fixtures');

        // The icons are returned alphabetically and with keys === values.
        // For the icon with numbers after the name, only the base name is
        // returned and only one of it.
        $this->assertEquals(array('frog' => 'frog', 'zombie' => 'zombie'), $icons);
    }

    /**
     * Test get_file_icons() function to confirm no file icons are removed by mistake.
     *
     * @covers ::get_file_icons
     */
    public function test_get_file_icons(): void {
        $icons = utils::get_file_icons();
        $filetypes = core_filetypes::get_types();

        $requiredicons = array_column($filetypes, 'icon');
        $requireduniqueicons = array_unique($requiredicons);

        // The 'folder' icon is not a file, however the test validates no
        // file icons are removed by mistake from the directory pix/f.
        // Adding the folder icon manually completes the scope of this test.
        $requireduniqueicons[] = 'folder';

        foreach ($requireduniqueicons as $requiredicon) {
            $this->assertArrayHasKey($requiredicon, $icons, "Icon '$requiredicon' is missing.");
        }
    }
}
