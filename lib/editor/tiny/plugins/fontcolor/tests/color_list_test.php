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

namespace tiny_fontcolor;

/**
 * Unit tests for the color list class of the fontcolor plugin.
 *
 * @package    tiny_fontcolor
 * @category   test
 * @copyright  2015 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class color_list_test extends \advanced_testcase {

    /**
     * @var color_list
     */
    private $list;

    /**
     * Set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->list = new color_list([
            ['name' => 'Black', 'value' => '#000000'],
            ['name' => 'White', 'value' => '#FFFFFF'],
        ]);
    }

    /**
     * Test adding a color to the list.
     * @covers \tiny_fontcolor\color_list::add_color()
     * @covers \tiny_fontcolor\color_list::length()
     */
    public function test_add_color(): void {
        $this->assertEquals(2, $this->list->length());
        $this->list->add_color('Gray', 'cccccc');
        $this->assertEquals(3, $this->list->length());
    }

    /**
     * Test the json encoding.
     * @covers \tiny_fontcolor\color_list::to_json()
     */
    public function test_to_json(): void {

        $expected = json_encode([
            ['name' => 'Black', 'value' => '#000000'],
            ['name' => 'White', 'value' => '#FFFFFF'],
        ]);
        $this->assertEquals($expected, $this->list->to_json());
    }

    /**
     * Test the css class representation.
     * @covers \tiny_fontcolor\color_list::get_css_class_list()
     */
    public function test_get_css_class_list(): void {
        $this->list->add_color('Summer of 69', 'ff34ed');

        $csslist = $this->list->get_css_class_list();
        $this->assertEquals([
            'black' => '#000000',
            'white' => '#FFFFFF',
            'summerof' => '#FF34ED',
        ], $csslist);

        $csslist = $this->list->get_css_class_list('foo-');
        $this->assertArrayHasKey('foo-black', $csslist);
        $this->assertArrayHasKey('foo-summerof', $csslist);

        $this->list->add_color('black', '#000099');
        $csslist = $this->list->get_css_class_list();
        $this->assertArrayHasKey('black', $csslist);
        $this->assertArrayHasKey('black-4', $csslist);
    }

    /**
     * Test the color css string.
     * @covers \tiny_fontcolor\color_list::get_css_string()
     */
    public function test_get_css_string(): void {
        $expected = ".tiny_fontcolor-backgroundcolors-black{background-color:#000000}\n"
            . ".tiny_fontcolor-backgroundcolors-white{background-color:#FFFFFF}\n";
        $this->assertEquals($expected, $this->list->get_css_string('backgroundcolors'));

        $expected = ".tiny_fontcolor-textcolors-black{color:#000000}\n"
            . ".tiny_fontcolor-textcolors-white{color:#FFFFFF}\n";
        $this->assertEquals($expected, $this->list->get_css_string('textcolors'));

    }
}
