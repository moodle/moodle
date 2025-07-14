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

namespace core_files\external;

use advanced_testcase;
use context_user;

/**
 * Unit tests for stored file exporter
 *
 * @package     core_files
 * @covers      \core_files\external\stored_file_exporter
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class stored_file_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE, $USER, $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $contextuser = context_user::instance($USER->id);

        $file = get_file_storage()->create_file_from_string([
            'contextid' => $contextuser->id,
            'userid' => $USER->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => 'Hi.txt',
        ], 'Hello');

        $exporter = new stored_file_exporter($file, ['context' => $contextuser]);
        $export = $exporter->export($PAGE->get_renderer('core'));

        $this->assertEquals((object) [
            'contextid' => $file->get_contextid(),
            'component' => $file->get_component(),
            'filearea' => $file->get_filearea(),
            'itemid' => $file->get_itemid(),
            'filepath' => $file->get_filepath(),
            'filename' => $file->get_filename(),
            'isdir' => false,
            'isimage' => false,
            'timemodified' => $file->get_timemodified(),
            'timecreated' => $file->get_timecreated(),
            'filesize' => $file->get_filesize(),
            'author' => $file->get_author(),
            'license' => $file->get_license(),
            'filenameshort' => $file->get_filename(),
            'filesizeformatted' => display_size($file->get_filesize()),
            'icon' => 'f/text',
            'timecreatedformatted' => userdate($file->get_timecreated()),
            'timemodifiedformatted' => userdate($file->get_timemodified()),
            'url' => "{$CFG->wwwroot}/pluginfile.php/{$contextuser->id}/user/draft/{$file->get_itemid()}/Hi.txt?forcedownload=1",
        ], $export);
    }

    /**
     * Data provider for {@see test_export_filenameshort}
     *
     * @return array[]
     */
    public static function export_filenameshort_provider(): array {
        return [
            // Long filenames (30 characters), with extensions of varying length.
            ['Lorem ipsum dolor sit amet sit.c', 'Lorem ipsum dolor sit...c'],
            ['Lorem ipsum dolor sit amet sit.txt', 'Lorem ipsum dolor s...txt'],
            ['Lorem ipsum dolor sit amet sit.docx', 'Lorem ipsum dolor ...docx'],
            // Multi-byte filenames.
            ['Мазитов А.З. практика тусур.py', 'Мазитов А.З. практик...py'],
        ];
    }

    /**
     * Test exporting shortened filename
     *
     * @param string $filename
     * @param string $expected
     *
     * @dataProvider export_filenameshort_provider
     */
    public function test_export_filenameshort(string $filename, string $expected): void {
        global $PAGE, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $contextuser = context_user::instance($USER->id);

        $file = get_file_storage()->create_file_from_string([
            'contextid' => $contextuser->id,
            'userid' => $USER->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => $filename,
        ], 'Hello');

        $exporter = new stored_file_exporter($file, ['context' => $contextuser]);
        $export = $exporter->export($PAGE->get_renderer('core'));

        $this->assertEquals($expected, $export->filenameshort);
    }
}
