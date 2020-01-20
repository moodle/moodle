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
 * Contains tests for the \core_course\local\entity\content_item class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tests\core_course;

defined('MOODLE_INTERNAL') || die();

use core_course\local\entity\content_item;

/**
 * Tests for the \core_course\local\entity\content_item class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_testcase extends \advanced_testcase {

    /**
     * Test the content_item class.
     */
    public function test_content_item() {
        $this->resetAfterTest();

        $contentitem = new content_item(22, 'Item name', 'Item title', new \moodle_url('mod_edit.php'), '<img src="test">',
            'Description of the module', MOD_ARCHETYPE_RESOURCE, 'mod_page');

        $this->assertEquals(22, $contentitem->get_id());
        $this->assertEquals('Item name', $contentitem->get_name());
        $this->assertEquals('Item title', $contentitem->get_title());
        $this->assertEquals(new \moodle_url('mod_edit.php'), $contentitem->get_link());
        $this->assertEquals('<img src="test">', $contentitem->get_icon());
        $this->assertEquals('Description of the module', $contentitem->get_help());
        $this->assertEquals(MOD_ARCHETYPE_RESOURCE, $contentitem->get_archetype());
        $this->assertEquals('mod_page', $contentitem->get_component_name());
    }
}
