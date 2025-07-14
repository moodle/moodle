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

namespace core_course;

defined('MOODLE_INTERNAL') || die();

use core_course\local\entity\content_item;
use core_course\local\entity\lang_string_title;
use core_course\local\entity\string_title;

/**
 * Tests for the \core_course\local\entity\content_item class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_course\local\entity\content_item
 */
final class content_item_test extends \advanced_testcase {

    /**
     * Test the content_item class.
     */
    public function test_content_item(): void {
        $this->resetAfterTest();

        $contentitem = new content_item(22, 'Item name', new lang_string_title('modulename', 'mod_assign'),
            new \moodle_url('mod_edit.php'), '<img src="test">', 'Description of the module', MOD_ARCHETYPE_RESOURCE, 'mod_page',
                MOD_PURPOSE_CONTENT, true);

        $this->assertEquals(22, $contentitem->get_id());
        $this->assertEquals('Item name', $contentitem->get_name());
        $this->assertEquals('Assignment', $contentitem->get_title()->get_value());
        $this->assertEquals(new \moodle_url('mod_edit.php'), $contentitem->get_link());
        $this->assertEquals('<img src="test">', $contentitem->get_icon());
        $this->assertEquals('Description of the module', $contentitem->get_help());
        $this->assertEquals(MOD_ARCHETYPE_RESOURCE, $contentitem->get_archetype());
        $this->assertEquals('mod_page', $contentitem->get_component_name());
        $this->assertEquals('content', $contentitem->get_purpose());
        $this->assertTrue($contentitem->is_branded());
    }

    /**
     * Test confirming that plugins can return custom titles for a content item.
     */
    public function test_content_item_custom_string_title(): void {
        $this->resetAfterTest();

        $contentitem = new content_item(22, 'Item name', new string_title('My custom string'),
            new \moodle_url('mod_edit.php'), '<img src="test">', 'Description of the module', MOD_ARCHETYPE_RESOURCE, 'mod_page',
                MOD_PURPOSE_CONTENT);

        $this->assertEquals('My custom string', $contentitem->get_title()->get_value());
    }
}
