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
 * Unit tests for the color class of the fontcolor plugin.
 *
 * @package    tiny_fontcolor
 * @category   test
 * @copyright  2015 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class color_test extends \advanced_testcase {

    /**
     * Set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test the getter for the name.
     * @covers \tiny_fontcolor\color::get_name()
     */
    public function test_get_name(): void {
        $color = new color('Black', '#000000');
        $this->assertEquals('Black', $color->get_name());

        $color = new color(' Black ', '#000000');
        $this->assertEquals('Black', $color->get_name());

        $color = new color('Black or White', '#000000');
        $this->assertEquals('Black or White', $color->get_name());
    }

    /**
     * Test the getter for the value.
     * @covers \tiny_fontcolor\color::get_value()
     */
    public function test_get_value(): void {
        $color = new color('Black', '#000000');
        $this->assertEquals('#000000', $color->get_value());

        $color = new color(' Black ', ' #000000 ');
        $this->assertEquals('#000000', $color->get_value());

        $color = new color('Black or White', 'ff0000');
        $this->assertEquals('#FF0000', $color->get_value());
    }

    /**
     * Test the color name validation.
     * @covers \tiny_fontcolor\color::has_name_error()
     */
    public function test_has_name_error(): void {
        $color = new color('Black', '#000000');
        $this->assertFalse($color->has_name_error());

        $color = new color(' Black ', '#000000');
        $this->assertFalse($color->has_name_error());

        $color = new color(' ', '#000000');
        $this->assertTrue($color->has_name_error());
    }

    /**
     * Test the color value validation.
     * @covers \tiny_fontcolor\color::has_value_error()
     */
    public function test_has_value_error(): void {
        $color = new color('Black', '#000000');
        $this->assertFalse($color->has_value_error());

        $color = new color('Black semi transparent', '#000000CC');
        $this->assertFalse($color->has_value_error());

        $color = new color('Black', '#g00000');
        $this->assertTrue($color->has_value_error());

        $color = new color('Black', '#0000');
        $this->assertTrue($color->has_value_error());
    }

    /**
     * Test the is_valid function.
     * @covers \tiny_fontcolor\color::is_valid()
     */
    public function test_is_valid(): void {
        $color = new color('Black', '#000000');
        $this->assertTrue($color->is_valid());

        $color = new color('Gray', 'fcfcfc00');
        $this->assertTrue($color->is_valid());

        $color = new color('Black', '#g00000');
        $this->assertFalse($color->is_valid());

        $color = new color('Black', '#0000');
        $this->assertFalse($color->is_valid());

        $color = new color('', '#000000');
        $this->assertFalse($color->is_valid());
    }
}
