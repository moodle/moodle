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

namespace core_form;

use advanced_testcase;
use core\context\user;
use core\output\html_writer;
use core\url;
use MoodleQuickForm_filemanager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/form/filemanager.php");

/**
 * Tests for the filemanager form element
 *
 * @package    core_form
 * @covers     \MoodleQuickForm_filemanager
 * @copyright  2026 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class filemanager_test extends advanced_testcase {
    /**
     * Test retrieving frozen HTML
     */
    public function test_get_frozen_html(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $file = get_file_storage()->create_file_from_string([
            'contextid' => user::instance($USER->id)->id,
            'userid' => $USER->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => 'Hello.txt',
        ], 'Hello');

        $element = new MoodleQuickForm_filemanager('file', 'File');
        $element->setValue($file->get_itemid());

        $draftfileurl = url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename(), true);

        $this->assertStringContainsString(
            html_writer::link($draftfileurl, $file->get_filename()),
            $element->getFrozenHtml(),
        );
    }
}
