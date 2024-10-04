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

namespace core_ai;

/**
 * Test ai image class methods.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\ai_image
 */
final class ai_image_test extends \advanced_testcase {

    /**
     * Test get_predominant_color.
     */
    public function test_get_predominant_color(): void {
        $x = 0;
        $y = 180;
        $width = 20;
        $height = 20;

        // Test for black image.
        $imagepath = self::get_fixture_path(__NAMESPACE__, 'black.png'); // Get the test image from the fixtures file.
        $image = new ai_image($imagepath);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($image, 'get_predominant_color');
        $color = $method->invoke($image, $x, $y, $width, $height);

        $this->assertEquals(0, $color['red']);
        $this->assertEquals(0, $color['green']);
        $this->assertEquals(0, $color['blue']);

        // Test for white image.
        $imagepath = self::get_fixture_path(__NAMESPACE__, 'white.png');
        $image = new ai_image($imagepath);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($image, 'get_predominant_color');
        $color = $method->invoke($image, $x, $y, $width, $height);

        $this->assertEquals(255, $color['red']);
        $this->assertEquals(255, $color['green']);
        $this->assertEquals(255, $color['blue']);

        // Test for grey image.
        $imagepath = self::get_fixture_path(__NAMESPACE__, 'grey.png');
        $image = new ai_image($imagepath);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($image, 'get_predominant_color');
        $color = $method->invoke($image, $x, $y, $width, $height);

        $this->assertEquals(128, $color['red']);
        $this->assertEquals(128, $color['green']);
        $this->assertEquals(128, $color['blue']);
    }

    /**
     * Test is_color_dark.
     */
    public function test_is_color_dark(): void {
        // Load an image as it is required for class instantiation.
        // The color of the image is not important for this test.
        $imagepath = self::get_fixture_path(__NAMESPACE__, 'black.png');
        $image = new ai_image($imagepath);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($image, 'is_color_dark');
        $color = ['red' => 0, 'green' => 0, 'blue' => 0];
        $result = $method->invoke($image, $color);

        $this->assertTrue($result);

        $image = new ai_image($imagepath);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($image, 'is_color_dark');
        $color = ['red' => 255, 'green' => 255, 'blue' => 255];
        $result = $method->invoke($image, $color);

        $this->assertFalse($result);

        $imagepath = self::get_fixture_path(__NAMESPACE__, 'grey.png');
        $image = new ai_image($imagepath);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($image, 'is_color_dark');
        $color = ['red' => 128, 'green' => 128, 'blue' => 128];
        $result = $method->invoke($image, $color);

        $this->assertTrue($result);
    }
}
