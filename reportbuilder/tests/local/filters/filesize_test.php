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

declare(strict_types=1);

namespace core_reportbuilder\local\filters;

use advanced_testcase;
use core\context\user;
use core_reportbuilder\local\report\filter;
use lang_string;

/**
 * Unit tests for filesize report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\filesize
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filesize_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array
     */
    public static function get_sql_filter_provider(): array {
        return [
            [filesize::ANY_VALUE, true],

            [filesize::LESS_THAN, false, 10, filesize::SIZE_UNIT_BYTE],
            [filesize::LESS_THAN, false, 10, filesize::SIZE_UNIT_KILOBYTE],
            [filesize::LESS_THAN, true, 10, filesize::SIZE_UNIT_MEGABYTE],
            [filesize::LESS_THAN, true, 10, filesize::SIZE_UNIT_GIGABYTE],

            [filesize::GREATER_THAN, true, 10, filesize::SIZE_UNIT_BYTE],
            [filesize::GREATER_THAN, true, 10, filesize::SIZE_UNIT_KILOBYTE],
            [filesize::GREATER_THAN, false, 10, filesize::SIZE_UNIT_MEGABYTE],
            [filesize::GREATER_THAN, false, 10, filesize::SIZE_UNIT_GIGABYTE],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param bool $expected
     * @param float $value
     * @param int $unit
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(
        int $operator,
        bool $expected,
        float $value = 1,
        int $unit = filesize::SIZE_UNIT_BYTE,
    ): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a sample 2MB file.
        $file = get_file_storage()->create_file_from_string([
            'contextid' => user::instance($USER->id)->id,
            'userid' => $USER->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => 'Hello.txt',
        ], str_repeat('A', 2 * filesize::SIZE_UNIT_MEGABYTE));

        $filter = new filter(
            filesize::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'filesize'
        );

        // Create instance of our filter, passing given values.
        [$select, $params] = filesize::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value1' => $value,
            $filter->get_unique_identifier() . '_unit' => $unit,
        ]);

        $fileids = $DB->get_fieldset_select('files', 'id', $select, $params);
        if ($expected) {
            $this->assertContains((string) $file->get_id(), $fileids);
        } else {
            $this->assertNotContains((string) $file->get_id(), $fileids);
        }
    }
}
