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

namespace mod_wiki\backup;

/**
 * Unit tests for wiki restoration process
 *
 * @package   mod_wiki
 * @copyright 2024 Laurent David <laurent.david@moodle.com>
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class restore_format_test extends \advanced_testcase {

    /**
     * Data provider for test_duplicating_wiki_removes_unwanted_formats.
     *
     * @return array[]
     */
    public static function restore_format_test_provider(): array {
        return [
            'creole' => [
                'format' => 'creole',
                'expected' => 'creole',
            ],
            'html' => [
                'format' => 'html',
                'expected' => 'html',
            ],
            'wikimarkup' => [
                'format' => 'nwiki',
                'expected' => 'nwiki',
            ],
            'wrong format' => [
                'format' => '../wrongformat123',
                'expected' => 'wrongformat',
            ],
        ];
    }

    /**
     * Test that duplicating a wiki removes unwanted / invalid format.
     *
     * @param string $format The format of the wiki.
     * @param string $expected The expected format of the wiki after duplication.
     *
     * @covers       \restore_wiki_activity_structure_step
     * @dataProvider restore_format_test_provider
     */
    public function test_duplicating_wiki_removes_unwanted_formats(string $format, string $expected): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Make a test course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $wiki = $generator->create_module('wiki', array_merge(['course' => $course->id, 'defaultformat' => $format]));
        // Duplicate the wiki.
        $newwikicm = duplicate_module($course, get_fast_modinfo($course)->get_cm($wiki->cmid));
        // Verify the settings of the duplicated activity.
        $newwiki = $DB->get_record('wiki', ['id' => $newwikicm->instance]);
        $this->assertEquals($expected, $newwiki->defaultformat);
    }
}
