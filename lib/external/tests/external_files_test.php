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

namespace core_external;

/**
 * Unit tests for core_external\external_files.
 *
 * @package     core_external
 * @category    test
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @covers      \core_external\external_files
 */
class external_files_test extends \advanced_testcase {
    /**
     * Text external files structure.
     *
     * @covers \core_external\external_files
     */
    public function test_files_structure(): void {
        $description = new external_files();

        // First check that the expected default values and keys are returned.
        $expectedkeys = array_flip([
            'filename', 'filepath', 'filesize', 'fileurl', 'timemodified', 'mimetype',
            'isexternalfile', 'repositorytype',
        ]);
        $returnedkeys = array_flip(array_keys($description->content->keys));
        $this->assertEquals($expectedkeys, $returnedkeys);
        $this->assertEquals('List of files.', $description->desc);
        $this->assertEquals(VALUE_REQUIRED, $description->required);
        foreach ($description->content->keys as $key) {
            $this->assertEquals(VALUE_OPTIONAL, $key->required);
        }

    }
}
