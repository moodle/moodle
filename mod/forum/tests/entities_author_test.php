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
 * The author entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\author as author_entity;

/**
 * The author entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_entities_author_testcase extends advanced_testcase {
    /**
     * Test the entity returns expected values.
     */
    public function test_entity() {
        $this->resetAfterTest();

        $author = new author_entity(
            1,
            2,
            'test',
            'person',
            'test person',
            'test@example.com',
            false,
            'middle',
            'tteeeeest',
            'ppppeeerssson',
            'maverick',
            'image alt'
        );

        $this->assertEquals(1, $author->get_id());
        $this->assertEquals(2, $author->get_picture_item_id());
        $this->assertEquals('test', $author->get_first_name());
        $this->assertEquals('person', $author->get_last_name());
        $this->assertEquals('test person', $author->get_full_name());
        $this->assertEquals('test@example.com', $author->get_email());
        $this->assertEquals(false, $author->is_deleted());
        $this->assertEquals('middle', $author->get_middle_name());
        $this->assertEquals('tteeeeest', $author->get_first_name_phonetic());
        $this->assertEquals('ppppeeerssson', $author->get_last_name_phonetic());
        $this->assertEquals('maverick', $author->get_alternate_name());
        $this->assertEquals('image alt', $author->get_image_alt());
    }
}
