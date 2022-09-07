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
use MoodleQuickForm_editor;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/form/editor.php");

/**
 * Tests for the editor form element
 *
 * @package    core_form
 * @covers     \MoodleQuickForm_editor
 * @copyright  2026 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class editor_test extends advanced_testcase {
    /**
     * Test retrieving frozen HTML
     */
    public function test_get_frozen_html(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Ensure "URL" filter is active.
        filter_set_global_state('urltolink', TEXTFILTER_ON);

        $element = new MoodleQuickForm_editor('description_editor', 'Description');
        $element->setValue(['text' => 'http://example.com', 'format' => FORMAT_HTML]);

        $this->assertStringContainsString(
            '<a href="http://example.com" class="_blanktarget">http://example.com</a>',
            $element->getFrozenHtml(),
        );
    }
}
