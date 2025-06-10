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

/**
 * Contains tests for template's page operations.
 *
 * @package   mod_customcert
 * @copyright 2023 Leon Stringer <leon.stringer@ntlworld.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

/**
 * Contains tests for template's page operations.
 *
 * @package   mod_customcert
 * @copyright 2023 Leon Stringer <leon.stringer@ntlworld.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_test extends \advanced_testcase {

    /**
     * Set the test up.
     *
     * @return void
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Check deleting a non-empty page has no errors.
     *   1. Create a template.
     *   2. Add a second page.
     *   3. Add an element to the second page.
     *   4. Delete the second page.
     * Previously the above scenario resulted in an error (#571).
     *
     * @covers \template::delete_page
     */
    public function test_delete_non_empty_page(): void {
        global $DB;

        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);

        // Add a second page and add an element to it.
        $page2id = $template->add_page();
        $element = new \stdClass();
        $element->pageid = $page2id;
        $element->name = 'Image';
        $element->element = 'image';
        $DB->insert_record('customcert_elements', $element);

        $template->delete_page($page2id);

        $records = $DB->count_records('customcert_elements', ['pageid' => $page2id]);
        $this->assertEquals(0, $records);

        $records = $DB->count_records('customcert_pages', ['id' => $page2id]);
        $this->assertEquals(0, $records);
    }
}
