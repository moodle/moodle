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

namespace core_backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Tests for backup_plan_dbops.
 *
 * @package    core_backup
 * @category   test
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \backup_plan_dbops::get_default_backup_filename
 */
final class backup_plan_dbops_test extends \advanced_testcase {
    /**
     * Test that get_default_backup_filename returns a valid filename within the OS 255-byte limit.
     *
     * @param string $shortname The course shortname.
     * @dataProvider get_default_backup_filename_provider
     */
    public function test_get_default_backup_filename(string $shortname): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['shortname' => $shortname]);

        $filename = \backup_plan_dbops::get_default_backup_filename(
            \backup::FORMAT_MOODLE,
            \backup::TYPE_1COURSE,
            $course->id,
            true,
            false,
        );

        $this->assertLessThanOrEqual(255, strlen($filename));
        $this->assertTrue(mb_check_encoding($filename, 'UTF-8'));
    }

    /**
     * Data provider for test_get_default_backup_filename.
     *
     * @return array
     */
    public static function get_default_backup_filename_provider(): array {
        // 255 ASCII chars = 255 bytes = maximum shortname length allowed by the DB column.
        $ascii255 = str_repeat('a', 255);

        // 255 Cyrillic chars = 510 bytes (each Cyrillic char is 2 bytes in UTF-8).
        $cyrillic255 = str_repeat('и', 255);

        // 255 CJK chars = 765 bytes (each CJK char is 3 bytes in UTF-8).
        $cjk255 = str_repeat('中', 255);

        // 1 ASCII byte + 254 Cyrillic = 255 chars = 509 bytes. The odd byte shifts the Cyrillic block
        // so a byte-based substr() splits mid-character, producing invalid UTF-8.
        $oddalignedcyrillic = 'a' . str_repeat('и', 254);

        return [
            'short ascii shortname'                  => ['CS101'],
            '255 ascii chars - max shortname length' => [$ascii255],
            '255 cyrillic chars'                     => [$cyrillic255],
            '255 cjk chars'                          => [$cjk255],
            'odd-aligned ascii+cyrillic'             => [$oddalignedcyrillic],
            'shortname with spaces'                  => ['My Course Name Here'],
        ];
    }

    /**
     * Test that a short ASCII shortname is preserved and not truncated.
     */
    public function test_get_default_backup_filename_preserves_short_name(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['shortname' => 'CS101']);

        $filename = \backup_plan_dbops::get_default_backup_filename(
            \backup::FORMAT_MOODLE,
            \backup::TYPE_1COURSE,
            $course->id,
            true,
            false,
        );

        $this->assertStringContainsString('cs101', strtolower($filename));
    }

    /**
     * Test that when useidonly is true, the shortname does not appear in the filename.
     */
    public function test_get_default_backup_filename_useidonly_excludes_shortname(): void {
        $this->resetAfterTest();

        $shortname = 'UniqueShortname123';
        $course = $this->getDataGenerator()->create_course(['shortname' => $shortname]);

        $filename = \backup_plan_dbops::get_default_backup_filename(
            \backup::FORMAT_MOODLE,
            \backup::TYPE_1COURSE,
            $course->id,
            true,
            false,
            true,
        );

        $this->assertStringNotContainsString(strtolower($shortname), $filename);
    }
}
