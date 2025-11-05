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

namespace mod_board\phpunit\local;

use mod_board\board;
use mod_board\local\template;
use mod_board\local\install;

/**
 * Tests for Board
 *
 * @package    mod_board
 * @category   test
 * @copyright  2025 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\local\install
 */
final class install_test extends \advanced_testcase {
    public function test_template_exists(): void {
        $this->resetAfterTest();

        /** @var \mod_board_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_board');

        $template = $generator->create_template([
            'name' => 'My template',
            'description' => 'Fancy <em>template</em>',
            'columns' => "Col 1\r\nCol2",
            'intro' => 'Some fancy <em>text</em>',
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'sortby' => 999999,
        ]);

        $exists = install::template_exists($template);
        $this->assertTrue($exists);

        $template2 = clone $template;
        $this->assertTrue(install::template_exists($template2));
        $template2->name = 'Different name';
        $this->assertFalse(install::template_exists($template2));

        $template3 = clone $template;
        $this->assertTrue(install::template_exists($template3));
        $template3->description = 'Different description';
        $this->assertFalse(install::template_exists($template3));

        $template4 = clone $template;
        $this->assertTrue(install::template_exists($template4));
        $template4->columns = "Col 1\r\nCol2\r\nCol3";
        $this->assertFalse(install::template_exists($template4));
    }

    public function test_builtin_templates(): void {
        $templates = install::get_builtin_templates();
        $this->assertTrue(is_array($templates));
        $this->assertNotEmpty($templates);
        foreach ($templates as $template) {
            $this->assertTrue(is_object($template));
            $this->assertObjectHasProperty('name', $template);
            $this->assertObjectHasProperty('description', $template);
            $this->assertObjectHasProperty('columns', $template);
        }
    }

    public function test_setup_builtin_templates(): void {
        global $DB;
        $this->resetAfterTest();

        $DB->delete_records('board_templates');

        $startingcount = $DB->count_records('board_templates');
        $this->assertSame(0, $startingcount);

        // Running setup creates built-in templates.
        install::setup_builtin_templates();
        $postsetupcount = $DB->count_records('board_templates');
        $this->assertGreaterThan($startingcount, $postsetupcount);
        $this->assertEquals(12, $postsetupcount);

        // Running it again shouldn't re-add everything.
        install::setup_builtin_templates();
        $postsetupcount2 = $DB->count_records('board_templates');
        $this->assertSame($postsetupcount, $postsetupcount2);
    }
}
